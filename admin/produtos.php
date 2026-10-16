<?php

include '../conexao.php';
include '../verifica_login.php';

$loja_id = (int) $_SESSION['loja_id'];


// CRIAR TOKEN CSRF PARA EXCLUSÃO

if (!isset($_SESSION['csrf_excluir_produto'])) {

    try {

        $_SESSION['csrf_excluir_produto'] = bin2hex(random_bytes(32));

    } catch (Exception $e) {

        $_SESSION['csrf_excluir_produto'] =
            bin2hex(openssl_random_pseudo_bytes(32));

    }

}


// BUSCAR MENSAGEM DE EXCLUSÃO

$mensagem = "";

if (isset($_SESSION['mensagem_excluir_produto'])) {

    $mensagem = $_SESSION['mensagem_excluir_produto'];

    unset($_SESSION['mensagem_excluir_produto']);

}


// BUSCAR PRODUTOS DA LOJA LOGADA

$sql = "SELECT id, nome, descricao, preco, imagem
        FROM produtos
        WHERE loja_id = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

$resultado = false;

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $loja_id);

    if (mysqli_stmt_execute($stmt)) {

        $resultado = mysqli_stmt_get_result($stmt);

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include '../includes_site/head.php'; ?>

    <title>Meus Produtos - Vitrine Digital</title>

</head>

<body>

<?php include '../includes_site/header.php'; ?>


<section class="py-5">

    <div class="container">


        <!-- MENSAGEM -->

        <?php if ($mensagem != "") { ?>

            <div class="alert alert-info alert-dismissible fade show shadow-sm mb-4" role="alert">

                <?php echo htmlspecialchars($mensagem); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fechar">
                </button>

            </div>

        <?php } ?>


        <!-- CABEÇALHO -->

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 mb-5">

            <div>

                <span class="badge bg-primary px-3 py-2 mb-2">
                    Área administrativa
                </span>

                <h1 class="fw-bold mb-2">
                    Meus Produtos
                </h1>

                <p class="text-muted mb-0">
                    Gerencie os produtos da sua loja.
                </p>

            </div>


            <div class="d-flex flex-column flex-sm-row gap-2">

                <a
                    href="cadastrar_produto.php"
                    class="btn btn-primary">

                    <i class="fa-solid fa-plus me-2"></i>

                    Cadastrar Produto

                </a>


                <a
                    href="dashboard.php"
                    class="btn btn-outline-dark">

                    <i class="fa-solid fa-arrow-left me-2"></i>

                    Voltar

                </a>

            </div>

        </div>



        <!-- PRODUTOS -->

        <div class="row g-4">


            <?php

            if ($resultado && mysqli_num_rows($resultado) > 0) {

                while ($produto = mysqli_fetch_assoc($resultado)) {

            ?>


                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">


                            <!-- IMAGEM -->

                            <div
                                style="height: 250px; overflow: hidden;">

                                <img
                                    src="../img/<?php echo htmlspecialchars($produto['imagem']); ?>"
                                    class="w-100 h-100"
                                    style="object-fit: cover;"
                                    alt="<?php echo htmlspecialchars($produto['nome']); ?>">

                            </div>


                            <!-- CONTEÚDO -->

                            <div class="card-body p-4 d-flex flex-column">


                                <span class="badge bg-primary align-self-start mb-2">
                                    Produto
                                </span>


                                <h5 class="fw-bold mb-2">

                                    <?php echo htmlspecialchars($produto['nome']); ?>

                                </h5>


                                <p class="text-muted mb-3">

                                    <?php echo htmlspecialchars($produto['descricao']); ?>

                                </p>


                                <p class="fw-bold text-success fs-4 mb-4">

                                    R$

                                    <?php echo number_format(
                                        $produto['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ); ?>

                                </p>


                                <!-- BOTÕES -->

                                <div class="d-flex gap-2 mt-auto">


                                    <a
                                        href="editar_produto.php?id=<?php echo (int) $produto['id']; ?>"
                                        class="btn btn-warning flex-fill">

                                        <i class="fa-solid fa-pen-to-square me-1"></i>

                                        Editar

                                    </a>


                                    <!-- FORMULÁRIO DE EXCLUSÃO -->

                                    <form
                                        method="POST"
                                        action="excluir_produto.php"
                                        class="flex-fill"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int) $produto['id']; ?>">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php echo htmlspecialchars($_SESSION['csrf_excluir_produto']); ?>">

                                        <button
                                            type="submit"
                                            class="btn btn-danger w-100">

                                            <i class="fa-solid fa-trash me-1"></i>

                                            Excluir

                                        </button>

                                    </form>


                                </div>


                            </div>

                        </div>

                    </div>


            <?php

                }

            } else {

            ?>


                <!-- NENHUM PRODUTO -->

                <div class="col-12">

                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body text-center py-5">


                            <div
                                class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mx-auto mb-4"
                                style="width:80px; height:80px;">

                                <i class="fa-solid fa-box-open text-primary fs-2"></i>

                            </div>


                            <h3 class="fw-bold mb-2">
                                Nenhum produto cadastrado
                            </h3>


                            <p class="text-muted mb-4">
                                Você ainda não possui produtos cadastrados na sua loja.
                            </p>


                            <a
                                href="cadastrar_produto.php"
                                class="btn btn-primary">

                                <i class="fa-solid fa-plus me-2"></i>

                                Cadastrar meu primeiro produto

                            </a>


                        </div>

                    </div>

                </div>


            <?php

            }

            ?>


        </div>

    </div>

</section>


<?php include '../includes_site/footer.php'; ?>


</body>

</html>

<?php

if ($stmt) {
    mysqli_stmt_close($stmt);
}

?>