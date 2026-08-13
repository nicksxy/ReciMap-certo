<?php
session_start();

require_once 'conn.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $senha1 = trim($_POST['senha1'] ?? '');
    $senha2 = trim($_POST['senha2'] ?? '');
 
    if ($senha1 === '' || $senha2 === '' || $senha1 !== $senha2) {
        die("As senhas não conferem.");
    }
 
    $sql = "SELECT id FROM tab_usuarios WHERE token_recuperacao = ? AND expira_token > NOW() LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
 
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $id);
        mysqli_stmt_fetch($stmt);
 
        // ⚠️ Se quiser compatibilizar com seu login atual, troque password_hash() por texto puro (não recomendado)
      
        $novaSenha = password_hash($senha1, PASSWORD_DEFAULT);
 
        $update = "UPDATE tab_usuarios
                   SET senha = ?, token_recuperacao = NULL, expira_token = NULL
                   WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt2, "si", $novaSenha, $id);
        mysqli_stmt_execute($stmt2);
 
        header("Location: login.php?msg=5"); // senha redefinida
        exit;
    } else {
        die("Token inválido ou expirado.");
    }
}
?>
 
 