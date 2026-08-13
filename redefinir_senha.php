<?php
require_once 'conn.php';
 
$token = $_GET['token'] ?? '';
$msg = $_GET['msg'] ?? '';
 
// Verifica token
$sql = "SELECT id FROM tab_usuarios WHERE token_recuperacao = ? AND expira_token > NOW() LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
 
if (mysqli_stmt_num_rows($stmt) === 0) {
    $token_invalido = true;
} else {
    $token_invalido = false;
}
?>
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - ReciMap</title>
    <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
    <?php include("estrutura/import_css.php") ?>
</head>
 
    <style>
        body {
            background-image: url("./IMG/fundo_login.webp");
            background-repeat: no-repeat;
            background-size: cover;
            backdrop-filter: blur(5px);
 
        }
 
        /* From Uiverse.io by Praashoo7 */
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-left: 2em;
            padding-right: 2em;
            padding-bottom: 0.4em;
            background-color: rgb(100, 161, 157);
            border-radius: 25px;
            transition: .4s ease-in-out;
            height: 300px;
        }
 
        .form:hover {
            transform: scale(1.05);
            border: 1px solid black;
        }
 
        #heading {
            text-align: center;
            margin-top: 2rem;
            font-size: 24px;
        }
 
        .field {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5em;
            border-radius: 25px;
            padding: 0.6em;
            border: none;
            outline: none;
            color: white;
            background-color: white;
            box-shadow: inset 2px 5px 10px rgb(5, 5, 5);
            margin-top: 10px;
        }
 
        .input-icon {
            height: 1.3em;
            width: 1.3em;
            fill: black;
        }
 
        .input-field {
            background: none;
            border: none;
            outline: none;
            width: 100%;
            color: #252525;
        }
 
        .form .btn {
            display: flex;
            justify-content: center;
            flex-direction: row;
            margin-top: 1em;
        }
 
        .btnsalvar {
            padding: 0.5em;
            padding-left: 1.1em;
            padding-right: 1.1em;
            border-radius: 5px;
            margin-right: 0.5em;
            border: none;
            outline: none;
            transition: .4s ease-in-out;
            background-color: #252525;
            color: white;
        }
 
        .btnsalvar:hover {
            background-color: black;
            color: white;
        }
 
 
        .txt_cadastro{
            font-size: 16px;
            color: black;
        }
 
        #heading{
            color: black;
        }
 
 
 
 
    </style>
 
 
<body>
<div class="container d-flex justify-content-center align-items-center flex-column vh-100">
 
<?php if($token_invalido): ?>
    <div class="alert alert-danger">Link inválido ou expirado. Solicite um novo link.</div>
    <a href="esqueceu_senha.php" class="btn btn-danger mt-3">Voltar</a>
<?php else: ?>
    <form class="form" method="post" action="atualizar_senha.php">
        <p id="heading">Nova Senha</p>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
 
        <div class="field">
            <input type="password" name="senha1" class="input-field" placeholder="Nova senha" required>
        </div>
        <div class="field">
            <input type="password" name="senha2" class="input-field" placeholder="Repita a senha" required>
        </div>
 
        <?php if($msg === 'senhas_nao_conferem'): ?>
            <div class="alert alert-warning mt-2">As senhas não conferem.</div>
        <?php endif; ?>
 
        <div class="txt_cadastro d-flex ms-2 gap-3 m-0 p-0">
            <a href="login.php" class="btn btn-danger">Voltar</a>
            <button type="submit" class="btn btn-dark">Salvar nova senha</button>
        </div>
    </form>
<?php endif; ?>
 
</div>
 
<?php include "estrutura/footer.php"; ?>
 
 
 
 
 
 