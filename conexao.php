<?php

/* =========================
   CONFIGURAÇÃO DO BANCO
========================= */

$servidor = "localhost";
$usuario = "u948351601_vitrinedigital";
$senha = "CEEDOu948351601";
$banco = "u948351601_vitrinedigital";


/* =========================
   CONEXÃO
========================= */

$conn = mysqli_connect(
    $servidor,
    $usuario,
    $senha,
    $banco
);


/* =========================
   VERIFICA CONEXÃO
========================= */

if (!$conn) {

    die("Não foi possível conectar ao banco de dados.");

}


/* =========================
   CONFIGURA CHARSET
========================= */

if (!mysqli_set_charset($conn, "utf8mb4")) {

    die("Não foi possível configurar a conexão com o banco de dados.");

}

?>