<?php
// chat.php

header('Content-Type: application/json');

// Substitua pela sua chave da OpenAI
$OPENAI_API_KEY = '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';

// Recebe dados do POST
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['mensagem']) || empty($input['mensagem'])) {
    echo json_encode(['error' => 'Mensagem não enviada']);
    exit;
}

$mensagem = $input['mensagem'];

// Configuração da requisição para OpenAI
$ch = curl_init('https://api.openai.com/v1/chat/completions');

$data = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'Você é a Recimind, assistente de reciclagem que ajuda usuários a descartar resíduos corretamente.'
        ],
        [
            'role' => 'user',
            'content' => $mensagem
        ]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
];

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Executa a requisição
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => "Erro ao conectar com OpenAI: HTTP $http_code"]);
    exit;
}

// Decodifica resposta da OpenAI
$resposta_data = json_decode($response, true);
$resposta_text = $resposta_data['choices'][0]['message']['content'] ?? 'Sem resposta';

echo json_encode(['resposta' => $resposta_text]);
