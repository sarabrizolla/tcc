<?php

session_start();

include 'conexao.php';

$mensagem = "";
$tipo_mensagem = "";


/* =========================
   TOKEN CSRF
========================= */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


/* =========================
   PROCESSAMENTO DO FORMULÁRIO
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* =========================
       VERIFICA TOKEN CSRF
    ========================= */

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {

        $mensagem = "Não foi possível processar o cadastro. Tente novamente.";
        $tipo_mensagem = "danger";

    } else {

        /* =========================
           RECEBE OS DADOS
        ========================= */

        $nome = trim($_POST['nome'] ?? "");
        $categoria = trim($_POST['categoria'] ?? "");
        $email = trim($_POST['email'] ?? "");
        $senha_original = $_POST['senha'] ?? "";

        $telefone = trim($_POST['telefone'] ?? "");
        $whatsapp = trim($_POST['whatsapp'] ?? "");
        $instagram = trim($_POST['instagram'] ?? "");
        $cidade = trim($_POST['cidade'] ?? "");
        $estado = strtoupper(trim($_POST['estado'] ?? ""));
        $endereco = trim($_POST['endereco'] ?? "");
        $descricao = trim($_POST['descricao'] ?? "");


        /* =========================
           CATEGORIAS PERMITIDAS
        ========================= */

        $categorias_permitidas = [
            "Moda",
            "Alimentação",
            "Beleza",
            "Tecnologia",
            "Esportes",
            "Casa"
        ];


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

            $mensagem = "Categoria inválida.";
            $tipo_mensagem = "danger";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $mensagem = "Informe um e-mail válido.";
            $tipo_mensagem = "danger";

        } elseif (strlen($email) > 255) {

            $mensagem = "O e-mail informado é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($senha_original) < 8) {

            $mensagem = "A senha deve possuir pelo menos 8 caracteres.";
            $tipo_mensagem = "danger";

        } elseif (strlen($telefone) > 30) {

            $mensagem = "O telefone informado é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($whatsapp) > 30) {

            $mensagem = "O WhatsApp informado é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($instagram) > 100) {

            $mensagem = "O Instagram informado é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($cidade) > 100) {

            $mensagem = "A cidade informada é muito grande.";
            $tipo_mensagem = "danger";

        } elseif ($estado !== "" && !preg_match('/^[A-Z]{2}$/', $estado)) {

            $mensagem = "Informe um estado válido usando duas letras. Exemplo: RS.";
            $tipo_mensagem = "danger";

        } elseif (strlen($endereco) > 255) {

            $mensagem = "O endereço informado é muito grande.";
            $tipo_mensagem = "danger";

        } elseif (strlen($descricao) > 1000) {

            $mensagem = "A descrição da loja é muito grande.";
            $tipo_mensagem = "danger";

        } else {

            /* =========================
               VERIFICA E-MAIL
            ========================= */

            $sql_verifica = "SELECT id FROM lojas WHERE email = ? LIMIT 1";

            $stmt_verifica = mysqli_prepare($conn, $sql_verifica);

            if (!$stmt_verifica) {

                $mensagem = "Não foi possível realizar o cadastro. Tente novamente.";
                $tipo_mensagem = "danger";

            } else {

                mysqli_stmt_bind_param(
                    $stmt_verifica,
                    "s",
                    $email
                );

                mysqli_stmt_execute($stmt_verifica);

                mysqli_stmt_store_result($stmt_verifica);

                if (mysqli_stmt_num_rows($stmt_verifica) > 0) {

                    $mensagem = "Este e-mail já está cadastrado.";
                    $tipo_mensagem = "danger";

                    mysqli_stmt_close($stmt_verifica);

                } else {

                    mysqli_stmt_close($stmt_verifica);


                    /* =========================
                       CRIA HASH DA SENHA
                    ========================= */

                    $senha = password_hash(
                        $senha_original,
                        PASSWORD_DEFAULT
                    );


                    if ($senha === false) {

                        $mensagem = "Não foi possível proteger a senha. Tente novamente.";
                        $tipo_mensagem = "danger";

                    } else {

                        /* =========================
                           CADASTRA A LOJA
                        ========================= */

                        $sql = "INSERT INTO lojas
                        (
                            nome,
                            email,
                            senha,
                            telefone,
                            instagram,
                            endereco,
                            categoria,
                            descricao,
                            cidade,
                            estado,
                            whatsapp
                        )
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                        $stmt = mysqli_prepare($conn, $sql);

                        if (!$stmt) {

                            $mensagem = "Não foi possível realizar o cadastro. Tente novamente.";
                            $tipo_mensagem = "danger";

                        } else {

                            mysqli_stmt_bind_param(
                                $stmt,
                                "sssssssssss",
                                $nome,
                                $email,
                                $senha,
                                $telefone,
                                $instagram,
                                $endereco,
                                $categoria,
                                $descricao,
                                $cidade,
                                $estado,
                                $whatsapp
                            );


                            if (mysqli_stmt_execute($stmt)) {

                                $mensagem = "Loja cadastrada com sucesso!";
                                $tipo_mensagem = "success";

                                /*
                                 * Gera um novo token depois
                                 * de um cadastro bem-sucedido.
                                 */
                                $_SESSION['csrf_token'] = bin2hex(
                                    random_bytes(32)
                                );

                            } else {

                                /*
                                 * Caso o banco rejeite o e-mail
                                 * por causa da regra UNIQUE.
                                 */
                                if (mysqli_errno($conn) == 1062) {

                                    $mensagem = "Este e-mail já está cadastrado.";

                                } else {

                                    $mensagem = "Não foi possível realizar o cadastro. Tente novamente.";

                                }

                                $tipo_mensagem = "danger";
                            }

                            mysqli_stmt_close($stmt);
                        }
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>Cadastrar Loja - Vitrine Digital</title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>


<section class="py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-lg-9 col-xl-8">

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="card-body p-4 p-md-5">


                        <!-- CABEÇALHO -->

                        <div class="text-center mb-5">

                            <span class="badge bg-primary px-3 py-2 mb-3">
                                🏪 Nova loja
                            </span>

                            <h1 class="fw-bold mb-2">
                                Cadastre sua loja
                            </h1>

                            <p class="text-muted mb-0">
                                Crie sua vitrine e mostre seus produtos para sua região.
                            </p>

                        </div>


                        <!-- MENSAGEM -->

                        <?php if ($mensagem != "") { ?>

                            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensagem); ?> text-center">

                                <?php if ($tipo_mensagem == "success") { ?>

                                    <i class="fa-solid fa-circle-check me-2"></i>

                                <?php } else { ?>

                                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                                <?php } ?>

                                <?php echo htmlspecialchars($mensagem); ?>

                            </div>

                        <?php } ?>


                        <form action="" method="POST">

                            <!-- TOKEN CSRF -->

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">


                            <!-- DADOS PRINCIPAIS -->

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-store me-2"></i>

                                    Informações da loja

                                </h5>

                                <hr>

                            </div>


                            <div class="row">


                                <!-- NOME -->

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Nome da Loja
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="fa-solid fa-store"></i>
                                        </span>

                                        <input
                                            type="text"
                                            name="nome"
                                            class="form-control"
                                            placeholder="Nome da sua loja"
                                            maxlength="100"
                                            required>

                                    </div>

                                </div>


                                <!-- CATEGORIA -->

                                <div class="col-md-6 mb-3">

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

                                            <option value="Moda">Moda</option>
                                            <option value="Alimentação">Alimentação</option>
                                            <option value="Beleza">Beleza</option>
                                            <option value="Tecnologia">Tecnologia</option>
                                            <option value="Esportes">Esportes</option>
                                            <option value="Casa">Casa</option>

                                        </select>

                                    </div>

                                </div>


                                <!-- E-MAIL -->

                                <div class="col-md-6 mb-3">

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
                                            placeholder="exemplo@email.com"
                                            maxlength="255"
                                            required>

                                    </div>

                                </div>


                                <!-- SENHA -->

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Senha
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>

                                        <input
                                            type="password"
                                            name="senha"
                                            class="form-control"
                                            placeholder="Mínimo de 8 caracteres"
                                            minlength="8"
                                            required>

                                    </div>

                                </div>

                            </div>


                            <!-- CONTATO -->

                            <div class="mb-4 mt-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-address-book me-2"></i>

                                    Contato

                                </h5>

                                <hr>

                            </div>


                            <div class="row">


                                <!-- TELEFONE -->

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
                                            placeholder="(00) 00000-0000"
                                            maxlength="30">

                                    </div>

                                </div>


                                <!-- WHATSAPP -->

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
                                            placeholder="(00) 00000-0000"
                                            maxlength="30">

                                    </div>

                                </div>


                                <!-- INSTAGRAM -->

                                <div class="col-md-6 mb-3">

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
                                            placeholder="@sualoja"
                                            maxlength="100">

                                    </div>

                                </div>


                                <!-- CIDADE -->

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Cidade
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </span>

                                        <input
                                            type="text"
                                            name="cidade"
                                            class="form-control"
                                            placeholder="Sua cidade"
                                            maxlength="100">

                                    </div>

                                </div>


                                <!-- ESTADO -->

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Estado
                                    </label>

                                    <input
                                        type="text"
                                        name="estado"
                                        class="form-control"
                                        placeholder="RS"
                                        maxlength="2">

                                </div>


                                <!-- ENDEREÇO -->

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Endereço
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </span>

                                        <input
                                            type="text"
                                            name="endereco"
                                            class="form-control"
                                            placeholder="Rua, número, bairro..."
                                            maxlength="255">

                                    </div>

                                </div>

                            </div>


                            <!-- DESCRIÇÃO -->

                            <div class="mb-4 mt-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="fa-solid fa-align-left me-2"></i>

                                    Sobre a loja

                                </h5>

                                <hr>

                            </div>


                            <div class="mb-4">

                                <label class="form-label fw-bold">
                                    Descrição da Loja
                                </label>

                                <textarea
                                    name="descricao"
                                    rows="4"
                                    class="form-control"
                                    maxlength="1000"
                                    placeholder="Conte um pouco sobre sua loja, produtos ou serviços..."></textarea>

                            </div>


                            <!-- BOTÃO -->

                            <button
                                type="submit"
                                class="btn btn-gradient btn-lg w-100">

                                <i class="fa-solid fa-store me-2"></i>

                                Cadastrar Loja

                            </button>


                        </form>


                        <!-- LOGIN -->

                        <div class="text-center mt-4">

                            <p class="text-muted mb-2">
                                Já possui uma conta?
                            </p>

                            <a
                                href="login.php"
                                class="fw-bold text-decoration-none">

                                Entrar na minha loja

                                <i class="fa-solid fa-arrow-right ms-1"></i>

                            </a>

                        </div>


                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<?php include 'includes_site/footer.php'; ?>


</body>

</html>