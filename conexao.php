<?php

/* =========================
   CONFIGURAÇÃO DO BANCO
========================= */

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "vitrine_digital";


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