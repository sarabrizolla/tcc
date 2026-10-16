<?php

session_start();

include 'conexao.php';

$erro = "";


/* =========================
   TOKEN CSRF
========================= */

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );

}


/* =========================
   PROCESSAMENTO DO LOGIN
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    /* =========================
       VERIFICA TOKEN CSRF
    ========================= */

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {

        $erro = "Não foi possível processar o login. Tente novamente.";

    } else {


        /* =========================
           RECEBE OS DADOS
        ========================= */

        $email = trim($_POST['email'] ?? "");
        $senha = $_POST['senha'] ?? "";


        /* =========================
           VALIDAÇÕES
        ========================= */

        if ($email === "" || $senha === "") {

            $erro = "E-mail ou senha incorretos.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $erro = "E-mail ou senha incorretos.";

        } else {


            /* =========================
               BUSCA A LOJA
            ========================= */

            $sql = "SELECT id, nome, senha
                    FROM lojas
                    WHERE email = ?
                    LIMIT 1";

            $stmt = mysqli_prepare(
                $conn,
                $sql
            );


            if (!$stmt) {

                $erro = "Não foi possível realizar o login. Tente novamente.";

            } else {

                mysqli_stmt_bind_param(
                    $stmt,
                    "s",
                    $email
                );

                mysqli_stmt_execute($stmt);

                mysqli_stmt_store_result($stmt);


                /* =========================
                   VERIFICA RESULTADO
                ========================= */

                if (mysqli_stmt_num_rows($stmt) == 1) {

                    mysqli_stmt_bind_result(
                        $stmt,
                        $id,
                        $nome,
                        $senha_hash
                    );

                    mysqli_stmt_fetch($stmt);


                    /* =========================
                       VERIFICA SENHA
                    ========================= */

                    if (
                        password_verify(
                            $senha,
                            $senha_hash
                        )
                    ) {


                        /* =========================
                           REGENERA SESSÃO
                        ========================= */

                        session_regenerate_id(true);


                        /* =========================
                           CRIA SESSÃO
                        ========================= */

                        $_SESSION['loja_id'] = $id;
                        $_SESSION['loja_nome'] = $nome;


                        /* =========================
                           NOVO TOKEN CSRF
                        ========================= */

                        $_SESSION['csrf_token'] = bin2hex(
                            random_bytes(32)
                        );


                        header(
                            "Location: admin/dashboard.php"
                        );

                        exit;


                    } else {

                        $erro = "E-mail ou senha incorretos.";

                    }


                } else {

                    $erro = "E-mail ou senha incorretos.";

                }


                mysqli_stmt_close($stmt);

            }

        }

    }

}

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>Entrar - Vitrine Digital</title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>


<section class="py-5" style="min-height: 75vh;">

    <div class="container py-4">

        <div class="row justify-content-center align-items-center">

            <div class="col-12 col-md-8 col-lg-5">

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="card-body p-4 p-md-5">


                        <!-- CABEÇALHO -->

                        <div class="text-center mb-4">

                            <span class="badge bg-primary px-3 py-2 mb-3">
                                🔐 Área da loja
                            </span>

                            <h1 class="fw-bold mb-2">
                                Bem-vindo de volta!
                            </h1>

                            <p class="text-muted mb-0">
                                Entre no painel para gerenciar sua loja.
                            </p>

                        </div>


                        <!-- ERRO -->

                        <?php if ($erro != "") { ?>

                            <div class="alert alert-danger text-center">

                                <i class="fa-solid fa-circle-exclamation me-2"></i>

                                <?php echo htmlspecialchars($erro); ?>

                            </div>

                        <?php } ?>


                        <!-- FORMULÁRIO -->

                        <form method="POST" action="login.php">


                            <!-- CSRF -->

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">


                            <!-- E-MAIL -->

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
                                        placeholder="Digite seu e-mail"
                                        maxlength="255"
                                        required>

                                </div>

                            </div>


                            <!-- SENHA -->

                            <div class="mb-4">

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
                                        placeholder="Digite sua senha"
                                        required>

                                </div>

                            </div>


                            <!-- BOTÃO -->

                            <button
                                type="submit"
                                class="btn btn-gradient btn-lg w-100">

                                <i class="fa-solid fa-right-to-bracket me-2"></i>

                                Entrar

                            </button>


                        </form>


                        <hr class="my-4">


                        <!-- CADASTRO -->

                        <div class="text-center">

                            <p class="text-muted mb-2">
                                Ainda não possui uma loja?
                            </p>

                            <a
                                href="cadastro.php"
                                class="fw-bold text-decoration-none">

                                Cadastre sua loja

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