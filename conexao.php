<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "vitrine_digital";

$conn = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");