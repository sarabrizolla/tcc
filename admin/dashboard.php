<?php

include '../conexao.php';
include '../verifica_login.php';


// GARANTIR QUE O ID DA LOJA SEJA NUMÉRICO

$loja_id = (int) $_SESSION['loja_id'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include '../includes_site/head.php'; ?>

    <title>Painel da Loja - Vitrine Digital</title>

</head>

<body>

<?php include '../includes_site/header.php'; ?>


<!-- PAINEL -->

<section class="py-5">

    <div class="container">


        <!-- CABEÇALHO -->

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 mb-5">

            <div>

                <span class="badge bg-primary px-3 py-2 mb-2">
                    Área administrativa
                </span>

                <h1 class="fw-bold mb-2">
                    Olá, <?php echo htmlspecialchars($_SESSION['loja_nome']); ?>! 👋
                </h1>

                <p class="text-muted mb-0">
                    Gerencie sua loja, produtos e informações.
                </p>

            </div>


            <div>

                <a
                    href="sair.php"
                    class="btn btn-outline-danger">

                    <i class="fa-solid fa-right-from-bracket me-2"></i>

                    Sair

                </a>

            </div>

        </div>



        <!-- CARDS -->

        <div class="row g-4">


            <!-- PRODUTOS -->

            <div class="col-12 col-md-6 col-lg-4">

                <div class="card border-0 shadow-lg rounded-4 h-100">

                    <div class="card-body p-4 d-flex flex-column">


                        <div
                            class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-4 mb-4"
                            style="width:70px; height:70px;">

                            <i class="fa-solid fa-box-open text-primary fs-2"></i>

                        </div>


                        <h4 class="fw-bold mb-2">
                            Meus Produtos
                        </h4>

                        <p class="text-muted mb-4">
                            Cadastre, edite e exclua os produtos da sua loja.
                        </p>


                        <div class="d-flex flex-column flex-sm-row gap-2 mt-auto">

                            <a
                                href="produtos.php"
                                class="btn btn-primary flex-fill">

                                <i class="fa-solid fa-box me-1"></i>

                                Ver Produtos

                            </a>

                            <a
                                href="cadastrar_produto.php"
                                class="btn btn-outline-primary flex-fill">

                                <i class="fa-solid fa-plus me-1"></i>

                                Cadastrar

                            </a>

                        </div>


                    </div>

                </div>

            </div>



            <!-- MINHA LOJA -->

            <div class="col-12 col-md-6 col-lg-4">

                <div class="card border-0 shadow-lg rounded-4 h-100">

                    <div class="card-body p-4 d-flex flex-column">


                        <div
                            class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-4 mb-4"
                            style="width:70px; height:70px;">

                            <i class="fa-solid fa-store text-success fs-2"></i>

                        </div>


                        <h4 class="fw-bold mb-2">
                            Minha Loja
                        </h4>

                        <p class="text-muted mb-4">
                            Visualize a página pública da sua loja como seus clientes.
                        </p>


                        <div class="mt-auto">

                            <a
                                href="../loja.php?id=<?php echo $loja_id; ?>"
                                class="btn btn-outline-dark w-100">

                                <i class="fa-solid fa-eye me-2"></i>

                                Ver Minha Loja

                            </a>

                        </div>


                    </div>

                </div>

            </div>



            <!-- CONFIGURAÇÕES -->

            <div class="col-12 col-md-6 col-lg-4">

                <div class="card border-0 shadow-lg rounded-4 h-100">

                    <div class="card-body p-4 d-flex flex-column">


                        <div
                            class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-4 mb-4"
                            style="width:70px; height:70px;">

                            <i class="fa-solid fa-gear text-warning fs-2"></i>

                        </div>


                        <h4 class="fw-bold mb-2">
                            Configurações
                        </h4>

                        <p class="text-muted mb-4">
                            Altere as informações, logo e aparência da sua loja.
                        </p>


                        <div class="mt-auto">

                            <a
                                href="minha_loja.php"
                                class="btn btn-outline-dark w-100">

                                <i class="fa-solid fa-pen-to-square me-2"></i>

                                Editar Loja

                            </a>

                        </div>


                    </div>

                </div>

            </div>


        </div>


        <!-- INFORMAÇÃO -->

        <div class="mt-5">

            <div class="alert alert-light border shadow-sm rounded-4 mb-0">

                <div class="d-flex align-items-start gap-3">

                    <i class="fa-solid fa-circle-info text-primary fs-4 mt-1"></i>

                    <div>

                        <strong>Dica:</strong>

                        <span class="text-muted">
                            mantenha os produtos e informações da sua loja sempre atualizados
                            para que seus clientes tenham uma experiência melhor.
                        </span>

                    </div>

                </div>

            </div>

        </div>


    </div>

</section>


<?php include '../includes_site/footer.php'; ?>


</body>

</html>