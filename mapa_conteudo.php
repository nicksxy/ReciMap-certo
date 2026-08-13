<?php
    session_start();
 
    $id = $_SESSION["id_usuario"];
   
 
    include "conn.php";
    mysqli_set_charset($conn, "utf8");
 
    // ID do usuário logado na sessão
    $usuario_id_sessao = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : null;
 
    // Define o cabeçalho JSON para todos os endpoints AJAX
    if (isset($_GET['action']) || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']))) {
        header('Content-Type: application/json; charset=UTF-8');
    }
 
    // --------------------- ENDPOINTS AJAX ---------------------
 
    // 1. Buscar Avaliações (USANDO PREPARED STATEMENT)
    if (isset($_GET['action']) && $_GET['action'] === 'buscar_avaliacoes') {
        $ponto_id = intval($_GET['ponto_id'] ?? $_GET['id'] ?? 0);
        if ($ponto_id <= 0) { echo json_encode([]); exit; }
 
        // Prepared statement para prevenir Injeção de SQL
        $stmt = $conn->prepare("
            SELECT
                a.avaliacao AS nota,
                a.comentario,
                DATE_FORMAT(a.dt_hr_avaliacao, '%d/%m/%m %H:%i') AS data,
                u.apelido
            FROM tab_avaliacoes AS a
            LEFT JOIN tab_usuarios AS u ON a.usuario_id = u.id
            WHERE a.ponto_coleta_id = ?
            ORDER BY a.dt_hr_avaliacao DESC
        ");
        $stmt->bind_param("i", $ponto_id);
        $stmt->execute();
        $res = $stmt->get_result();
       
        $avaliacoes = [];
        while ($row = $res->fetch_assoc()) {
            $avaliacoes[] = $row;
        }
       
        $stmt->close();
        echo json_encode($avaliacoes, JSON_UNESCAPED_UNICODE);
        exit;
    }
 
    // 2. Obter Estatísticas (USANDO PREPARED STATEMENT)
    if (isset($_GET['action']) && $_GET['action'] === 'get_stats') {
        $ponto_id = intval($_GET['ponto_id'] ?? 0);
        if ($ponto_id <= 0) { echo json_encode(["media" => null, "total" => 0]); exit; }
 
        $stmt = $conn->prepare("SELECT AVG(avaliacao) AS media, COUNT(*) AS total FROM tab_avaliacoes WHERE ponto_coleta_id = ?");
        $stmt->bind_param("i", $ponto_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $row['media'] = $row['media'] !== null ? round(floatval($row['media']), 2) : null;
        $stmt->close();
        echo json_encode($row);
        exit;
    }
 
    // 3. Enviar Avaliação (POST) (USANDO PREPARED STATEMENT - APENAS LOGADOS)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enviar_avaliacao') {
        try {
            $ponto_id = intval($_POST['ponto_coleta_id'] ?? 0);
           
            // Usa SOMENTE o ID da sessão
            $usuario_id = $usuario_id_sessao;
           
            // ✨ VERIFICAÇÃO OBRIGATÓRIA DE LOGIN
            if ($usuario_id === null) {
                // Retorna um erro claro se não houver ID de usuário na sessão
                echo json_encode(["status" => "error", "message" => "Obrigatório fazer login para enviar uma avaliação."]);
                exit;
            }
 
            $avaliacao = intval($_POST['avaliacao'] ?? 0);
            $comentario = htmlspecialchars(trim($_POST['comentario'] ?? ''), ENT_QUOTES, 'UTF-8');
 
            if ($ponto_id <= 0 || $avaliacao < 1 || $avaliacao > 5) {
                echo json_encode(["status" => "error", "message" => "Dados de avaliação inválidos (Ponto ou Nota fora do intervalo 1-5)"]);
                exit;
            }
 
            // AGORA USA APENAS A QUERY PARA USUÁRIOS LOGADOS (iiis)
            $sql = "INSERT INTO tab_avaliacoes (usuario_id, ponto_coleta_id, avaliacao, comentario, dt_hr_avaliacao) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $usuario_id, $ponto_id, $avaliacao, $comentario);
 
            if ($stmt->execute()) {
                echo json_encode(["status" => "success"]);
            } else {
                // Esta mensagem é agora apenas para erros internos SQL, não mais por ID NULL.
                echo json_encode(["status" => "error", "message" => "Erro de execução da consulta: " . $stmt->error]);
            }
            $stmt->close();
            exit;
           
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>"Erro interno: ".$e->getMessage()]);
            exit;
        }
    }
 
    // --------------------- BUSCAR DADOS (Inicial) ---------------------
 
    // Busca de Materiais
    $sqlMateriais = "SELECT id, nome FROM tab_materiais ORDER BY nome";
    $resM = $conn->query($sqlMateriais);
    $materiais = [];
    if ($resM) {
        while ($r = $resM->fetch_assoc()) {
            $materiais[] = $r;
        }
    } else {
        error_log("Erro na consulta de materiais: " . $conn->error);
    }
 
    // Busca de Pontos de Coleta
    $sql = "SELECT
        c.id AS id_ponto, c.nome AS nome_ponto, c.endereco, c.telefone, c.horario_funcionamento AS horario,
        m.id AS id_material, m.nome AS nome_material, c.latitude, c.longitude
    FROM tab_ponto_coleta_material p
    JOIN tab_pontos_coleta c ON p.ponto_coleta_id = c.id
    JOIN tab_materiais m ON p.material_id = m.id
    ORDER BY c.nome";
 
    $res = $conn->query($sql);
    $locais = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $id = intval($row['id_ponto']);
            if (!isset($locais[$id])) {
                $locais[$id] = [
                    "id" => $id,
                    "nome" => $row['nome_ponto'],
                    "endereco" => $row['endereco'],
                    "telefone" => $row['telefone'],
                    "horario" => $row['horario'],
                    "latitude" => $row['latitude'],
                    "longitude" => $row['longitude'],
                    "materiais" => [],
                    "materiais_id" => []
                ];
            }
            $locais[$id]['materiais'][] = $row['nome_material'];
            $locais[$id]['materiais_id'][] = intval($row['id_material']);
        }
    } else {
        error_log("Erro na consulta de locais: " . $conn->error);
    }
 
    // Estatísticas iniciais de avaliações
    $avaliacoes_stats = [];
    $stmt = $conn->prepare("SELECT ponto_coleta_id, AVG(avaliacao) AS media, COUNT(*) AS total FROM tab_avaliacoes GROUP BY ponto_coleta_id");
    if ($stmt) {
        $stmt->execute();
        $resStats = $stmt->get_result();
        while ($r = $resStats->fetch_assoc()) {
            $avaliacoes_stats[intval($r['ponto_coleta_id'])] = [
                "media" => $r['media'] !== null ? round(floatval($r['media']), 2) : null,
                "total" => intval($r['total'])
            ];
        }
        $stmt->close();
    }
 
    $locais_array = array_values($locais);
    $locais_json = json_encode($locais_array, JSON_UNESCAPED_UNICODE);
    $materiais_json = json_encode($materiais, JSON_UNESCAPED_UNICODE);
    $avaliacoes_json = json_encode($avaliacoes_stats, JSON_UNESCAPED_UNICODE);
 
    ?>
 
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta charset="utf-8" />
    <title>Mapa Interativo - Recimap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css"/>
    <style>
 
    /* ==================== VARIÁVEIS E RESET ==================== */
    :root {
        --accent: #64a19d;
        --accent-dark: #4e817d;
        --muted: #6b7280;
        --bg-light: #f7fbff;
        --shadow: 0 8px 30px rgba(2,6,23,0.08);
        /* Cor unificada conforme sua solicitação */
        --solicitar-bg: var(--accent);
    }
 
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        background: var(--bs-body-bg);
    }
 
    .container {
        max-width: 1100px;
        margin: 14px auto;
        padding: 0 16px 40px;
    }
 
    /* ==================== MAPA ==================== */
    #map {
        height: 480px;
        width: 100%;
        max-width: 1200px;
        transition: height 0.35s ease;
        border-radius: 12px;
        margin: 10px auto 0;
        box-shadow: var(--shadow);
        background: linear-gradient(180deg, #e9f2ff 0%, #ffffff 100%);
    }
 
    #map.expanded {
        height: 745px;
    }
 
    #mapContainer {
        position: relative;
    }
 
    /* ==================== FILTRO ==================== */
    .filter-container,
    #selectContainer {
        position: static;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--accent);
        color: #fff;
        padding: 15px 20px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 1200;
        text-align: center;
        width: 350px;
        font-family: Arial, sans-serif;
    }
    #selectContainer {
    position: static;
    transform: none;
    box-shadow: none;
    padding: 8px 20px 15px;
    width: 250px;
}

 
      .filter-container label,
    #selectContainer label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 14px;
    }
 
    .filter-container select,
    #selectContainer select {
        width: 100%;
        padding: 8px 10px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }
    /* ==================== CONTROLES ==================== */
    #controls {
        display: flex;
        gap: 10px;
        margin: 10px auto;
        max-width: 600px;
        justify-content: center;
    }
 
    .control-btn {
        padding: 10px 15px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        color: white;
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        transition: background 0.2s;
    }
 
    .control-btn:hover {
        background: var(--accent-dark);
    }
 
    .control-btn svg {
        width: 16px;
        height: 16px;
        fill: white;
    }
 
    #infoBox {
        padding: 10px 15px;
        border-radius: 10px;
        background-color: var(--accent);
        color: white;
        font-weight: 700;
        text-align: center;
        margin: 10px auto;
        max-width: 600px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
 
    /* ROTA - ESCRITA RESPONSIVA E COMPACTA */
    #routeInfo {
        position: absolute;
        bottom: 14px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1300;
        background: #fff;
        padding: 10px 14px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(2,6,23,0.12);
        font-weight: 700;
        display: none;
        /* AJUSTES DE RESPONSIVIDADE */
        max-width: 90%;
        box-sizing: border-box;
        text-align: center;
        font-size: 14px;
        line-height: 1.4;
    }
 
    #routeInfo .badge {
        /* Faz os badges serem compactos e responsivos */
        display: inline-block;
        padding: 3px 6px;
        font-size: 12px;
        margin: 2px 2px;
        white-space: nowrap;
    }
 
 
    /* ==================== FORMULÁRIOS ==================== */
    .forms-wrapper {
        display: flex;
        flex-direction: column; /* PADRÃO: Empilhado em telas pequenas (Celular) */
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin: 30px auto;
        max-width: 1100px;
        width: 100%;
    }
 
    #formContainer,
    #solicitarContainer {
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 24px rgba(2,6,23,0.06);
        width: 100%;
        max-width: 480px;
        color: #fff;
        background-color: var(--accent);
    }
 
    #solicitarContainer {
        background-color: var(--solicitar-bg);
    }
 
    #formContainer h2,
    #solicitarContainer h2 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 20px;
        color: white;
    }
 
    #formContainer label,
    #solicitarContainer label {
        display: block;
        margin-top: 10px;
        font-weight: 700;
        color: #fff;
        font-size: 14px;
    }
 
    #formContainer input,
    #formContainer select,
    #formContainer textarea,
    #solicitarContainer input,
    #solicitarContainer select,
    #solicitarContainer textarea {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.3);
        font-size: 15px;
        box-sizing: border-box;
    }
 
    #formContainer button,
    #solicitarContainer button {
        margin-top: 15px;
        padding: 12px 16px;
        border: none;
        border-radius: 10px;
        background: #155d53;
        color: white;
        font-size: 16px;
        cursor: pointer;
        font-weight: 700;
        width: 100%;
        transition: opacity 0.2s;
    }
 
    #formContainer button:disabled,
    #solicitarContainer button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
 
    #formContainer .msg {
        margin-top: 10px;
        font-weight: 700;
        color: #fff;
        text-align: center;
    }
 
    #solicitarContainer .success {
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
        font-weight: normal;
    }
    .custom-select-wrapper {
    position: relative;
    width: 100%;
}
 
