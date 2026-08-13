<?php
session_start();
 
// Se já estiver logado, redireciona para index.php
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}
 
// Exibe mensagens via GET?msg=1,2,3 etc
$msg = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case '2': $msg = 'Email ou senha inválidos.'; break;
        case '3': $msg = 'Por favor, preencha todos os campos.'; break;
        case '4': $msg = 'Erro interno, tente novamente mais tarde.'; break;
        case '5': $msg = 'Senha redefinida com sucesso! Faça login.'; break; // ✅ Novo caso
        case '6': $msg = 'Faça login para ter acesso.'; break; // ✅ Novo caso
    }
}
 ?>
 
 
 
 
 
<!DOCTYPE html>
<html lang="pt-br">
 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ReciMap</title>
    <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />

    <?php include("estrutura/import_css.php") ?>
   
   
 
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
            height: 400px;
        }
 
        .form:hover {
            transform: scale(1.05);
            border: 1px solid black;
        }
 
        #heading {
            text-align: center;
            margin: 1.5em;
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
            color: black;
            background-color: white;
            box-shadow: inset 2px 5px 10px rgb(5, 5, 5);
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
            color: black;
        }
 
        .form .btn {
            display: flex;
            justify-content: center;
            flex-direction: row;
            margin-top: 1em;
        }
 
        .button1 {
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
 
        .button1:hover {
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
 
        a{
            color: black;
        }
 
        a:hover{
            font-size: 17px;
        }
 
 
 
    </style>
</head>
 
<body>
 
    <div class="container d-flex justify-content-center align-items-center flex-column vh-100">
        <!-- From Uiverse.io by Praashoo7 -->
        <form class="form" method="post" action="login_process.php">
            <p id="heading">Login</p>
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
                </svg>
                <input autocomplete="off" placeholder="Email" class="input-field" type="email" name="email">
            </div>
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                </svg>
                <input placeholder="Senha" class="input-field" type="password" name="senha">
            </div>
            <div class="btn">
                <button type="submit" class="button1" name="btnlogin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
            </div>
            <h4 class="txt_cadastro"><a href="./esqueceu_senha.php">Esqueceu sua senha?</a></h4>
            <h4 class="txt_cadastro">Não possui login, clique aqui para se
                <a href="cadastro_usuario.php">Cadastrar</a>
            </h4>
           
 
 
        </form>
        <div class="msg d-flex justify-content-center pt-3" >
            <?php if(isset($_GET["msg"]) && $_GET["msg"] == 2) {?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Email ou senha incorretos!!</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
 
            <?php if(isset($_GET["msg"]) && $_GET["msg"] == 3) {?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>Preencha corretamente todos os campos!!</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>  
            </div>
            <?php } ?>
            <?php if(isset($_GET["msg"]) && $_GET["msg"] == 5) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Senha redefinida com sucesso!</strong> Faça login novamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <?php if(isset($_GET["msgCadastro"]) && $_GET["msgCadastro"] == 1) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Cadastro realizado com sucesso!</strong> Faça login.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <?php if(isset($_GET["msg"]) && $_GET["msg"] == 6) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Faça login para ter acesso!</strong> Faça login.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            
            <?php if(isset($_GET["logout"]) && $_GET["logout"] == 1) {?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Usuario desconectado com sucesso!!</strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>  
            </div>
            <?php } ?>
 
        </div>
    </div>
 
 
    </div>
 
 
 
 
 
 
<?php
include "estrutura/footer.php";
?>
 