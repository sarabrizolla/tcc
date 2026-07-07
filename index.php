<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>Vitrine Digital</title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>

<div class="hero">

    <div class="container text-center">

        <h1>Encontre lojas da sua região</h1>

        <p>
            Produtos, serviços e contatos em um só lugar.
        </p>

        <div class="pesquisa">

            <input type="text"
                   placeholder="Pesquisar loja...">

            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

        </div>

    </div>

</div>

<div class="container mt-5">

    <h2 class="mb-4">
        Lojas em destaque
    </h2>

    <div class="row">

        <div class="col-md-4 mb-4">

            <div class="card-loja">

                <img src="img/loja1.jpg">

                <h4>Loja Exemplo</h4>

                <p>
                    Roupas e acessórios.
                </p>

                <a href="#" class="btn btn-primary">
                    Ver Loja
                </a>

            </div>

        </div>

    </div>

</div>

<?php include 'includes_site/footer.php'; ?>

</body>
</html>