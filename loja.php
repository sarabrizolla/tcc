<?php

include 'conexao.php';


/*
------------------------------------------------
VERIFICAR SE O ID FOI INFORMADO
------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");
    exit;

}


$id = (int) $_GET['id'];


/*
------------------------------------------------
BUSCAR LOJA COM PREPARED STATEMENT
------------------------------------------------
*/

$sql = "SELECT
            id,
            nome,
            email,
            telefone,
            instagram,
            endereco,
            categoria,
            descricao,
            cidade,
            estado,
            whatsapp,
            logo,
            foto_capa
        FROM lojas
        WHERE id = ?
        LIMIT 1";

$stmt_loja = mysqli_prepare($conn, $sql);

if (!$stmt_loja) {

    header("Location: index.php");
    exit;

}

mysqli_stmt_bind_param(
    $stmt_loja,
    "i",
    $id
);

if (!mysqli_stmt_execute($stmt_loja)) {

    mysqli_stmt_close($stmt_loja);

    header("Location: index.php");
    exit;

}

$resultado = mysqli_stmt_get_result($stmt_loja);


/*
------------------------------------------------
VERIFICAR SE A LOJA EXISTE
------------------------------------------------
*/

if (!$resultado || mysqli_num_rows($resultado) == 0) {

    mysqli_stmt_close($stmt_loja);

    header("Location: index.php");
    exit;

}


$loja = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt_loja);


/*
------------------------------------------------
FUNÇÃO PARA VALIDAR NOME DE IMAGEM
------------------------------------------------
*/

function imagemSegura($nome, $imagemPadrao)
{

    if (empty($nome)) {

        return $imagemPadrao;

    }


    /*
    Impedir caminhos como:
    ../arquivo
    ../../arquivo
    pasta/arquivo
    */

    if (basename($nome) !== $nome) {

        return $imagemPadrao;

    }


    /*
    Permitir somente extensões de imagem
    */

    $extensao = strtolower(
        pathinfo($nome, PATHINFO_EXTENSION)
    );

    $extensoes_permitidas = array(
        "jpg",
        "jpeg",
        "png",
        "webp"
    );


    if (!in_array($extensao, $extensoes_permitidas, true)) {

        return $imagemPadrao;

    }


    /*
    Verificar se o arquivo realmente existe
    */

    $caminho = __DIR__ . "/img/" . $nome;

    if (!file_exists($caminho)) {

        return $imagemPadrao;

    }


    return $nome;

}


/*
------------------------------------------------
DEFINIR IMAGEM DE CAPA
------------------------------------------------
*/

$foto_capa_nome = imagemSegura(
    $loja['foto_capa'],
    "loja1.jpg"
);

$foto_capa = "img/" . $foto_capa_nome;


/*
------------------------------------------------
DEFINIR LOGO
------------------------------------------------
*/

$logo_nome = imagemSegura(
    $loja['logo'],
    "loja1.jpg"
);

$logo = "img/" . $logo_nome;

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>
        <?php echo htmlspecialchars(
            $loja['nome'],
            ENT_QUOTES,
            'UTF-8'
        ); ?> - Vitrine Digital
    </title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>