.custom-select {
    background: #ffffff;
    color: black;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    user-select: none;
}
.custom-select .arrow {
    font-size: 12px;
}
.custom-select ul.options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #fff;
    max-height: 150px;
    overflow-y: auto;
    display: none;
    margin: 0;
    padding: 0;
    list-style: none;
    z-index: 100;
}
.custom-select ul.options li {
    padding: 8px 12px;
    cursor: pointer;
    color: #d4edda
}
.custom-select ul.options li:hover {
    background: #f0f0f0;
}
.custom-select.open ul.options {
    display: block;
}
    .custom-select ul.options li {
    color: #333333; /* cor do texto das opções */
}
 
.custom-select ul.options li:hover {
    background: #f0f0f0;
    color: #155d53; /* cor ao passar o mouse */
}
 
 
    /* LADO A LADO EM DESKTOP ( >= 768px) */
    @media (min-width: 768px) {
        .forms-wrapper {
            flex-direction: row; /* CHAVE: Caixas lado a lado */
            align-items: flex-start;
            justify-content: center;
            gap: 40px;
        }
 
        #formContainer {
            width: max-content; /* Caixa 1 (Avaliação) menor, ajustada ao conteúdo */
            min-width: 300px;
            max-width: none;
        }
       
        #solicitarContainer {
            flex-grow: 1; /* Caixa 2 (Solicitação) ocupa o espaço restante */
            min-width: 350px;
            max-width: none;
        }
    }
 
    /* ==================== POPUP LEAFLET FORÇADO RESPONSIVO ==================== */
 
    /* AJUSTE LEAFLET: Força a largura do wrapper do popup */
 
 
    .leaflet-popup-content {
        /* Garante que o conteúdo (o nosso .popup-card) siga a largura do wrapper */
       
        padding: 0 !important; /* Remove o padding padrão do Leaflet */
        margin: 9 !important; /* Remove margens extras */
    }
 
    /* Garante que a ponta do balão não fique fora da tela */
    .leaflet-popup-tip-container {
        width: 100% !important;
    }
 
    /* NOSSO CARD (JÁ RESPONSIVO) */
    .popup-card {
        width: 300px;
        height:400px;
        min-width:280px;
        max-width: 100%; /* Ajustado para 100% já que o wrapper define o limite */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
        font-family: 'Segoe UI', Arial, sans-serif;
        transition: all 0.2s ease;
    }
 
    /* AJUSTES NO POPUP PARA DIMINUIR A ALTURA E APROVEITAR MELHOR O ESPAÇO */
    .popup-header {
        padding: 8px 10px; /* Reduz o padding superior e inferior */
    }
    .popup-body {
        padding: 8px 10px 10px; /* Reduz o padding em geral */
        font-size: 14px;
    }
    .popup-title {
        font-weight: 800;
        font-size: 14px; /* Diminui o tamanho do título */
        margin: 0;
        color: #0f172a;
        word-wrap: break-word;
    }
    .popup-sub {
        font-size: 11px; /* Diminui o subtítulo (endereço) */
        color: var(--muted);
        margin-top: 2px;
    }
    .stars {
        font-size: 16px; /* Diminui o tamanho das estrelas/média */
    }
    .total-avaliacoes {
        font-size: 12px;
    }
 
 
    /* Footer e Botões */
    .popup-footer {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px; /* Reduz o padding no rodapé */
        background: var(--bg-light);
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        justify-content: center;
    }
 
    /* Botões do popup */
    .btn-route,
    .btn-avaliar,
    .btn-ver-avaliacoes {
        background-color: var(--accent) !important;
        color: #fff !important;
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
        transition: background-color 0.2s;
        flex: 1 1 30%; /* Tenta 3 por linha, mas permite que cresçam/diminuam */
        min-width: 80px;
        max-width: 100%;
        text-align: center;
    }
 
    .btn-route:hover,
    .btn-avaliar:hover,
    .btn-ver-avaliacoes:hover {
        background-color: var(--accent-dark) !important;
    }
 
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        background: #155d53;
        color: white;
    }
    #materiaisSelect {
    width: 100%;
    height: 35px; /* altura da linha */
    padding: 4px 8px;
    font-size: 14px;
    border-radius: 5px;
}
 
    /* ==================== RESPONSIVIDADE GERAL ==================== */
    @media (max-width: 900px) {
        .filter-container {
            width: 300px;
            padding: 12px 15px;
        }
        .filter-container select {
            font-size: 13px;
            padding: 6px 8px;
        }

    }
 
    @media (max-width: 600px) {
        .filter-container {
            width: 80%;
            padding: 8px 10px;
            font-size: 13px;
            border-radius: 10px;
            top: 10px;
        }
        .filter-container label {
            font-size: 12px;
        }
        .filter-container select {
            font-size: 12px;
            padding: 5px 6px;
        }
 
        /* Rota mais compacta no celular */
        #routeInfo {
            font-size: 12px;
            padding: 6px 10px;
        }
 
        .popup-card {
            /* max-width já é controlado pelo wrapper agora */
            font-size: 13px;
            width:100px;
            height:350px
        }
        .popup-header, .popup-body, .popup-footer {
            padding: 6px 8px; /* Mais compacto ainda */
        }
        .popup-title { font-size: 13px; }
        .stars { font-size: 15px; }
        .btn-route, .btn-avaliar, .btn-ver-avaliacoes { padding: 4px 7px; font-size: 10px; }
    }
 
    @media (max-width: 400px) {
        .popup-card { font-size: 12px; }
        .btn-route, .btn-avaliar, .btn-ver-avaliacoes { font-size: 9px; padding: 3px 5px; }
        .filter-container { width: 90%; padding: 6px 8px; font-size: 12px; }
        .filter-container select { padding: 4px 5px; font-size: 11px; }
    }
 
    /* ==================== MODAL DE AVALIAÇÕES ==================== */
    #avaliacoesModal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6);
    }
 
    #avaliacoesModal .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
 
    #avaliacoesModal .close {
        color: var(--accent);
        float: right;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }
 
    #avaliacoesModal h2 {
        text-align: center;
        color: var(--accent);
        margin-bottom: 10px;
    }
 
    #listaAvaliacoes {
        max-height: 300px;
        overflow-y: auto;
        padding: 0 10px;
    }
 
    .avaliacao {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
 
    .avaliacao:last-child {
        border-bottom: none;
    }
 
    .avaliacao strong {
        color: #333;
    }
 
    .avaliacao span {
       font-weight: 700;
    }
    </style>
    </head>
    <body>

    <input type="hidden" name="materiais" id="materiaisInput">
    <div id="avaliacoesModal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Avaliações</h2>
            <div id="listaAvaliacoes"></div>
        </div>
    </div>
 
    <div class="container">
        <div id="mapContainer">
            <div id="map"></div>
          
            <div id="routeInfo" aria-live="polite"></div>
        </div>
<div id="controls">
    <div id="selectContainer" class="filter-inline">
        <label for="filtroMaterial">Filtrar Material:</label>
        <select id="filtroMaterial">
            <option value="0">Todos</option>
        </select>
    </div>
 
    <button id="clearRouteBtn" class="control-btn">
        <svg viewBox="0 0 24 24">
            <path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.18 12 2.88 5.71 4.29 4.29 10.59 10.59 16.88 4.29z"/>
        </svg>
        Limpar rota
    </button>
</div>
 
 
        <div id="infoBox">Selecione um ponto no mapa para ver informações e avaliações.</div>
       
        <div class="forms-wrapper">
           
            <div id="formContainer">
                <h2>Avaliar ponto de coleta</h2>
                <input type="hidden" id="usuario_id" value="<?php echo $usuario_id_sessao ?? ''; ?>">
       
                <label for="ponto_coleta_id">Ponto de coleta:</label>
                <select id="ponto_coleta_id" aria-required="true">
                    <option value="">Selecione...</option>
                </select>
       
                <label for="avaliacao">Avaliação (1 a 5):</label>
                <select id="avaliacao" aria-required="true">
                    <option value="1">1 ⭐</option>
                    <option value="2">2 ⭐</option>
                    <option value="3">3 ⭐</option>
                    <option value="4">4 ⭐</option>
                    <option value="5">5 ⭐</option>
                </select>
       
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" rows="3" placeholder="Escreva um comentário..."></textarea>
       
                <button id="enviarAvaliacao">Enviar avaliação</button>
                <div class="msg" id="msgForm" aria-live="polite"></div>
            </div>
 
            <div id="solicitarContainer">
                <h2 style="text-align:center;">Solicitar novo ponto de coleta</h2>
               
                <?php if(isset($_GET['msg']) && $_GET['msg']==='success'): ?>
                <p class="success">✅ Solicitação enviada! Aguarde aprovação do administrador.</p>
                <?php endif; ?>
               
             <form action="solicitar_ponto.php" method="POST">
                    <label>Nome do ponto:</label>
                    <input type="text" name="nome" required>
 
                    <label>CEP:</label>
                    <input type="text" id="cep" name="cep" placeholder="00000-000" required>
 
                    <label>Número:</label>
                    <input type="text" id="numero" name="numero" placeholder="Número" required>
 
 
                    <label>Endereço:</label>
                    <input type="text" name="endereco" id="endereco" required>
 
                    <label>Telefone:</label>
                    <input type="text" name="telefone">
                   
                    <label>Horário de funcionamento:</label>
                    <input type="text" name="horario">
 
                    <label>Latitude:</label>
                    <input type="text" name="latitude" id="latitude" required>
 
                    <label>Longitude:</label>
                    <input type="text" name="longitude" id="longitude" required>
 
                   
                   
                        <div id="materiaisContainer" role="region" aria-label="Materiais aceitos">
                        <label for="materiaisSelect"><strong>Materiais aceitos:</strong></label>
                        <div class="custom-select-wrapper">
                            <div id="customPonto">
                                <select class="options" name="material">
                                    <?php
                                    $queryy = "SELECT id, nome FROM tab_materiais ORDER BY nome ASC";
                                    $result = mysqli_query($conn, $queryy);
                                    while($linha = mysqli_fetch_assoc($result)){
                                    ?>
                                    <option value="<?= $linha["id"]; ?>"> <?= $linha["nome"]; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                       
           
 
    <!-- valor enviado ao PHP -->
    <input type="hidden" name="materiais" id="materiaisInput">
</div>
 
                   
                    <button type="submit">Enviar solicitação</button>
                </form>
            </div>
        </div>
    </div>
 
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
 
    <script>
        
    const locais = <?php echo $locais_json; ?>;
    const materiais = <?php echo $materiais_json; ?>;
    let avaliacoes_stats = <?php echo $avaliacoes_json; ?>;


    function colorFromId(id) {
    const colors = {
        1: '#ff4d4f', // vermelho
        2: '#40a9ff', // azul
        3: '#73d13d', // verde
        4: '#ffa940', // laranja
        5: '#9254de', // roxo
        6: '#f759ab', // rosa
        7: '#13c2c2', // ciano
        8: '#faad14', // amarelo
        9: '#595959', // cinza
        10:'#ff85c0'  // rosa claro
    };
    return colors[id] || '#64a19d'; // cor padrão
}


 
    // Configuração inicial do mapa
    const map = L.map('map').setView([-23.5505, -46.6333], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
 
    const markers = [];

    async function atualizarPontos() {
  try {
    const res = await fetch('get_pontos.php');
    const novosLocais = await res.json();
 
    // Remove os marcadores antigos do mapa
    markers.forEach(m => map.removeLayer(m));
    markers.length = 0;
 
    // Adiciona os novos marcadores
    novosLocais.forEach(local => {
      const firstMatId = local.materiais_id.length ? local.materiais_id[0] : 1;
      const color = colorFromId(firstMatId);
      const marker = L.marker([local.latitude, local.longitude], { icon: createMaterialIcon(color) }).addTo(map);
      marker.localId = local.id;
      marker.localObj = local;
      marker.bindPopup(criarPopup(local));
      markers.push(marker);
 
      marker.on('popupopen', function() {
        const popupDiv = marker.getPopup().getElement();
        const btnAvaliar = popupDiv.querySelector('.btn-avaliar');
        const btnVer = popupDiv.querySelector('.btn-ver-avaliacoes');
        const btnRoute = popupDiv.querySelector('.btn-route');
 
        btnAvaliar.onclick = e => {
          e.preventDefault();
          document.getElementById('ponto_coleta_id').value = marker.localId;
          window.scrollTo({ top: document.querySelector('.forms-wrapper').offsetTop - 10, behavior: 'smooth' });
        };
 
        btnVer.onclick = e => {
          e.preventDefault();
          verAvaliacoes(marker.localId, marker.localObj.nome);
        };
 
        btnRoute.onclick = e => {
          e.preventDefault();
          if (routeControl) { map.removeControl(routeControl); routeControl = null; }
          const from = userLatLng ? L.latLng(userLatLng.lat, userLatLng.lng) : map.getCenter();
          routeControl = L.Routing.control({
            waypoints: [from, L.latLng(marker.getLatLng())],
            lineOptions: { styles: [{color: '#64a19d', weight: 5}] },
            show: false, addWaypoints: false, draggableWaypoints: false, fitSelectedRoutes: true,
            router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1' })
          }).on('routesfound', e => {
            const route = e.routes[0];
            const km = (route.summary.totalDistance / 1000).toFixed(2);
            const min = Math.round(route.summary.totalTime / 60);
            routeInfo.style.display = 'block';
            routeInfo.innerHTML = `Rota até ${local.nome}: ${km} km • ${min} min`;
          }).addTo(map);
        };
      });
    });
 
    console.log('🔄 Pontos atualizados no mapa!');
  } catch (err) {
    console.error('Erro ao atualizar pontos:', err);
  }
}
 

    const filtroSelect = document.getElementById('filtroMaterial');
    const pontoSelect = document.getElementById('ponto_coleta_id');

    const clearRouteBtn = document.getElementById('clearRouteBtn');
    const infoBox = document.getElementById('infoBox');
    const routeInfo = document.getElementById('routeInfo');
    const enviarAvaliacaoBtn = document.getElementById('enviarAvaliacao');
    const msgForm = document.getElementById('msgForm');
    let routeControl = null;
    let userMarker = null;
    let userLatLng = null;

 
// =================================================================
// 🚀 LÓGICA JAVASCRIPT PARA O SELECT CUSTOMIZADO
// Isso deve ser adicionado ao seu arquivo de script principal.
// =================================================================

// const customSelect = document.getElementById('customPonto');
// const selectedSpan = customSelect.querySelector('.selected');
// const optionsList = customSelect.querySelector('.options');
// const hiddenInput = document.getElementById('materiaisInput');

// let selectedValues = [];

// // 1. Toggles (Abre/Fecha) o dropdown.
// customSelect.addEventListener('click', (e) => {
//     // Apenas abre/fecha se o clique não for dentro de um item da lista (li)
//     if (!e.target.closest('.options li')) {
//         customSelect.classList.toggle('open');
//     }
// });

// // 2. Lógica de seleção dos itens.
// optionsList.querySelectorAll('li').forEach(option => {
//     option.addEventListener('click', (e) => {
//         // MUITO IMPORTANTE: Impede que o evento do item propague e feche o customSelect
//         e.stopPropagation(); 
        
//         const value = option.getAttribute('data-value');
        
//         // Adiciona/Remove o valor do array e a classe visual
//         if (!selectedValues.includes(value)) {
//             selectedValues.push(value);
//             option.classList.add('selected-custom'); // Adiciona classe para destaque visual
//         } else {
//             selectedValues = selectedValues.filter(v => v !== value);
//             option.classList.remove('selected-custom'); // Remove classe visual
//         }
        
//         // Atualiza o texto exibido no seletor
//         if (selectedValues.length === 0) {
//             selectedSpan.textContent = 'Selecione os materiais';
//         } else {
//             // Mapeia os valores selecionados de volta para o texto exibido
//             selectedSpan.textContent = selectedValues
//                 .map(v => optionsList.querySelector(`li[data-value="${v}"]`).textContent)
//                 .join(', ');
//         }
        
//         // CHAVE: Atualiza o valor do input escondido para o PHP (formato: "1,5,7")
//         hiddenInput.value = selectedValues.join(',');

//         // Mantém o dropdown aberto após a seleção
//         if (!customSelect.classList.contains('open')) {
//              customSelect.classList.add('open');
//         }
//     });
// });

// // 3. Listener para fechar o dropdown se o usuário clicar fora dele
// document.addEventListener('click', (e) => {
//     if (!customSelect.contains(e.target)) {
//         customSelect.classList.remove('open');
//     }
// });
 
// // Fecha o dropdown se clicar fora
// document.addEventListener('click', (e) => {
//     if (!customSelect.contains(e.target)) {
//         customSelect.classList.remove('open');
//     }
// });
 
 
    // Cria ícone de pino (DivIcon) com a imagem que você enviou como base
    function createMaterialIcon(color) {
        const iconSize = 28; // Tamanho do ícone
       
        return L.divIcon({
            className: 'material-marker-new', // Nova classe para este ícone
            html: `<div style="
                width: ${iconSize}px;
                height: ${iconSize}px;
                background-color: ${color};
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                border: 2px solid #fff;
            ">
                <div style="
                    width: 10px;
                    height: 10px;
                    background-color: #fff;
                    border-radius: 50%;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(45deg);
                "></div>
            </div>`,
            iconSize: [iconSize, iconSize],
            iconAnchor: [iconSize/2, iconSize]
        });
    }
 
    // Popular select de materiais
    materiais.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.nome;
        filtroSelect.appendChild(opt);
    });
 
    // Função para criar popup HTML
    function criarPopup(local) {
        const stat = avaliacoes_stats[local.id] || {media:null,total:0};
        const media = stat.media !== null ? stat.media : 'N/A';
        const total = stat.total;
        const popup = document.createElement('div');
        popup.className = 'popup-card';
        popup.innerHTML = `
            <div class="popup-header">
                <div class="popup-icon" aria-hidden="true">📍</div>
                <div>
                    <p class="popup-title">${local.nome}</p>
                    <p class="popup-sub">${local.endereco}</p>
                </div>
            </div>
            <div class="popup-body">
                <p><strong>Materiais:</strong> ${local.materiais.join(', ')}</p>
                <p class="stars" role="img" aria-label="Média de avaliação de ${media} de 5">⭐ Média: ${media} / 5</p>
                <p class="total-avaliacoes">Total avaliações: ${total}</p>
            </div>
            <div class="popup-footer">
                <a href="#" class="btn-route" role="button">Traçar rota</a>
                <a href="#" class="btn-avaliar" role="button">Avaliar</a>
                <a href="#" class="btn-ver-avaliacoes" role="button">Ver avaliações</a>
            </div>
        `;
        return popup;
    }
 
    // Criação de markers e binding de popups
    locais.forEach(local => {
        const firstMatId = local.materiais_id.length ? local.materiais_id[0] : 1;
        const color = colorFromId(firstMatId);
        const marker = L.marker([local.latitude, local.longitude], { icon: createMaterialIcon(color) }).addTo(map);
        marker.localId = local.id;
        marker.localObj = local;
        marker.bindPopup(criarPopup(local));
        markers.push(marker);
 
        marker.on('popupopen', function() {
            const popupDiv = marker.getPopup().getElement();
            const btnRoute = popupDiv.querySelector('.btn-route');
            const btnAvaliar = popupDiv.querySelector('.btn-avaliar');
            const btnVer = popupDiv.querySelector('.btn-ver-avaliacoes');
 
            btnRoute.onclick = (e) => {
                e.preventDefault();
                if (routeControl) { map.removeControl(routeControl); routeControl = null; }
                const from = userLatLng ? L.latLng(userLatLng.lat, userLatLng.lng) : map.getCenter();
                routeControl = L.Routing.control({
                    waypoints: [
                        from,
                        L.latLng(marker.getLatLng())
                    ],
                    lineOptions: { styles: [{color: '#64a19d', weight: 5}] },
                    show: false,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    fitSelectedRoutes: true,
                    router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1' })
                }).on('routesfound', function(e) {
                    const route = e.routes[0];
                    const distance_km = (route.summary.totalDistance/1000).toFixed(2);
                    const duration_min = Math.round(route.summary.totalTime/60);
                    const walk_min = Math.round((route.summary.totalDistance/1000) / 5 * 60);
                    routeInfo.style.display = 'block';
                    routeInfo.innerHTML = ` Distância: <span class="badge">${distance_km} km</span> • Carro: <span class="badge">${duration_min} min</span> • A pé (estim.): <span class="badge">${walk_min} min</span>`;
                }).addTo(map);
            };
 
            btnAvaliar.onclick = (e) => {
                e.preventDefault();
                document.getElementById('ponto_coleta_id').value = marker.localId;
                // Scroll para o wrapper dos forms
                window.scrollTo({ top: document.querySelector('.forms-wrapper').offsetTop - 10, behavior: 'smooth' });
                document.getElementById('avaliacao').focus();
            };
 
            btnVer.onclick = (e) => {
                e.preventDefault();
                verAvaliacoes(marker.localId, marker.localObj.nome); // Passa o nome para o modal
            };
        });
    });
 
    // Evento de filtro
    filtroSelect.addEventListener('change', function(){
        const val = parseInt(this.value);
        markers.forEach(m => {
            const local = locais.find(l=>l.id===m.localId);
            if (val === 0 || local.materiais_id.includes(val)) m.addTo(map);
            else map.removeLayer(m);
        });
    });
 
    // Popular select de pontos do formulário
    locais.forEach(l => {
        const opt = document.createElement('option');
        opt.value = l.id;
        opt.textContent = l.nome;
        pontoSelect.appendChild(opt);
    });
 

 
    // Limpar rota
    clearRouteBtn.addEventListener('click', () => {
        if (routeControl) { map.removeControl(routeControl); routeControl = null; routeInfo.style.display = 'none'; }
    });
 
    // Enviar avaliação (Melhorado com feedback visual e reset)
    enviarAvaliacaoBtn.addEventListener('click', async ()=>{
        enviarAvaliacaoBtn.disabled = true; // Desabilita o botão
        msgForm.textContent = 'Enviando...';
       
        const ponto_coleta_id = document.getElementById('ponto_coleta_id').value;
        const avaliacao = document.getElementById('avaliacao').value;
        const comentario = document.getElementById('comentario').value;
 
        if(!ponto_coleta_id || !avaliacao) {
            msgForm.textContent='Selecione o ponto e a avaliação (1 a 5).';
            enviarAvaliacaoBtn.disabled = false;
            return;
        }
 
        const formData = new FormData();
        formData.append('action','enviar_avaliacao');
        formData.append('ponto_coleta_id', ponto_coleta_id);
        formData.append('avaliacao', avaliacao);
        formData.append('comentario', comentario);
 
        try {
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
 
            if(data.status==='success'){
                msgForm.style.color = 'lime';
                msgForm.textContent = '🎉 Avaliação registrada com sucesso!';
                document.getElementById('comentario').value='';
               
                // Lógica de atualização local de estatísticas
                const id = ponto_coleta_id;
                avaliacoes_stats[id] = avaliacoes_stats[id] || {media:0,total:0};
                const stat = avaliacoes_stats[id];
                stat.total +=1;
                // Recálculo da média
                const newAvg = ((parseFloat(stat.media)*(stat.total-1)+parseFloat(avaliacao))/stat.total);
                stat.media = newAvg.toFixed(2);
               
                // Atualizar popup se estiver aberto
                const marker = markers.find(m=>m.localId==id);
                if(marker && marker.isPopupOpen()){
                    marker.getPopup().setContent(criarPopup(marker.localObj));
                }
 
            } else {
                msgForm.style.color = 'red';
                msgForm.textContent='Erro ao registrar avaliação: '+(data.message||'Tente novamente.');
            }
        } catch (e) {
            msgForm.style.color = 'red';
            msgForm.textContent='Erro de comunicação com o servidor. Verifique sua conexão.';
        } finally {
            enviarAvaliacaoBtn.disabled = false;
            setTimeout(() => { msgForm.textContent = ''; msgForm.style.color = 'white'; }, 6000);
        }
    });
 
 
   
    // ------------------ Localização do usuário ------------------
 
    if (navigator.geolocation) {
        // Usa watchPosition para pegar a localização e manter atualizada
        navigator.geolocation.watchPosition(pos => {
            const newLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude);
           
            // ÍCONE DE PINO PARA O USUÁRIO (cor fixa: #155d53)
            const userIconHtml = `<div style="
                width: 20px; height: 20px; border-radius: 50%;
                background: #155d53; border: 3px solid #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                position: relative;
            ">
                <div style="
                    width: 8px; height: 8px; border-radius: 50%;
                    background: #fff;
                    position: absolute; top: 50%; left: 50%;
                    transform: translate(-50%, -50%);
                "></div>
            </div>`;
 
            // Inicializa o marcador na primeira vez
            if (!userMarker) {
                userLatLng = newLatLng; // Define a posição inicial
                userMarker = L.marker([userLatLng.lat, userLatLng.lng], {
                    icon: L.divIcon({
                        html: userIconHtml,
                        className: '',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map).bindPopup("Você está aqui").openPopup();
                map.setView(newLatLng, 13); // Centraliza o mapa apenas na primeira vez
            } else {
                // Apenas atualiza a posição em tempo real
                userMarker.setLatLng(newLatLng);
                userLatLng = newLatLng;
            }
        }, err => {
            console.warn('Geolocation error:', err);
            if (err.code === 1) {
                infoBox.textContent = '📍 Localização negada. Traçar rota usará o centro do mapa.';
            } else {
                infoBox.textContent = '📍 Não foi possível obter sua localização.';
            }
        }, { enableHighAccuracy: true, maximumAge: 2000, timeout: 10000 });
    } else {
        infoBox.textContent = '📍 Seu navegador não suporta geolocalização.';
    }
 
 
    // ------------------ Funções do Modal ------------------
 
    function fecharModal() {
        document.getElementById("avaliacoesModal").style.display = "none";
    }
 
    window.onclick = function(event) {
        const modal = document.getElementById("avaliacoesModal");
        if (event.target === modal) fecharModal();
    };
 
    // ======== VER AVALIAÇÕES ========
    function verAvaliacoes(pontoId, nomePonto) {
        const modal = document.getElementById("avaliacoesModal");
        const lista = document.getElementById("listaAvaliacoes");
        const tituloModal = modal.querySelector('h2');
 
        tituloModal.textContent = `Avaliações de: ${nomePonto}`;
        modal.style.display = "block";
        lista.innerHTML = "<p style='text-align:center;'>Carregando avaliações...</p>";
 
        fetch(`?action=buscar_avaliacoes&ponto_id=${pontoId}`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    lista.innerHTML = "<p style='text-align:center;'>Nenhuma avaliação encontrada.</p>";
                } else {
                    lista.innerHTML = data.map(av => {
                        const corNota = av.nota >= 4 ? 'green' : (av.nota <= 2 ? 'red' : '#555');
                        return `
                            <div class="avaliacao" role="listitem">
                                <strong>${av.apelido ? av.apelido : "Usuário anônimo"}</strong>
                                <span style="float:right; color:${corNota}">${av.nota}⭐</span>
                                <p style="margin:4px 0; color:#333;">${av.comentario ? av.comentario : "<em>Sem comentário</em>"}</p>
                                <small style="color:#777;">${av.data}</small>
                            </div>
                        `;
                    }).join("");
                }
            })
            .catch(() => {
                lista.innerHTML = "<p style='text-align:center; color:red;'>Erro ao carregar avaliações.</p>";
            });
    }
    const solicitarForm = document.querySelector('form[action="solicitar_ponto.php"]');
if (solicitarForm) {
    solicitarForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const nome = solicitarForm.querySelector('input[name="nome"]').value.trim();
    const cep = solicitarForm.querySelector('input[name="cep"]').value.trim();
    const numero = solicitarForm.querySelector('input[name="numero"]').value.trim();
    const endereco = solicitarForm.querySelector('input[name="endereco"]').value.trim();
    const horario = solicitarForm.querySelector('input[name="horario"]').value.trim();
    const latitude = solicitarForm.querySelector('input[name="latitude"]').value.trim();
    const longitude = solicitarForm.querySelector('input[name="longitude"]').value.trim();
    const materiais = document.getElementById('materiaisInput').value.trim();
    const telefone = solicitarForm.querySelector('input[name="telefone"]').value.trim(); // opcional

    // Remove espaços e verifica
function isEmpty(val) {
    return !val || val.trim() === '';
}

// No submit:
if (
    isEmpty(nome) ||
    isEmpty(cep) ||
    isEmpty(numero) ||
    isEmpty(endereco) ||
    isEmpty(horario) ||
    isEmpty(latitude) ||
    isEmpty(longitude)
) {
    alert('⚠️ Preencha todos os campos obrigatórios antes de enviar.\n(O telefone é opcional)');
    return;
}


    // 🚀 Envio
    const formData = new FormData(solicitarForm);

    fetch('solicitar_ponto.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        const msg = document.createElement('p');
        msg.textContent = '🎉 Obrigado pela solicitação! Ela foi enviada para análise.';
        msg.style.cssText = `
            color: limegreen;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            opacity: 0;
            transition: opacity 0.6s ease;
        `;
        solicitarForm.after(msg);
        setTimeout(() => msg.style.opacity = '1', 100);

        solicitarForm.reset();
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';

        setTimeout(() => msg.remove(), 6000);
    })
    .catch(() => {
        alert('❌ Ocorreu um erro ao enviar sua solicitação. Tente novamente.');
    });
});

           
}
    setInterval(atualizarPontos, 30000); // Atualiza a cada 30 segundos









