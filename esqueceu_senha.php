<?php
session_start();
$msg = '';
 
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case '1': $msg = 'Um link de redefinição foi enviado para seu e-mail.'; break;
        case '2': $msg = 'E-mail não encontrado.'; break;
        case '3': $msg = 'Erro interno, tente novamente mais tarde.'; break;
    }
}
?>
 
 
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Esqueceu a Senha - ReciMap</title>
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
            height: 280px;
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
 
        .btncadastro {
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
 
        .btncadastro:hover {
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
    <form class="form d-flex" method="post" action="enviar_token.php">
        <p id="heading">Recuperar Senha</p>
        <div class="field">
            <input type="email" class="input-field" name="email" placeholder="Digite seu e-mail" required>
        </div>
        <h4 class="txt_cadastro d-flex mt-3 ms-2 gap-3 m-0 p-0">
            <a href="login.php" class="btn btn-danger">Voltar</a>
            <button type="submit" class="btnlink btn btn-dark" name="btnlink">Enviar link</button>
        </h4>
    </form>
 
    <?php if($msg != ''): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
</div>
 
 
 
 
<?php
include "estrutura/footer.php";
?>
 