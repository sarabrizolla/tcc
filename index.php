<?php

include 'conexao.php';

$busca = "";
$categoria = "";

/*
------------------------------------------------
CATEGORIAS PERMITIDAS
------------------------------------------------
*/

$categorias_permitidas = array(
    "Moda",
    "Alimentação",
    "Beleza",
    "Tecnologia",
    "Esportes",
    "Casa"
);


/*
------------------------------------------------
RECEBER E VALIDAR PESQUISA
------------------------------------------------
*/

if (isset($_GET['busca'])) {

    $busca = trim($_GET['busca']);

    /*
    Limitar o tamanho da pesquisa para evitar
    entradas exageradamente grandes.
    */

    if (strlen($busca) > 100) {
        $busca = substr($busca, 0, 100);
    }
}


/*
------------------------------------------------
RECEBER E VALIDAR CATEGORIA
------------------------------------------------
*/

if (isset($_GET['categoria'])) {

    $categoria_recebida = trim($_GET['categoria']);

    if (in_array($categoria_recebida, $categorias_permitidas, true)) {

        $categoria = $categoria_recebida;

    } else {

        $categoria = "";

    }
}

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

    <div class="container py-5">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <span class="badge bg-light text-dark px-3 py-2 mb-3 shadow-sm">
                    ✨ Conectando lojas e clientes
                </span>

                <h1 class="display-4 fw-bold mb-3">
                    Descubra lojas incríveis da sua cidade.
                </h1>

                <p class="lead mb-4">
                    Encontre produtos, serviços e contatos em um único lugar.
                </p>


                <!-- PESQUISA -->

                <form method="GET" action="index.php">

                    <div class="input-group input-group-lg shadow">

                        <input
                            type="text"
                            name="busca"
                            class="form-control"
                            placeholder="Pesquisar loja..."
                            value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>"
                            maxlength="100">

                        <button
                            class="btn btn-dark px-4"
                            type="submit">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </button>

                    </div>

                </form>

            </div>


            <div class="col-lg-6 text-center">

                <img
                    src="img/banner.png"
                    class="img-fluid"
                    style="max-height:420px;"
                    alt="Ilustração da Vitrine Digital">

            </div>

        </div>

    </div>

</section>


<!-- CATEGORIAS -->

<section class="container py-5">

    <div class="text-center mb-5">

        <span class="badge bg-primary px-3 py-2 mb-2">
            Explore
        </span>

        <h2 class="fw-bold mb-2">
            Encontre pelo que você procura
        </h2>

        <p class="text-muted mb-0">
            Navegue pelas categorias e descubra lojas da sua região.
        </p>

    </div>


    <div class="row g-4">


        <!-- MODA -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Moda"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    👗

                    <h6>
                        Moda
                    </h6>

                </div>

            </a>

        </div>


        <!-- ALIMENTAÇÃO -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Alimenta%C3%A7%C3%A3o"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    🍔

                    <h6>
                        Alimentação
                    </h6>

                </div>

            </a>

        </div>


        <!-- BELEZA -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Beleza"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    💄

                    <h6>
                        Beleza
                    </h6>

                </div>

            </a>

        </div>


        <!-- TECNOLOGIA -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Tecnologia"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    💻

                    <h6>
                        Tecnologia
                    </h6>

                </div>

            </a>

        </div>


        <!-- ESPORTES -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Esportes"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    🏋️

                    <h6>
                        Esportes
                    </h6>

                </div>

            </a>

        </div>


        <!-- CASA -->

        <div class="col-6 col-md-4 col-lg-2">

            <a
                href="index.php?categoria=Casa"
                class="text-decoration-none text-dark">

                <div class="categoria">

                    🏠

                    <h6>
                        Casa
                    </h6>

                </div>

            </a>

        </div>


    </div>

</section>


<!-- LOJAS -->

