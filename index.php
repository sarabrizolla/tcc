<?php
include 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>Vitrine Digital</title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>

<!-- HERO -->

<section class="hero d-flex align-items-center">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <span class="badge bg-light text-dark px-3 py-2 mb-3">
                    ✨ Conectando lojas e clientes
                </span>

                <h1 class="display-4 fw-bold mb-3">
                    Descubra lojas incríveis da sua cidade.
                </h1>

                <p class="lead mb-4">
                    Encontre produtos, serviços e contatos em um único lugar.
                </p>

                <div class="input-group input-group-lg shadow">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Pesquisar loja...">

                    <button class="btn btn-dark">

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </button>

                </div>

            </div>

            <div class="col-lg-6 text-center">

                <img
                    src="img/banner.png"
                    class="img-fluid"
                    style="max-height:420px;">

            </div>

        </div>

    </div>

</section>

<!-- CATEGORIAS -->

<section class="container py-5">

    <h2 class="text-center fw-bold mb-5">
        Explore por categoria
    </h2>

    <div class="row g-4">

        <div class="col-md-2 col-6">
            <div class="categoria">
                👗
                <h6>Moda</h6>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="categoria">
                🍔
                <h6>Alimentação</h6>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="categoria">
                💄
                <h6>Beleza</h6>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="categoria">
                💻
                <h6>Tecnologia</h6>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="categoria">
                🏋️
                <h6>Esportes</h6>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="categoria">
                🏠
                <h6>Casa</h6>
            </div>
        </div>

    </div>

</section>

<!-- LOJAS -->

<section class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            Lojas em destaque
        </h2>

        <a href="#" class="btn btn-outline-dark">
            Ver todas
        </a>

    </div>

    <div class="row">

        <?php

        $sql = "SELECT * FROM lojas";
        $resultado = mysqli_query($conn, $sql);

        while($loja = mysqli_fetch_assoc($resultado)) {

        ?>

        <div class="col-lg-4 mb-4">

            <div class="card loja-card border-0 shadow">

                <img
                    src="img/loja1.jpg"
                    class="card-img-top">

                <div class="card-body">

                    <span class="badge bg-primary mb-2">

                        <?php echo $loja['categoria']; ?>

                    </span>

                    <h4>

                        <?php echo $loja['nome']; ?>

                    </h4>

                    <p>

                        Loja cadastrada na Vitrine Digital.

                    </p>

                    <a
                        href="loja.php?id=<?php echo $loja['id']; ?>"
                        class="btn btn-gradient w-100">

                        Ver Loja

                    </a>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</section>

<?php include 'includes_site/footer.php'; ?>

</body>

</html>