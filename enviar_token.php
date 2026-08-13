<?php
require_once 'conn.php';
require_once 'phpToMail.php'; // Certifique-se de ter o phpToMail atualizado com PHPMailer
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida o e-mail recebido do formulário
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
 
    if (!$email) {
        header("Location: esqueceu_senha.php?msg=3"); // ❌ E-mail inválido
        exit;
    }
 
    // Verifica se o e-mail existe no banco
    $sql = "SELECT id FROM tab_usuarios WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
 
    if (mysqli_stmt_num_rows($stmt) === 0) {
        header("Location: esqueceu_senha.php?msg=2"); // ⚠️ E-mail não encontrado
        exit;
    }
 
    // Gera token aleatório e define tempo de expiração (1 hora)
    $token = bin2hex(random_bytes(50));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
 
    // Atualiza banco com token e tempo de expiração
    $update = "UPDATE tab_usuarios
               SET token_recuperacao = ?, expira_token = ?
               WHERE email = ?";
    $stmt2 = mysqli_prepare($conn, $update);
    mysqli_stmt_bind_param($stmt2, "sss", $token, $expira, $email);
    mysqli_stmt_execute($stmt2);
 
    // Define URL base para localhost (ajuste a porta se necessário)
    $baseURL = "http://localhost/ReciMap"; // se usar XAMPP, ajuste porta: "http://localhost:8080"
    $link = $baseURL . "/redefinir_senha.php?token=" . urlencode($token);
 
    // Assunto e corpo do e-mail
    $assunto = "Recuperação de senha - ReciMap";
    $mensagem = "Olá!\n\nClique no link abaixo para redefinir sua senha:\n\n$link\n\n"
              . "Este link expira em 1 hora.\n\nSe você não solicitou isso, ignore este e-mail.";
    
    //$mail->CharSet = 'UTF-8';

    // Envia e-mail usando PHPMailer
    if (phpToMail($email, $assunto, $mensagem)) {
        header("Location: esqueceu_senha.php?msg=1"); // ✅ Sucesso
    } else {
        header("Location: esqueceu_senha.php?msg=3"); // ❌ Erro no envio
    }
    exit;
} else {
    header("Location: esqueceu_senha.php"); // Redireciona se acessar sem POST
    exit;
}
 
 