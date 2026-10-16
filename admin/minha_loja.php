<?php

include '../conexao.php';
include '../verifica_login.php';


/* =========================
   CONFIGURAÇÕES
========================= */

$loja_id = (int) $_SESSION['loja_id'];

$mensagem = "";
$tipo_mensagem = "";

$max_tamanho_imagem = 5 * 1024 * 1024; // 5 MB

$categorias_permitidas = array(
    'Moda',
    'Alimentação',
    'Beleza',
    'Tecnologia',
    'Esportes',
    'Casa'
);


/* =========================
   CSRF
========================= */

if (empty($_SESSION['csrf_minha_loja'])) {

    $_SESSION['csrf_minha_loja'] = bin2hex(
        random_bytes(32)
    );

}

$csrf_token = $_SESSION['csrf_minha_loja'];


/* =========================
   BUSCAR DADOS DA LOJA
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM lojas
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $loja_id
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

$loja = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);


if (!$loja) {

    session_unset();
    session_destroy();

    header("Location: ../login.php");
    exit;

}


/* =========================
   FUNÇÃO PARA VALIDAR IMAGEM
========================= */

function validarImagemUpload($arquivo, $max_tamanho)
{

    if (!isset($arquivo) || !is_array($arquivo)) {

        return "Arquivo inválido.";

    }


    if ($arquivo['error'] !== UPLOAD_ERR_OK) {

        if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE ||
            $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {

            return "A imagem é muito grande. O tamanho máximo é 5 MB.";

        }

        return "Não foi possível enviar a imagem.";

    }


    if ($arquivo['size'] <= 0) {

        return "A imagem enviada é inválida.";

    }


    if ($arquivo['size'] > $max_tamanho) {

        return "A imagem é muito grande. O tamanho máximo é 5 MB.";

    }


    /* =========================
       VERIFICA SE REALMENTE É IMAGEM
    ========================= */

    $informacoes_imagem = @getimagesize(
        $arquivo['tmp_name']
    );

    if ($informacoes_imagem === false) {

        return "O arquivo enviado não é uma imagem válida.";

    }


    /* =========================
       MIME REAL DO ARQUIVO
    ========================= */

    if (function_exists('finfo_open')) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo !== false) {

            $mime = finfo_file(
                $finfo,
                $arquivo['tmp_name']
            );

            finfo_close($finfo);

        } else {

            $mime = "";

        }

    } else {

        $mime = isset($informacoes_imagem['mime'])
            ? $informacoes_imagem['mime']
            : "";

    }


    $mimes_permitidos = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    );


    if (!isset($mimes_permitidos[$mime])) {

        return "Formato de imagem não permitido. Use JPG, JPEG, PNG ou WEBP.";

    }


    return $mimes_permitidos[$mime];

}


