<?php
session_start();
 
 
include "conn.php";
header('Content-Type: application/json; charset=utf-8');
 
if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    echo json_encode(["erro" => "Não autorizado."]);
    exit;
}
 
$data = json_decode(file_get_contents("php://input"), true);
 
 
 
$score = isset($data['score']) ? intval($data['score']) : 0;
 
if (!is_numeric($score)) {
    http_response_code(400);
    echo json_encode(["erro" => "Pontuação ausente ou inválida."]);
    exit;
}
 
 
$id_usuario = $_SESSION["id_usuario"];
 
// Verifique se conexão está OK
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["erro" => "Falha na conexão: " . $conn->connect_error]);
    exit;
}
 
$stmt = $conn->prepare("INSERT INTO ranking (id_usuario, pontuacao, data_jogo) VALUES (?, ?, NOW())");
 
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro na preparação da query: " . $conn->error]);
    exit;
}
 
$stmt->bind_param("ii", $id_usuario, $score);
 
if ($stmt->execute()) {
    echo json_encode(["sucesso" => true]);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao executar: " . $stmt->error]);
}
 
$stmt->close();
$conn->close();
?>
 
 
 