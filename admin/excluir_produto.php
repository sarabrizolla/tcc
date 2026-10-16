<?php

include '../conexao.php';
include '../verifica_login.php';


// VERIFICAR SE O ID FOI INFORMADO

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {

    $_SESSION['mensagem_excluir_produto'] =
        "Não foi possível excluir o produto.";

    header("Location: produtos.php");
    exit;
}


$id = (int) $_POST['id'];

$loja_id = (int) $_SESSION['loja_id'];


// VERIFICAR TOKEN CSRF

if (!isset($_POST['csrf_token'])) {

    $_SESSION['mensagem_excluir_produto'] =
        "Não foi possível excluir o produto. Tente novamente.";

    header("Location: produtos.php");
    exit;
}


$csrf_token = $_POST['csrf_token'];


if (
    empty($_SESSION['csrf_excluir_produto']) ||
    !hash_equals(
        $_SESSION['csrf_excluir_produto'],
        $csrf_token
    )
) {

    $_SESSION['mensagem_excluir_produto'] =
        "Não foi possível excluir o produto. Tente novamente.";

    header("Location: produtos.php");
    exit;
}


// EXCLUIR SOMENTE O PRODUTO DA LOJA LOGADA

$sql = "DELETE FROM produtos
        WHERE id = ?
        AND loja_id = ?";


$stmt = mysqli_prepare($conn, $sql);


if (!$stmt) {

    $_SESSION['mensagem_excluir_produto'] =
        "Não foi possível excluir o produto. Tente novamente.";

    header("Location: produtos.php");
    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $loja_id
);


// EXECUTAR EXCLUSÃO

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        $_SESSION['mensagem_excluir_produto'] =
            "Produto excluído com sucesso.";

    } else {

        $_SESSION['mensagem_excluir_produto'] =
            "Produto não encontrado ou não pertence à sua loja.";
    }

} else {

    $_SESSION['mensagem_excluir_produto'] =
        "Não foi possível excluir o produto. Tente novamente.";
}


mysqli_stmt_close($stmt);


// GERAR NOVO TOKEN CSRF

try {

    $_SESSION['csrf_excluir_produto'] =
        bin2hex(random_bytes(32));

} catch (Exception $e) {

    $_SESSION['csrf_excluir_produto'] =
        bin2hex(openssl_random_pseudo_bytes(32));
}


// VOLTAR PARA A LISTA DE PRODUTOS

header("Location: produtos.php");
exit;

?>