/* =========================
   ATUALIZAR DADOS
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    /* =========================
       VERIFICA CSRF
    ========================= */

    $csrf_recebido = isset($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : "";


    if (
        empty($csrf_recebido) ||
        empty($_SESSION['csrf_minha_loja']) ||
        !hash_equals(
            $_SESSION['csrf_minha_loja'],
            $csrf_recebido
        )
    ) {

        $mensagem = "Não foi possível processar a alteração. Tente novamente.";
        $tipo_mensagem = "danger";

    } else {


        /* =========================
           RECEBER DADOS
        ========================= */

        $nome = trim(
            isset($_POST['nome'])
                ? $_POST['nome']
                : ""
        );

        $email = trim(
            isset($_POST['email'])
                ? $_POST['email']
                : ""
        );

        $telefone = trim(
            isset($_POST['telefone'])
                ? $_POST['telefone']
                : ""
        );

        $instagram = trim(
            isset($_POST['instagram'])
                ? $_POST['instagram']
                : ""
        );

        $endereco = trim(
            isset($_POST['endereco'])
                ? $_POST['endereco']
                : ""
        );

        $categoria = trim(
            isset($_POST['categoria'])
                ? $_POST['categoria']
                : ""
        );

        $descricao = trim(
            isset($_POST['descricao'])
                ? $_POST['descricao']
                : ""
        );

        $cidade = trim(
            isset($_POST['cidade'])
                ? $_POST['cidade']
                : ""
        );

        $estado = strtoupper(
            trim(
                isset($_POST['estado'])
                    ? $_POST['estado']
                    : ""
            )
        );

        $whatsapp = trim(
            isset($_POST['whatsapp'])
                ? $_POST['whatsapp']
                : ""
        );


        /* =========================
           VALIDAÇÕES
        ========================= */

        if ($nome === "") {

            $mensagem = "Informe o nome da loja.";
            $tipo_mensagem = "danger";

        } elseif (strlen($nome) > 100) {

            $mensagem = "O nome da loja é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (!in_array($categoria, $categorias_permitidas, true)) {

            $mensagem = "Selecione uma categoria válida.";
            $tipo_mensagem = "danger";

        } elseif (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 255
        ) {

            $mensagem = "Informe um e-mail válido.";
            $tipo_mensagem = "danger";

        } elseif (strlen($telefone) > 30) {

            $mensagem = "O telefone é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($whatsapp) > 30) {

            $mensagem = "O WhatsApp é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($instagram) > 100) {

            $mensagem = "O Instagram é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($endereco) > 255) {

            $mensagem = "O endereço é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($cidade) > 100) {

            $mensagem = "O nome da cidade é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (
            $estado !== "" &&
            !preg_match('/^[A-Z]{2}$/', $estado)
        ) {

            $mensagem = "Informe um estado válido com 2 letras.";
            $tipo_mensagem = "danger";

        } elseif (strlen($descricao) > 1000) {

            $mensagem = "A descrição da loja é muito grande.";
            $tipo_mensagem = "danger";

        }


        /* =========================
           VERIFICA E-MAIL DUPLICADO
        ========================= */

        if ($mensagem == "") {

            $stmt = mysqli_prepare(
                $conn,
                "SELECT id
                 FROM lojas
                 WHERE email = ?
                 AND id <> ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $email,
                $loja_id
            );

            mysqli_stmt_execute($stmt);

            $resultado_email = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($resultado_email) > 0) {

                $mensagem = "Este e-mail já está sendo usado por outra loja.";
                $tipo_mensagem = "danger";

            }

            mysqli_stmt_close($stmt);

        }


        /* =========================
           MANTER IMAGENS ATUAIS
        ========================= */

        $logo = $loja['logo'];
        $foto_capa = $loja['foto_capa'];

        $arquivos_novos = array();


        /* =========================
           UPLOAD DA LOGO
        ========================= */

        if (
            $mensagem == "" &&
            isset($_FILES['logo']) &&
            $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            $extensao_logo = validarImagemUpload(
                $_FILES['logo'],
                $max_tamanho_imagem
            );


            if (
                $extensao_logo === "jpg" ||
                $extensao_logo === "png" ||
                $extensao_logo === "webp"
            ) {

                try {

                    $nome_aleatorio = bin2hex(
                        random_bytes(16)
                    );

                } catch (Exception $e) {

                    $nome_aleatorio = uniqid(
                        '',
                        true
                    );

                }


                $novo_nome_logo =
                    'logo_' .
                    $loja_id .
                    '_' .
                    $nome_aleatorio .
                    '.' .
                    $extensao_logo;


                $caminho_logo =
                    '../img/' .
                    $novo_nome_logo;


                if (
                    move_uploaded_file(
                        $_FILES['logo']['tmp_name'],
                        $caminho_logo
                    )
                ) {

                    $logo = $novo_nome_logo;

                    $arquivos_novos[] = $caminho_logo;

                } else {

                    $mensagem = "Não foi possível salvar a logo enviada.";
                    $tipo_mensagem = "danger";

                }

            } else {

                $mensagem = $extensao_logo;
                $tipo_mensagem = "danger";

            }

        }


        /* =========================
           UPLOAD DA FOTO DE CAPA
        ========================= */

        if (
            $mensagem == "" &&
            isset($_FILES['foto_capa']) &&
            $_FILES['foto_capa']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            $extensao_capa = validarImagemUpload(
                $_FILES['foto_capa'],
                $max_tamanho_imagem
            );


            if (
                $extensao_capa === "jpg" ||
                $extensao_capa === "png" ||
                $extensao_capa === "webp"
            ) {

                try {

                    $nome_aleatorio = bin2hex(
                        random_bytes(16)
                    );

                } catch (Exception $e) {

                    $nome_aleatorio = uniqid(
                        '',
                        true
                    );

                }


                $novo_nome_capa =
                    'capa_' .
                    $loja_id .
                    '_' .
                    $nome_aleatorio .
                    '.' .
                    $extensao_capa;


                $caminho_capa =
                    '../img/' .
                    $novo_nome_capa;


                if (
                    move_uploaded_file(
                        $_FILES['foto_capa']['tmp_name'],
                        $caminho_capa
                    )
                ) {

                    $foto_capa = $novo_nome_capa;

                    $arquivos_novos[] = $caminho_capa;

                } else {

                    $mensagem = "Não foi possível salvar a foto de capa enviada.";
                    $tipo_mensagem = "danger";

                }

            } else {

                $mensagem = $extensao_capa;
                $tipo_mensagem = "danger";

            }

        }


        /* =========================
           ATUALIZAR BANCO
        ========================= */

        if ($mensagem == "") {

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE lojas SET
                    nome = ?,
                    email = ?,
                    telefone = ?,
                    instagram = ?,
                    endereco = ?,
                    categoria = ?,
                    descricao = ?,
                    cidade = ?,
                    estado = ?,
                    whatsapp = ?,
                    logo = ?,
                    foto_capa = ?
                 WHERE id = ?"
            );


            if ($stmt) {

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssssssssssi",
                    $nome,
                    $email,
                    $telefone,
                    $instagram,
                    $endereco,
                    $categoria,
                    $descricao,
                    $cidade,
                    $estado,
                    $whatsapp,
                    $logo,
                    $foto_capa,
                    $loja_id
                );


                if (mysqli_stmt_execute($stmt)) {

                    /* =========================
                       REMOVE IMAGENS ANTIGAS
                    ========================= */

                    if (
                        !empty($loja['logo']) &&
                        $loja['logo'] !== $logo &&
                        preg_match(
                            '/^logo_' . preg_quote($loja_id, '/') . '_/',
                            $loja['logo']
                        )
                    ) {

                        $arquivo_antigo =
                            '../img/' . basename($loja['logo']);

                        if (is_file($arquivo_antigo)) {

                            @unlink($arquivo_antigo);

                        }

                    }


                    if (
                        !empty($loja['foto_capa']) &&
                        $loja['foto_capa'] !== $foto_capa &&
                        preg_match(
                            '/^capa_' . preg_quote($loja_id, '/') . '_/',
                            $loja['foto_capa']
                        )
                    ) {

                        $arquivo_antigo =
                            '../img/' . basename($loja['foto_capa']);

                        if (is_file($arquivo_antigo)) {

                            @unlink($arquivo_antigo);

                        }

                    }


                    $mensagem =
                        "Informações da loja atualizadas com sucesso!";

                    $tipo_mensagem = "success";


                    /* =========================
                       NOVO TOKEN CSRF
                    ========================= */

                    $_SESSION['csrf_minha_loja'] =
                        bin2hex(
                            random_bytes(32)
                        );

                    $csrf_token =
                        $_SESSION['csrf_minha_loja'];


                    /* =========================
                       BUSCAR DADOS ATUALIZADOS
                    ========================= */

                    $stmt_busca = mysqli_prepare(
                        $conn,
                        "SELECT *
                         FROM lojas
                         WHERE id = ?
                         LIMIT 1"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_busca,
                        "i",
                        $loja_id
                    );

                    mysqli_stmt_execute(
                        $stmt_busca
                    );

                    $resultado =
                        mysqli_stmt_get_result(
                            $stmt_busca
                        );

                    $loja =
                        mysqli_fetch_assoc(
                            $resultado
                        );

                    mysqli_stmt_close(
                        $stmt_busca
                    );

                } else {

                    /* =========================
                       APAGA ARQUIVOS NOVOS
                       SE O BANCO FALHAR
                    ========================= */

                    foreach (
                        $arquivos_novos as $arquivo_novo
                    ) {

                        if (is_file($arquivo_novo)) {

                            @unlink($arquivo_novo);

                        }

                    }


                    if (mysqli_errno($conn) == 1062) {

                        $mensagem =
                            "Este e-mail já está sendo usado por outra loja.";

                    } else {

                        $mensagem =
                            "Não foi possível atualizar os dados da loja.";

                    }

                    $tipo_mensagem = "danger";

                }


                mysqli_stmt_close($stmt);

            } else {

                foreach (
                    $arquivos_novos as $arquivo_novo
                ) {

                    if (is_file($arquivo_novo)) {

                        @unlink($arquivo_novo);

                    }

                }

                $mensagem =
                    "Não foi possível preparar a atualização da loja.";

                $tipo_mensagem = "danger";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include '../includes_site/head.php'; ?>

    <title>Minha Loja - Vitrine Digital</title>

</head>

<body>

<?php include '../includes_site/header.php'; ?>


<section class="py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-xl-9">

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="card-body p-4 p-md-5">


                        <!-- CABEÇALHO -->

                        <div class="text-center mb-5">

                            <span class="badge bg-primary px-3 py-2 mb-3">
                                🏪 Área administrativa
                            </span>

                            <h1 class="fw-bold mb-2">
                                Minha Loja
                            </h1>

                            <p class="text-muted mb-0">
                                Edite as informações e a aparência da sua loja.
                            </p>

                        </div>


                        <!-- MENSAGEM -->

                        <?php if ($mensagem != "") { ?>

                            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensagem); ?> text-center mb-4">

                                <?php if ($tipo_mensagem == "success") { ?>

                                    <i class="fa-solid fa-circle-check me-2"></i>

                                <?php } else { ?>

                                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                                <?php } ?>

                                <?php echo htmlspecialchars($mensagem); ?>

                            </div>

                        <?php } ?>


                        <form
                            method="POST"
                            enctype="multipart/form-data">


                            <!-- CSRF -->

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($csrf_token); ?>">


                            <!-- INFORMAÇÕES DA LOJA -->

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-store me-2"></i>

                                    Informações da loja

                                </h5>

                                <hr>

                            </div>


                            <!-- NOME E CATEGORIA -->

                            <div class="row">

                                <div class="col-md-7 mb-3">

                                    <label class="form-label fw-bold">
                                        Nome da loja
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">

                                            <i class="fa-solid fa-store"></i>

                                        </span>

                                        <input
                                            type="text"
                                            name="nome"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($loja['nome']); ?>"
                                            maxlength="100"
                                            required>

                                    </div>

                                </div>


                                <div class="col-md-5 mb-3">

                                    <label class="form-label fw-bold">
                                        Categoria
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">

                                            <i class="fa-solid fa-tags"></i>

                                        </span>

                                        <select
                                            name="categoria"
                                            class="form-select"
                                            required>

                                            <option value="">
                                                Selecione uma categoria
                                            </option>

                                            <option
                                                value="Moda"
                                                <?php
                                                if ($loja['categoria'] == 'Moda') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Moda

                                            </option>

                                            <option
                                                value="Alimentação"
                                                <?php
                                                if ($loja['categoria'] == 'Alimentação') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Alimentação

                                            </option>

                                            <option
                                                value="Beleza"
                                                <?php
                                                if ($loja['categoria'] == 'Beleza') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Beleza

                                            </option>

                                            <option
                                                value="Tecnologia"
                                                <?php
                                                if ($loja['categoria'] == 'Tecnologia') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Tecnologia

                                            </option>

                                            <option
                                                value="Esportes"
                                                <?php
                                                if ($loja['categoria'] == 'Esportes') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Esportes

                                            </option>

                                            <option
                                                value="Casa"
                                                <?php
                                                if ($loja['categoria'] == 'Casa') {
                                                    echo 'selected';
                                                }
                                                ?>>

                                                Casa

                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>


                            <!-- EMAIL -->

                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    E-mail
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">

                                        <i class="fa-solid fa-envelope"></i>

                                    </span>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($loja['email']); ?>"
                                        maxlength="255"
                                        required>

                                </div>

                            </div>


                            <!-- TELEFONE E WHATSAPP -->

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Telefone
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">

                                            <i class="fa-solid fa-phone"></i>

                                        </span>

                                        <input
                                            type="text"
                                            name="telefone"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($loja['telefone']); ?>"
                                            maxlength="30">

                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        WhatsApp
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">

                                            <i class="fa-brands fa-whatsapp"></i>

                                        </span>

                                        <input
                                            type="text"
                                            name="whatsapp"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($loja['whatsapp']); ?>"
                                            maxlength="30">

                                    </div>

                                </div>

                            </div>


                            <!-- INSTAGRAM -->

                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Instagram
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">

                                        <i class="fa-brands fa-instagram"></i>

                                    </span>

                                    <input
                                        type="text"
                                        name="instagram"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($loja['instagram']); ?>"
                                        maxlength="100">

                                </div>

                            </div>


                            <!-- ENDEREÇO -->

                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Endereço
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">

                                        <i class="fa-solid fa-location-dot"></i>

                                    </span>

                                    <input
                                        type="text"
                                        name="endereco"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($loja['endereco']); ?>"
                                        maxlength="255">

                                </div>

                            </div>


                            <!-- CIDADE E ESTADO -->

                            <div class="row">

                                <div class="col-md-8 mb-3">

                                    <label class="form-label fw-bold">
                                        Cidade
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">

                                            <i class="fa-solid fa-city"></i>

                                        </span>

                                        <input
                                            type="text"
                                            name="cidade"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($loja['cidade']); ?>"
                                            maxlength="100">

                                    </div>

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label class="form-label fw-bold">
                                        Estado
                                    </label>

                                    <input
                                        type="text"
                                        name="estado"
                                        class="form-control"
                                        maxlength="2"
                                        value="<?php echo htmlspecialchars($loja['estado']); ?>">

                                </div>

                            </div>


                            <!-- DESCRIÇÃO -->

                            <div class="mb-5">

                                <label class="form-label fw-bold">
                                    Descrição da loja
                                </label>

                                <textarea
                                    name="descricao"
                                    class="form-control"
                                    rows="5"
                                    maxlength="1000"
                                    placeholder="Conte um pouco sobre sua loja..."><?php echo htmlspecialchars($loja['descricao']); ?></textarea>

                            </div>


                            <!-- APARÊNCIA -->

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-image me-2"></i>

                                    Aparência da loja

                                </h5>

                                <p class="text-muted mb-3">
                                    Personalize a logo e a foto de capa exibidas na sua loja.
                                </p>

                                <hr>

                            </div>


                            <!-- LOGO -->

                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Logo da loja
                                </label>

                                <input
                                    type="file"
                                    name="logo"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">

                                <div class="form-text mt-2">

                                    <i class="fa-solid fa-circle-info me-1"></i>

                                    Formatos permitidos: JPG, JPEG, PNG e WEBP. Tamanho máximo: 5 MB.

                                </div>


                                <?php if (!empty($loja['logo'])) { ?>

                                    <div class="mt-4 p-3 bg-light rounded-4">

                                        <p class="fw-bold mb-3">
                                            Logo atual
                                        </p>

                                        <img
                                            src="../img/<?php echo htmlspecialchars($loja['logo']); ?>"
                                            style="width:120px; height:120px; object-fit:cover;"
                                            class="rounded-circle shadow-sm"
                                            alt="Logo atual da loja">

                                    </div>

                                <?php } ?>

                            </div>


                            <!-- FOTO DE CAPA -->

                            <div class="mb-5">

                                <label class="form-label fw-bold">
                                    Foto de capa
                                </label>

                                <input
                                    type="file"
                                    name="foto_capa"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">

                                <div class="form-text mt-2">

                                    <i class="fa-solid fa-circle-info me-1"></i>

                                    Escolha uma imagem para aparecer no topo da sua loja. Tamanho máximo: 5 MB.

                                </div>


                                <?php if (!empty($loja['foto_capa'])) { ?>

                                    <div class="mt-4 p-3 bg-light rounded-4">

                                        <p class="fw-bold mb-3">
                                            Foto de capa atual
                                        </p>

                                        <img
                                            src="../img/<?php echo htmlspecialchars($loja['foto_capa']); ?>"
                                            style="width:100%; max-height:250px; object-fit:cover;"
                                            class="rounded-4 shadow-sm"
                                            alt="Foto de capa atual da loja">

                                    </div>

                                <?php } ?>

                            </div>


                            <!-- BOTÕES -->

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg flex-fill">

                                    <i class="fa-solid fa-floppy-disk me-2"></i>

                                    Salvar Alterações

                                </button>


                                <a
                                    href="dashboard.php"
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