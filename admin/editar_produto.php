<?php

include '../conexao.php';
include '../verifica_login.php';


// VERIFICAR SE O ID FOI INFORMADO

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: produtos.php");
    exit;

}


$id = (int) $_GET['id'];

$loja_id = (int) $_SESSION['loja_id'];

$mensagem = "";
$tipo_mensagem = "";


// CSRF

if (empty($_SESSION['csrf_editar_produto'])) {
    $_SESSION['csrf_editar_produto'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_editar_produto'];


// BUSCAR O PRODUTO

$sql = "SELECT id, loja_id, nome, descricao, preco, imagem
        FROM produtos
        WHERE id = ?
        AND loja_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header("Location: produtos.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $loja_id
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado || mysqli_num_rows($resultado) == 0) {

    mysqli_stmt_close($stmt);

    header("Location: produtos.php");
    exit;
}

$produto = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);


// ATUALIZAR PRODUTO

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // VERIFICAR CSRF

    $csrf_recebido = isset($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : "";

    if (
        empty($csrf_recebido) ||
        !hash_equals($_SESSION['csrf_editar_produto'], $csrf_recebido)
    ) {

        $mensagem = "Não foi possível processar a alteração. Tente novamente.";
        $tipo_mensagem = "danger";

    } else {

        // RECEBER DADOS

        $nome = isset($_POST['nome'])
            ? trim($_POST['nome'])
            : "";

        $descricao = isset($_POST['descricao'])
            ? trim($_POST['descricao'])
            : "";

        $preco = isset($_POST['preco'])
            ? trim($_POST['preco'])
            : "";

        $imagem = isset($_POST['imagem'])
            ? trim($_POST['imagem'])
            : "";


        // VALIDAÇÕES

        if ($nome == "" || $preco == "" || $imagem == "") {

            $mensagem = "Preencha todos os campos obrigatórios.";
            $tipo_mensagem = "danger";

        } elseif (strlen($nome) > 150) {

            $mensagem = "O nome do produto é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($descricao) > 1000) {

            $mensagem = "A descrição do produto é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $preco)) {

            $mensagem = "Informe um preço válido.";
            $tipo_mensagem = "danger";

        } elseif ((float) $preco < 0) {

            $mensagem = "O preço não pode ser negativo.";
            $tipo_mensagem = "danger";

        } elseif (strlen($imagem) > 255) {

            $mensagem = "O nome da imagem é muito grande.";
            $tipo_mensagem = "danger";

        } elseif ($imagem !== basename($imagem)) {

            $mensagem = "Nome de imagem inválido.";
            $tipo_mensagem = "danger";

        } elseif (!preg_match('/^[a-zA-Z0-9._-]+\.(jpg|jpeg|png|webp)$/i', $imagem)) {

            $mensagem = "A imagem deve possuir uma extensão válida (JPG, JPEG, PNG ou WEBP).";
            $tipo_mensagem = "danger";

        } else {

            // VERIFICAR SE A IMAGEM EXISTE

            $caminho_imagem = dirname(__DIR__)
                . DIRECTORY_SEPARATOR
                . "img"
                . DIRECTORY_SEPARATOR
                . $imagem;

            if (!is_file($caminho_imagem)) {

                $mensagem = "A imagem informada não foi encontrada na pasta img.";
                $tipo_mensagem = "danger";

            } else {

                // ATUALIZAR COM PREPARED STATEMENT

                $sql = "UPDATE produtos SET
                            nome = ?,
                            descricao = ?,
                            preco = ?,
                            imagem = ?
                        WHERE id = ?
                        AND loja_id = ?";

                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {

                    $preco_decimal = (float) $preco;

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssdsii",
                        $nome,
                        $descricao,
                        $preco_decimal,
                        $imagem,
                        $id,
                        $loja_id
                    );

                    if (mysqli_stmt_execute($stmt)) {

                        mysqli_stmt_close($stmt);

                        // NOVO TOKEN CSRF

                        $_SESSION['csrf_editar_produto'] = bin2hex(random_bytes(32));

                        header("Location: produtos.php");
                        exit;

                    } else {

                        mysqli_stmt_close($stmt);

                        $mensagem = "Não foi possível atualizar o produto. Tente novamente.";
                        $tipo_mensagem = "danger";
                    }

                } else {

                    $mensagem = "Não foi possível atualizar o produto. Tente novamente.";
                    $tipo_mensagem = "danger";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include '../includes_site/head.php'; ?>

    <title>Editar Produto - Vitrine Digital</title>

</head>

<body>

<?php include '../includes_site/header.php'; ?>


<section class="py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-lg-8">


                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="card-body p-4 p-md-5">


                        <!-- CABEÇALHO -->

                        <div class="text-center mb-5">

                            <span class="badge bg-warning text-dark px-3 py-2 mb-3">
                                ✏️ Área administrativa
                            </span>

                            <h1 class="fw-bold mb-2">
                                Editar Produto
                            </h1>

                            <p class="text-muted mb-0">
                                Altere as informações do seu produto.
                            </p>

                        </div>


                        <!-- MENSAGEM -->

                        <?php if ($mensagem != "") { ?>

                            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensagem); ?> text-center mb-4">

                                <i class="fa-solid fa-circle-exclamation me-2"></i>

                                <?php echo htmlspecialchars($mensagem); ?>

                            </div>

                        <?php } ?>


                        <form method="POST">

                            <!-- CSRF -->

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($csrf_token); ?>">


                            <!-- INFORMAÇÕES DO PRODUTO -->

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-box-open me-2"></i>

                                    Informações do produto

                                </h5>

                                <hr>

                            </div>


                            <!-- NOME -->

                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Nome do produto
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-tag"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="nome"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($produto['nome']); ?>"
                                        maxlength="150"
                                        required>

                                </div>

                            </div>


                            <!-- DESCRIÇÃO -->

                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Descrição
                                </label>

                                <textarea
                                    name="descricao"
                                    class="form-control"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="Descreva o produto..."><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

                            </div>


                            <!-- PREÇO -->

                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Preço
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        R$
                                    </span>

                                    <input
                                        type="number"
                                        name="preco"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        value="<?php echo htmlspecialchars($produto['preco']); ?>"
                                        required>

                                </div>

                            </div>


                            <!-- IMAGEM -->

                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Nome da imagem
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-image"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="imagem"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($produto['imagem']); ?>"
                                        maxlength="255"
                                        required>

                                </div>

                                <div class="form-text mt-2">

                                    <i class="fa-solid fa-circle-info me-1"></i>

                                    Exemplo: <strong>produto1.jpg</strong>

                                </div>

                            </div>


                            <!-- BOTÕES -->

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-5">


                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg flex-fill">

                                    <i class="fa-solid fa-floppy-disk me-2"></i>

                                    Salvar alterações

                                </button>


                                <a
                                    href="produtos.php"
                                    class="btn btn-outline-dark btn-lg flex-fill">

                                    <i class="fa-solid fa-arrow-left me-2"></i>

                                    Voltar

                                </a>


                            </div>


                        </form>


                    </div>

                </div>


            </div>

        </div>

    </div>

</section>


<?php include '../includes_site/footer.php'; ?>


</body>

</html>