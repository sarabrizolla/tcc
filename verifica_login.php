<?php

/* =========================
   CONFIGURAÇÃO DA SESSÃO
========================= */

// Compatível com versões mais antigas do PHP
ini_set('session.use_strict_mode', '1');

session_set_cookie_params(
    0,
    '/',
    '',
    false,
    true
);


/* =========================
   INICIA A SESSÃO
========================= */

session_start();


/* =========================
   VERIFICA LOGIN
========================= */

if (
    !isset($_SESSION['loja_id']) ||
    !is_numeric($_SESSION['loja_id']) ||
    (int) $_SESSION['loja_id'] <= 0
) {

    session_unset();
    session_destroy();

    header("Location: ../login.php");
    exit;

}


/* =========================
   GARANTE ID COMO INTEIRO
========================= */

$_SESSION['loja_id'] = (int) $_SESSION['loja_id'];

?>