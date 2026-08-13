<?php
// conn.php
$host = "localhost";
$user = "root";
$pass = ""; // ajuste se necessário
$db   = "recimap";
 
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");