<section class="py-5">

    <div class="container">


        <!-- =========================
             CAPA DA LOJA
        ========================== -->

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">


            <!-- FOTO DE CAPA -->

            <img
                src="<?php echo htmlspecialchars(
                    $foto_capa,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                class="w-100"
                style="height:300px; object-fit:cover;"
                alt="Foto de capa da loja">


            <!-- INFORMAÇÕES DA LOJA -->

            <div class="card-body p-4 p-md-5">


                <div class="row align-items-center g-4">


                    <!-- LOGO -->

                    <div class="col-12 col-md-3 text-center">

                        <img
                            src="<?php echo htmlspecialchars(
                                $logo,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="img-fluid rounded-circle shadow"
                            style="width:180px; height:180px; object-fit:cover;"
                            alt="Logo da loja">

                    </div>


                    <!-- DADOS DA LOJA -->

                    <div class="col-12 col-md-9">


                        <span class="badge bg-primary px-3 py-2 mb-3">

                            <i class="fa-solid fa-tag me-1"></i>

                            <?php echo htmlspecialchars(
                                $loja['categoria'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </span>


                        <h1 class="fw-bold mb-2">

                            <?php echo htmlspecialchars(
                                $loja['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </h1>


                        <?php if (!empty($loja['descricao'])) { ?>

                            <p class="text-muted mb-4">

                                <?php echo htmlspecialchars(
                                    $loja['descricao'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </p>

                        <?php } ?>


                        <!-- CONTATOS -->

                        <div class="d-flex flex-wrap gap-2">


                            <!-- TELEFONE -->

                            <?php if (!empty($loja['telefone'])) { ?>

                                <a
                                    href="tel:<?php echo htmlspecialchars(
                                        $loja['telefone'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="btn btn-outline-dark">

                                    <i class="fa-solid fa-phone me-2"></i>

                                    Telefone

                                </a>

                            <?php } ?>


                            <!-- INSTAGRAM -->

                            <?php if (!empty($loja['instagram'])) { ?>

                                <?php

                                $instagram = ltrim(
                                    trim($loja['instagram']),
                                    '@'
                                );

                                ?>

                                <a
                                    href="https://instagram.com/<?php echo htmlspecialchars(
                                        $instagram,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-outline-danger">

                                    <i class="fa-brands fa-instagram me-2"></i>

                                    Instagram

                                </a>

                            <?php } ?>


                            <!-- WHATSAPP -->

                            <?php if (!empty($loja['whatsapp'])) { ?>

                                <?php

                                $whatsapp = preg_replace(
                                    '/[^0-9]/',
                                    '',
                                    $loja['whatsapp']
                                );

                                ?>

                                <?php if ($whatsapp != "") { ?>

                                    <a
                                        href="https://wa.me/<?php echo htmlspecialchars(
                                            $whatsapp,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-outline-success">

                                        <i class="fa-brands fa-whatsapp me-2"></i>

                                        WhatsApp

                                    </a>

                                <?php } ?>

                            <?php } ?>


                            <!-- ENDEREÇO -->

                            <?php if (!empty($loja['endereco'])) { ?>

                                <a
                                    href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode(
                                        $loja['endereco']
                                    ); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-outline-primary">

                                    <i class="fa-solid fa-location-dot me-2"></i>

                                    Ver localização

                                </a>

                            <?php } ?>


                        </div>


                        <!-- LOCALIZAÇÃO -->

                        <?php if (
                            !empty($loja['endereco']) ||
                            !empty($loja['cidade']) ||
                            !empty($loja['estado'])
                        ) { ?>

                            <div class="mt-4 p-3 bg-light rounded-4">


                                <?php if (!empty($loja['endereco'])) { ?>

                                    <p class="mb-2">

                                        <i class="fa-solid fa-location-dot text-primary me-2"></i>

                                        <strong>Endereço:</strong>

                                        <?php echo htmlspecialchars(
                                            $loja['endereco'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </p>

                                <?php } ?>


                                <?php if (
                                    !empty($loja['cidade']) ||
                                    !empty($loja['estado'])
                                ) { ?>

                                    <p class="mb-0">

                                        <i class="fa-solid fa-city text-primary me-2"></i>

                                        <strong>Localização:</strong>

                                        <?php echo htmlspecialchars(
                                            $loja['cidade'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                        <?php if (!empty($loja['estado'])) { ?>

                                            -
                                            <?php echo htmlspecialchars(
                                                $loja['estado'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        <?php } ?>

                                    </p>

                                <?php } ?>


                            </div>

                        <?php } ?>


                    </div>


                </div>


            </div>


        </div>


        <!-- =========================
             PRODUTOS
        ========================== -->

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">


            <div>

                <span class="badge bg-primary px-3 py-2 mb-2">

                    <i class="fa-solid fa-box-open me-1"></i>

                    Vitrine

                </span>


                <h2 class="fw-bold mb-1">

                    Produtos

                </h2>


                <p class="text-muted mb-0">

                    Confira os produtos disponíveis nesta loja.

                </p>

            </div>


        </div>


        <div class="row g-4">


            <?php

            /*
            ------------------------------------------------
            BUSCAR PRODUTOS COM PREPARED STATEMENT
            ------------------------------------------------
            */

            $sql = "SELECT
                        id,
                        nome,
                        descricao,
                        preco,
                        imagem
                    FROM produtos
                    WHERE loja_id = ?
                    ORDER BY id DESC";


            $stmt_produtos = mysqli_prepare(
                $conn,
                $sql
            );


            $resultado = false;


            if ($stmt_produtos) {

                mysqli_stmt_bind_param(
                    $stmt_produtos,
                    "i",
                    $id
                );


                if (mysqli_stmt_execute($stmt_produtos)) {

                    $resultado = mysqli_stmt_get_result(
                        $stmt_produtos
                    );

                }

            }


            if (
                $resultado &&
                mysqli_num_rows($resultado) > 0
            ) {


                while (
                    $produto = mysqli_fetch_assoc($resultado)
                ) {

                    /*
                    ----------------------------------------
                    VALIDAR IMAGEM DO PRODUTO
                    ----------------------------------------
                    */

                    $imagem_produto_nome = imagemSegura(
                        $produto['imagem'],
                        "produto1.jpg"
                    );

                    $imagem_produto =
                        "img/" . $imagem_produto_nome;

            ?>


                    <div class="col-12 col-md-6 col-lg-4">


                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">


                            <!-- IMAGEM DO PRODUTO -->

                            <div
                                style="height:250px; overflow:hidden;">

                                <img
                                    src="<?php echo htmlspecialchars(
                                        $imagem_produto,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="w-100 h-100"
                                    style="object-fit:cover;"
                                    alt="<?php echo htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>">

                            </div>


                            <!-- INFORMAÇÕES DO PRODUTO -->

                            <div class="card-body p-4 d-flex flex-column">


                                <span class="badge bg-primary align-self-start mb-2">

                                    Produto

                                </span>


                                <h5 class="fw-bold mb-2">

                                    <?php echo htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </h5>


                                <?php if (!empty($produto['descricao'])) { ?>

                                    <p class="text-muted mb-3">

                                        <?php echo htmlspecialchars(
                                            $produto['descricao'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </p>

                                <?php } ?>


                                <div class="mt-auto">


                                    <h4 class="text-success fw-bold mb-0">

                                        R$

                                        <?php echo number_format(
                                            $produto['preco'],
                                            2,
                                            ',',
                                            '.'
                                        ); ?>

                                    </h4>


                                </div>


                            </div>


                        </div>


                    </div>


            <?php

                }


            } else {

            ?>


                <div class="col-12">


                    <div class="card border-0 shadow-sm rounded-4">


                        <div class="card-body text-center py-5">


                            <div
                                class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mx-auto mb-4"
                                style="width:80px; height:80px;">

                                <i class="fa-solid fa-box-open text-primary fs-2"></i>

                            </div>


                            <h4 class="fw-bold mb-2">

                                Nenhum produto cadastrado

                            </h4>


                            <p class="text-muted mb-0">

                                Esta loja ainda não possui produtos cadastrados.

                            </p>


                        </div>


                    </div>


                </div>


            <?php

            }


            if ($stmt_produtos) {

                mysqli_stmt_close(
                    $stmt_produtos
                );

            }

            ?>


        </div>


    </div>

</section>


<?php include 'includes_site/footer.php'; ?>


</body>

</html>