<?php
session_start();
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: login.php?msg=6');
    exit;
}
include "conn.php";
 
$msg = "";
 
if (isset($_POST["btncadastro"])) {
    $apelido = !empty($_POST["apelido"]) ? trim($_POST["apelido"]) : null;
    $email = !empty($_POST["email"]) ? trim($_POST["email"]) : null;
    $senha = !empty($_POST["senha"]) ? $_POST["senha"] : null;
    $checksenha = !empty($_POST["confirmarsenha"]) ? $_POST["confirmarsenha"] : null;
    $nivel = 2; // 1 = usuário comum, 2 = admin (ou outros valores que você definir)
 
    // Valida todos os campos
    if ($apelido && $email && $senha && $checksenha) {
 
        // Valida email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Digite um email válido!";
        } elseif ($senha !== $checksenha) {
            $msg = "As senhas não coincidem!";
        } else {
            // Criptografa a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
 
            // Preparando o insert com o campo nível
            $stmt = $conn->prepare("INSERT INTO tab_usuarios(apelido, email, senha, nivel) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $apelido, $email, $senhaHash, $nivel);
 
 
            if ($stmt->execute()) {
                header('Location: administradores.php?msgCadastro=1'); // Cadastro realizado com sucesso
                exit;
            } else {
                $msg = "Este email já possui um cadastro!";
            }
        }
    } else {
        $msg = "Preencha todos os campos!";
    }
}
?>
 
<!DOCTYPE html>
<html lang="pt-br">
 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ReciMap - Cadastro Adm</title>
    <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
    <?php include("estrutura/import_css.php"); ?>
    <style>
        body {
            background-image: url("./IMG/fundo_login.webp");
            background-repeat: no-repeat;
            background-size: cover;
            backdrop-filter: blur(5px);
        }
 
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 2em;
            background-color: rgb(100, 161, 157);
            border-radius: 25px;
            transition: 0.4s ease-in-out;
            max-width: 400px;
            width: 100%;
        }
 
        .form:hover {
            transform: scale(1.05);
            border: 1px solid black;
        }
 
        #heading {
            text-align: center;
            font-size: 24px;
            color: black;
            margin-bottom: 1rem;
        }
 
        .field {
            display: flex;
            align-items: center;
            gap: 0.5em;
            border-radius: 25px;
            padding: 0.6em;
            background-color: white;
            box-shadow: inset 2px 5px 10px rgb(5, 5, 5);
        }
 
        .input-icon {
            height: 1.3em;
            width: 1.3em;
            fill: black;
        }
 
        .input-field {
            border: none;
            outline: none;
            width: 100%;
            color: #252525;
            background: none;
        }
 
        .btn {
            display: flex;
            justify-content: center;
            gap: 0.5em;
            margin-top: 1em;
        }
 
        .btncadastro {
            padding: 0.5em 1.2em;
            border-radius: 5px;
            border: none;
            background-color: #252525;
            color: white;
            transition: 0.4s ease-in-out;
        }
 
        .btncadastro:hover {
            background-color: black;
        }
 
        .msg {
            margin-top: 1rem;
        }
    </style>
</head>
 
<body>
 
    <div class="container d-flex justify-content-center align-items-center flex-column vh-100">
        <form class="form" method="post" autocomplete="off">
            <p id="heading">Cadastre outro administrador</p>
 
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4-3a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM14 14s-1-4-6-4-6 4-6 4 2.5 2 6 2 6-2 6-2z" />
                </svg>
                <input placeholder="Apelido" class="input-field" type="text" name="apelido"
                    value="<?= htmlspecialchars($apelido ?? '') ?>">
            </div>
 
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z">
                    </path>
                </svg>
                <input placeholder="Email" class="input-field" type="email" name="email"
                    value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
 
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z">
                    </path>
                </svg>
                <input placeholder="Senha" class="input-field" type="password" name="senha">
            </div>
 
            <div class="field">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z">
                    </path>
                </svg>
                <input placeholder="Confirmar senha" class="input-field" type="password" name="confirmarsenha">
            </div>
 
            <div class="btn">
                <a href="administradores.php" class="btn btn-danger">Voltar</a>
                <button type="submit" class="btncadastro btn btn-dark" name="btncadastro">Cadastrar</button>
            </div>
 
           
        </form>
        <?php if ($msg) : ?>
            <div class="msg alert alert-warning alert-dismissible fade show" role="alert">
                <strong><?= htmlspecialchars($msg) ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
    </div>
 
    <?php include "estrutura/footer.php"; ?>
</body>
 
</html>
 
 