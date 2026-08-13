<?php
include "conn.php";

$arquivos = ['mensagens.json', 'historico.json'];

foreach ($arquivos as $file) {
    if (!file_exists($file)) {
        echo "<p>⚠️ Arquivo <strong>$file</strong> não encontrado.</p>";
        continue;
    }

    $dados = json_decode(file_get_contents($file), true);

    if (!is_array($dados)) {
        echo "<p>❌ Erro ao ler <strong>$file</strong> (formato inválido).</p>";
        continue;
    }

    foreach ($dados as $ponto) {

        // Se não tiver nome ou id, pula
        if (empty($ponto['id']) || empty($ponto['nome'])) {
            continue;
        }

        // Verifica se o ponto já existe
        $check = $conn->prepare("SELECT COUNT(*) FROM tab_pontos_coleta WHERE id = ?");
        $check->bind_param("s", $ponto['id']);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count == 0) {
            // Insere o ponto principal
            $stmt = $conn->prepare("
                INSERT INTO tab_pontos_coleta 
                (id, nome, endereco, latitude, longitude, telefone, horario_funcionamento, usuario_id, apelido, email, status, data_criacao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssssssssssss",
                $ponto['id'],
                $ponto['nome'],
                $ponto['endereco'],
                $ponto['latitude'],
                $ponto['longitude'],
                $ponto['telefone'],
                $ponto['horario'],
                $ponto['usuario_id'],
                $ponto['apelido'],
                $ponto['email'],
                $ponto['status'],
                $ponto['data']
            );

            $stmt->execute();
            $stmt->close();

            echo "<p>✅ Ponto importado: <strong>{$ponto['nome']}</strong></p>";

            // ----- Agora insere os materiais relacionados -----
            if (!empty($ponto['materiais'])) {
                $materiais = is_array($ponto['materiais'])
                    ? $ponto['materiais']
                    : explode(',', $ponto['materiais']);

                foreach ($materiais as $nomeMat) {
                    $nomeMat = trim($nomeMat);
                    if ($nomeMat === '') continue;

                    // Verifica se o material existe na tabela tab_materiais
                    $mat_id = null;
                    $stmtMat = $conn->prepare("SELECT id FROM tab_materiais WHERE nome = ?");
                    $stmtMat->bind_param("s", $nomeMat);
                    $stmtMat->execute();
                    $stmtMat->bind_result($mat_id);
                    $stmtMat->fetch();
                    $stmtMat->close();

                    // Se não existir, cria
                    if (!$mat_id) {
                        $stmtIns = $conn->prepare("INSERT INTO tab_materiais (nome) VALUES (?)");
                        $stmtIns->bind_param("s", $nomeMat);
                        $stmtIns->execute();
                        $mat_id = $stmtIns->insert_id;
                        $stmtIns->close();
                    }

                    // Liga o ponto ao material
                    $stmtLink = $conn->prepare("
                        INSERT IGNORE INTO tab_ponto_coleta_material (ponto_coleta_id, material_id)
                        VALUES (?, ?)
                    ");
                    $stmtLink->bind_param("si", $ponto['id'], $mat_id);
                    $stmtLink->execute();
                    $stmtLink->close();
                }
            }

        } else {
            echo "<p>⚠️ Já existia: <strong>{$ponto['nome']}</strong> ({$ponto['id']})</p>";
        }
    }
}

$conn->close();

echo "<hr><p><strong>✅ Importação concluída com sucesso!</strong></p>";
?>
