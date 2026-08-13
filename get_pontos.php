<?php
include "conn.php";
mysqli_set_charset($conn, "utf8");

$sql = "
    SELECT 
        c.id AS id_ponto,
        c.nome AS nome_ponto,
        c.endereco,
        c.telefone,
        c.horario_funcionamento AS horario,
        c.latitude,
        c.longitude,
        m.id AS id_material,
        m.nome AS nome_material
    FROM tab_ponto_coleta_material p
    JOIN tab_pontos_coleta c ON p.ponto_coleta_id = c.id
    JOIN tab_materiais m ON p.material_id = m.id
    ORDER BY c.nome
";

$res = $conn->query($sql);

$locais = [];
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

$conn->close();
echo json_encode(array_values($locais), JSON_UNESCAPED_UNICODE);
?>
