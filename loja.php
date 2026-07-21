<?php

include 'conexao.php';

$id = $_GET['id'];

$sql = "SELECT * FROM lojas WHERE id = $id";

$resultado = mysqli_query($conn, $sql);

$loja = mysqli_fetch_assoc($resultado);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title><?php echo $loja['nome']; ?></title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>

<section class="container py-5">

    <div class="card shadow border-0 p-4 mb-5">

        <div class="row align-items-center">

            <div class="col-md-4 text-center">

                <img
                    src="img/loja1.jpg"
                    class="img-fluid rounded"
                    style="max-height:300px; object-fit:cover;">

            </div>

            <div class="col-md-8">

                <h1 class="fw-bold">

                    <?php echo $loja['nome']; ?>

                </h1>

                <p>

                    Loja cadastrada na Vitrine Digital.

                </p>

                <p>

                    📞 <?php echo $loja['telefone']; ?>

                </p>

                <p>

                    📷 @<?php echo $loja['instagram']; ?>

                </p>

                <p>

                    📍 <?php echo $loja['endereco']; ?>

                </p>

            </div>

        </div>

    </div>

    <h2 class="mb-4">

        Produtos

    </h2>

    <div class="row">

        <?php

        $sql = "SELECT * FROM produtos WHERE loja_id = $id";

        $resultado = mysqli_query($conn, $sql);

        while($produto = mysqli_fetch_assoc($resultado)) {

        ?>

        <div class="col-md-4 mb-4">

            <div class="card shadow border-0 h-100">

                <img
                    src="img/<?php echo $produto['imagem']; ?>"
                    class="card-img-top"
                    style="height:250px; object-fit:cover;">

                <div class="card-body">

                    <h5>

                        <?php echo $produto['nome']; ?>

                    </h5>

                    <p>

                        <?php echo $produto['descricao']; ?>

                    </p>

                    <h4 class="text-success fw-bold">

                        R$ <?php echo number_format($produto['preco'],2,",","."); ?>

                    </h4>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</section>

<?php include 'includes_site/footer.php'; ?>

</body>

</html>