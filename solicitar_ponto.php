<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header('Location: login.php?erro=4');
    exit;
}
include "conn.php";
 
// Permite apenas usuários logados
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php?msg=6");
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_ponto = [
        'id' => uniqid(), // 👈 Adiciona um identificador único
        'nome' => $_POST['nome'],
        'endereco' => $_POST['endereco'],
        'telefone' => $_POST['telefone'],
        'horario' => $_POST['horario'],
        'material' => $_POST['material'],
        'latitude' => $_POST['latitude'],
        'longitude' => $_POST['longitude'],
        'usuario_id' => $_SESSION['id_usuario'],
        'apelido' => $_SESSION['apelido'] ?? 'Usuário',
        'email' => $_SESSION['email'] ?? '',
        'status' => 'pendente',
        'data' => date('Y-m-d H:i:s')
    ];
 
    $file = 'mensagens.json';
    $mensagens = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $mensagens[] = $novo_ponto;
    file_put_contents($file, json_encode($mensagens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
 
    header("Location: mapa_conteudo.php?msg=success");
    exit;
}
?>
 
 
 