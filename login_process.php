<?php
session_start();

require_once 'conn.php'; // $conn mysqli procedural
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnlogin'])) {
 
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : null;
 
    if ($email && $senha !== null && $senha !== '') {
        // Busca id, email, senha e nivel
        $sql = "SELECT id, email, senha, nivel FROM tab_usuarios WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
 
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
 
            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $id, $email_db, $senha_db, $nivel_db);
                mysqli_stmt_fetch($stmt);
 
                // Verifica senha com hash seguro
                if (password_verify($senha, $senha_db)) {
 
                    session_regenerate_id(true);
                    $_SESSION['id_usuario'] = $id;
                    $_SESSION['email'] = $email_db;
                    $_SESSION['nivel'] = $nivel_db; // salva admin ou usuario
 
                    mysqli_stmt_close($stmt);
 
                    // Redireciona com base no tipo de usuário
                    if ($nivel_db === 'admin') {
                        header('Location: index.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    // Senha incorreta
                    mysqli_stmt_close($stmt);
                    header('Location: login.php?msg=2');
                    exit;
                }
            } else {
                // Nenhum usuário encontrado
                mysqli_stmt_close($stmt);
                header('Location: login.php?msg=2');
                exit;
            }
        } else {
            // Erro no prepare
            header('Location: login.php?msg=4');
            exit;
        }
    } else {
        // Campos vazios
        header('Location: login.php?msg=3');
        exit;
    }
} else {
    // Acesso direto sem POST
    header('Location: login.php');
    exit;
}
?>
 
 