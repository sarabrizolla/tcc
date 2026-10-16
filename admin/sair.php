<?php

// INICIAR A SESSÃO
session_start();

// LIMPAR TODAS AS VARIÁVEIS DA SESSÃO
$_SESSION = array();

// EXCLUIR O COOKIE DA SESSÃO, SE ELE EXISTIR
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// DESTRUIR A SESSÃO
session_destroy();

// VOLTAR PARA A PÁGINA DE LOGIN
header("Location: ../login.php");
exit;

?>