// Inputs do formulário
const cepInput = document.getElementById('cep');
const numeroInput = document.getElementById('numero');
const enderecoInput = document.getElementById('endereco');
const nomeInput = document.querySelector('input[name="nome"]');
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const infoBoxs = document.getElementById('infoBox');

/**
 * Função principal para buscar latitude e longitude
 * Funciona tanto para CEP+Número quanto para nome de ponto
 */
async function buscarLatLng() {

    const cep = cepInput.value.replace(/\D/g, '');
    const numero = numeroInput.value.trim();

    if (cep.length !== 8) return;

    try {
        // 1️⃣ Buscar dados do CEP para preencher endereço
        const resCep = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const dataCep = await resCep.json();

        console.log(dataCep)

        if (!dataCep.erro) {

            const logradouro = dataCep.logradouro || '';
            const bairro = dataCep.bairro || '';
            const localidade = dataCep.localidade || '';
            const uf = dataCep.uf || '';

            // Preenche automaticamente o campo endereço
            enderecoInput.value = `${logradouro}, ${bairro}, ${localidade} - ${uf}`;
        }

        // 2️⃣ Só busca lat/lng se tiver número também
        if (numero) {

            const resGeo = await fetch('/ReciMap-certo/geocode.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    cep: cep,
                    numero: numero
                })
            });

            const geoData = await resGeo.json();

            console.log(geoData)
            if (geoData.lat && geoData.lng) {
                latInput.value = geoData.lat;
                lngInput.value = geoData.lng;
                infoBoxs.textContent = '✅ Localização encontrada!';
            } else {
                latInput.value = '';
                lngInput.value = '';
                infoBoxs.textContent = geoData.error || '❌ Número não encontrado, posição aproximada.';
                alert("Cep não encontrado")
            }
        }

    } catch (e) {
        console.error('Erro:', e);
        infoBoxs.textContent = '❌ Erro ao buscar dados.';
    }
}



/**
 * Função debounce para evitar requisições excessivas
 */
function debounce(func, wait = 800) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

const buscarDebounced = debounce(buscarLatLng, 800);

// numero.addEventListener("blur",()=>{
//    buscarLatLng()
// })

// Eventos
cepInput.addEventListener('input', buscarDebounced);
numeroInput.addEventListener('input', buscarDebounced);
enderecoInput.addEventListener('input', buscarDebounced);
nomeInput.addEventListener('input', buscarDebounced);


    </script>
 
    </body>