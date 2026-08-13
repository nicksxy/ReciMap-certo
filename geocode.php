<?php
header('Content-Type: application/json; charset=utf-8');
 
$data = json_decode(file_get_contents("php://input"), true);
 
$cep = preg_replace('/\D/', '', $data['cep'] ?? '');
$numero = intval($data['numero'] ?? 0);
 
if (!$cep || !$numero) {
    echo json_encode(['lat'=>null,'lng'=>null,'error'=>'CEP ou número vazio']);
    exit;
}
 
/* ========================
   ViaCEP
======================== */
function buscarViaCep($cep){
    $res = file_get_contents("https://viacep.com.br/ws/$cep/json/");
    return json_decode($res, true);
}
 
/* ========================
   Busca rua no OSM
======================== */
function buscarRua($q){
    $params = [
        'format' => 'json',
        'limit' => 1,
        'countrycodes' => 'br',
        'polygon_geojson' => 1,
        'q' => $q
    ];
 
    $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
 
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: ReciMap/1.0 (contato@seudominio.com)'
        ],
        CURLOPT_TIMEOUT => 10
    ]);
 
    $res = curl_exec($ch);
    curl_close($ch);
 
    return json_decode($res, true);
}
 
/* ========================
   Interpolação na linha
======================== */
function interpolarNaLinha($coordinates, $numero){
 
    // Limite máximo considerado
    $maxNumero = 1000;
    $numero = min($numero, $maxNumero);
    $proporcao = $numero / $maxNumero;
 
    // Calcula comprimento total da linha
    $totalDist = 0;
    $segmentos = [];
 
    for($i=0; $i<count($coordinates)-1; $i++){
        $a = $coordinates[$i];
        $b = $coordinates[$i+1];
 
        $dist = sqrt(
            pow($b[0]-$a[0],2) +
            pow($b[1]-$a[1],2)
        );
 
        $segmentos[] = ['a'=>$a,'b'=>$b,'dist'=>$dist];
        $totalDist += $dist;
    }
 
    $alvo = $totalDist * $proporcao;
    $percorrido = 0;
 
    foreach($segmentos as $seg){
        if($percorrido + $seg['dist'] >= $alvo){
            $resto = $alvo - $percorrido;
            $ratio = $resto / $seg['dist'];
 
            $lon = $seg['a'][0] + ($seg['b'][0]-$seg['a'][0]) * $ratio;
            $lat = $seg['a'][1] + ($seg['b'][1]-$seg['a'][1]) * $ratio;
 
            // pequeno deslocamento lateral
            $offset = 0.00005;
            $lat += $offset;
 
            return [$lat, $lon];
        }
        $percorrido += $seg['dist'];
    }
 
    return null;
}
 
/* ========================
   EXECUÇÃO
======================== */
 
$via = buscarViaCep($cep);
 
if(isset($via['erro'])){
    echo json_encode(['lat'=>null,'lng'=>null,'error'=>'CEP inválido']);
    exit;
}
 
$logradouro = $via['logradouro'];
$cidade = $via['localidade'];
$uf = $via['uf'];
 
$busca = buscarRua("$logradouro, $cidade - $uf");
 
if(empty($busca[0])){
    echo json_encode(['lat'=>null,'lng'=>null,'error'=>'Rua não encontrada']);
    exit;
}
 
$rua = $busca[0];
 
// Se existir número exato
if(isset($rua['address']['house_number'])){
    echo json_encode([
        'lat'=>$rua['lat'],
        'lng'=>$rua['lon'],
        'aproximado'=>false
    ]);
    exit;
}
 
// Se tiver geometria
if(isset($rua['geojson']['coordinates'])){
 
    $coords = $rua['geojson']['coordinates'];
 
    // Caso seja LineString
    if($rua['geojson']['type'] === "LineString"){
        $ponto = interpolarNaLinha($coords, $numero);
    }
 
    // Caso seja MultiLineString
    if($rua['geojson']['type'] === "MultiLineString"){
        $ponto = interpolarNaLinha($coords[0], $numero);
    }
 
    if($ponto){
        echo json_encode([
            'lat'=>$ponto[0],
            'lng'=>$ponto[1],
            'aproximado'=>true
        ]);
        exit;
    }
}
 
// fallback final
echo json_encode([
    'lat'=>$rua['lat'],
    'lng'=>$rua['lon'],
    'aproximado'=>true
]);