<section class="container pb-5">


    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>

            <?php if ($categoria != "") { ?>

                <span class="badge bg-primary mb-2">
                    Categoria selecionada
                </span>

                <h2 class="fw-bold mb-0">
                    Lojas de <?php echo htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>
                </h2>

            <?php } elseif ($busca != "") { ?>

                <span class="badge bg-primary mb-2">
                    Pesquisa
                </span>

                <h2 class="fw-bold mb-0">
                    Resultados para:
                    "<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>"
                </h2>

            <?php } else { ?>

                <span class="badge bg-primary mb-2">
                    Vitrine Digital
                </span>

                <h2 class="fw-bold mb-0">
                    Lojas em destaque
                </h2>

            <?php } ?>

        </div>


        <a
            href="index.php"
            class="btn btn-outline-dark">

            <i class="fa-solid fa-store me-2"></i>
            Ver todas

        </a>

    </div>


    <div class="row g-4">


        <?php

        /*
        ------------------------------------------------
        MONTANDO A PESQUISA COM PREPARED STATEMENT
        ------------------------------------------------
        */

        $condicoes = array();
        $parametros = array();
        $tipos = "";


        /*
        PESQUISA POR NOME
        */

        if ($busca != "") {

            $condicoes[] = "nome LIKE ?";

            $parametros[] = "%" . $busca . "%";

            $tipos .= "s";

        }


        /*
        FILTRO POR CATEGORIA
        */

        if ($categoria != "") {

            $condicoes[] = "categoria = ?";

            $parametros[] = $categoria;

            $tipos .= "s";

        }


        /*
        MONTAR SQL
        */

        if (count($condicoes) > 0) {

            $sql = "SELECT
                        id,
                        nome,
                        categoria
                    FROM lojas
                    WHERE " . implode(" AND ", $condicoes) . "
                    ORDER BY id DESC";

        } else {

            $sql = "SELECT
                        id,
                        nome,
                        categoria
                    FROM lojas
                    ORDER BY id DESC";

        }


        /*
        PREPARAR CONSULTA
        */

        $stmt = mysqli_prepare($conn, $sql);

        $resultado = false;


        if ($stmt) {

            /*
            Se houver parâmetros, vinculá-los.
            */

            if (count($parametros) > 0) {

                mysqli_stmt_bind_param(
                    $stmt,
                    $tipos,
                    ...$parametros
                );

            }


            /*
            EXECUTAR CONSULTA
            */

            if (mysqli_stmt_execute($stmt)) {

                $resultado = mysqli_stmt_get_result($stmt);

            }

        }


        /*
        EXIBIR LOJAS
        */

        if ($resultado && mysqli_num_rows($resultado) > 0) {


            while ($loja = mysqli_fetch_assoc($resultado)) {

        ?>


        <div class="col-12 col-md-6 col-lg-4">


            <div class="card loja-card border-0 shadow h-100 overflow-hidden">


                <img
                    src="img/loja1.jpg"
                    class="card-img-top"
                    alt="<?php echo htmlspecialchars($loja['nome'], ENT_QUOTES, 'UTF-8'); ?>">


                <div class="card-body p-4 d-flex flex-column">


                    <span class="badge bg-primary align-self-start mb-2">

                        <?php echo htmlspecialchars($loja['categoria'], ENT_QUOTES, 'UTF-8'); ?>

                    </span>


                    <h4 class="fw-bold mb-2">

                        <?php echo htmlspecialchars($loja['nome'], ENT_QUOTES, 'UTF-8'); ?>

                    </h4>


                    <p class="text-muted mb-4">

                        Loja cadastrada na Vitrine Digital.

                    </p>


                    <a
                        href="loja.php?id=<?php echo (int) $loja['id']; ?>"
                        class="btn btn-gradient w-100 mt-auto">

                        <i class="fa-solid fa-store me-2"></i>
                        Ver Loja

                    </a>


                </div>


            </div>


        </div>


        <?php

            }

        } else {

        ?>


        <div class="col-12">

            <div class="alert alert-warning text-center shadow-sm">

                <?php if ($categoria != "") { ?>

                    Nenhuma loja encontrada na categoria
                    "<strong><?php echo htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?></strong>".

                <?php } elseif ($busca != "") { ?>

                    Nenhuma loja encontrada para
                    "<strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong>".

                <?php } else { ?>

                    Ainda não existem lojas cadastradas.

                <?php } ?>

            </div>

        </div>


        <?php

        }


        /*
        FECHAR STATEMENT
        */

        if ($stmt) {

            mysqli_stmt_close($stmt);

        }

        ?>


    </div>

</section>


<?php include 'includes_site/footer.php'; ?>


</body>

</html>