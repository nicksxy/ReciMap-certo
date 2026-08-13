<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION["id_usuario"])) {
    header('Location: login.php?erro=4');
    exit;
}

// Permite apenas administradores
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: login.php?msg=6');
    exit;
}

// Conexão com o banco
include "conn.php";

// Arquivos JSON
$file = 'mensagens.json';
$historico_file = 'historico.json';
$log_file = 'debug_log.txt'; // arquivo de log para depuração

// Função auxiliar para log
function log_debug($msg)
{
    global $log_file;
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Carrega os arquivos JSON
$mensagens = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$historico = file_exists($historico_file) ? json_decode(file_get_contents($historico_file), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $acao = $_POST['acao'] ?? null;

    if (!$id || !$acao) {
        header('Location: administradores.php?msg=erro');
        exit;
    }

    foreach ($mensagens as $index => $msg) {
        if ($msg['id'] === $id) {

            if ($acao === 'aceitar') {
                $msg['status'] = 'aceito';
                $mensagem_alerta = "Solicitação aprovada com sucesso!";

                // 1️⃣ Insere o ponto de coleta no banco
                $stmt = $conn->prepare(
                    "INSERT INTO tab_pontos_coleta 
                    (nome, endereco, latitude, longitude, telefone, horario_funcionamento)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                if (!$stmt) {
                    log_debug("Erro no prepare do ponto de coleta: " . $conn->error);
                    header('Location: administradores.php?msg=erro');
                    exit;
                }

                // Garantir que todos os campos existem
                $nome = $msg['nome'] ?? '';
                $endereco = $msg['endereco'] ?? '';
                $latitude = $msg['latitude'] ?? '';
                $longitude = $msg['longitude'] ?? '';
                $telefone = $msg['telefone'] ?? '';
                $horario = $msg['horario_funcionamento'] ?? '';

                $stmt->bind_param("ssssss", $nome, $endereco, $latitude, $longitude, $telefone, $horario);


                if (!$stmt->execute()) {
                    log_debug("Erro ao inserir ponto de coleta: " . $stmt->error . " | Dados: " . json_encode($msg));
                    header('Location: administradores.php?msg=erro');
                    exit;
                }


                $ponto_id = $stmt->insert_id;
                $material_id = $_POST["material"];
                $sql = "INSERT INTO tab_ponto_coleta_material (ponto_coleta_id, material_id) VALUES ('$ponto_id','$material_id')";
                if (mysqli_query($conn, $sql)) {
                    header('location:administradores.php');
                }
            } elseif ($acao === 'negar') {
                $msg['status'] = 'negado';
                $mensagem_alerta = "Solicitação negada com sucesso!";
            }

            // 3️⃣ Move a mensagem para o histórico
            $msg['data_processamento'] = date('Y-m-d H:i:s');
            $historico[] = $msg;
            unset($mensagens[$index]);

            // 4️⃣ Salva os arquivos JSON
            file_put_contents($file, json_encode(array_values($mensagens), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($historico_file, json_encode($historico, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 5️⃣ Fecha conexão e redireciona
            $conn->close();
            header("Location: administradores.php?msg=" . urlencode($mensagem_alerta));
            exit;
        }

        // Caso o ID não seja encontrado
        // log_debug("ID $id não encontrado nas mensagens.");
        header('Location: administradores.php?msg=erro');
        // exit;
    }
}
