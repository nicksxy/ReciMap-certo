<?php
include "conn.php";
 
// talvez definir charset
header('Content-Type: application/json; charset=utf-8');
 
$sql = "SELECT u.apelido, MAX(r.pontuacao) as pontuacao
        FROM ranking r
        JOIN tab_usuarios u ON u.id = r.id_usuario
        GROUP BY r.id_usuario
        ORDER BY pontuacao DESC
        LIMIT 20";
 
$result = $conn->query($sql);
$ranking = [];
 
while ($row = $result->fetch_assoc()) {
    $ranking[] = [
        "apelido" => $row["apelido"],
        "pontuacao" => $row["pontuacao"]
    ];
}
 
echo json_encode($ranking);
exit;
 
 