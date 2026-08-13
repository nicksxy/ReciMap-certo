<?php

session_start();
 
// Verifica se o usuário está logado

if (!isset($_SESSION['id'])) {

    header("Location: login.php");

    exit;

}
 
// Verifica se é administrador

function isAdmin() {

    return isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'admin';

}
 
// Verifica se é usuário comum

function isUsuario() {

    return isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'usuario';

}
 
// Exemplo de proteção de página

function protegerPaginaAdmin() {

    if (!isAdmin()) {

        die("Acesso negado: apenas administradores.");

    }

}

?>


 

 