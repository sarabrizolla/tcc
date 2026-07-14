<?php

include 'conexao.php';

if(isset($_POST['nome'])){

    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = $_POST['telefone'];
    $whatsapp = $_POST['whatsapp'];
    $instagram = $_POST['instagram'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $endereco = $_POST['endereco'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO lojas
    (nome, email, senha, telefone, instagram, endereco, categoria, descricao, cidade, estado, whatsapp)
    VALUES
    ('$nome','$email','$senha','$telefone','$instagram','$endereco','$categoria','$descricao','$cidade','$estado','$whatsapp')";

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('Loja cadastrada com sucesso!');</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar.');</script>";
    }

}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <?php include 'includes_site/head.php'; ?>

    <title>Cadastro da Loja</title>

</head>

<body>

<?php include 'includes_site/header.php'; ?>

<section class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-lg border-0 rounded-4">

                <div class="card-body p-5">

                    <h2 class="text-center mb-4">
                        Cadastre sua loja
                    </h2>

                    <form action="" method="POST">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome da Loja</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-select">
                                    <option>Moda</option>
                                    <option>Alimentação</option>
                                    <option>Beleza</option>
                                    <option>Tecnologia</option>
                                    <option>Esportes</option>
                                    <option>Casa</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="text" name="instagram" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="cidade" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" name="estado" class="form-control" maxlength="2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Endereço</label>
                                <input type="text" name="endereco" class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Descrição da Loja</label>
                                <textarea name="descricao" rows="4" class="form-control"></textarea>
                            </div>

                        </div>

                        <button class="btn btn-gradient w-100">
                            Cadastrar Loja
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include 'includes_site/footer.php'; ?>

</body>
</html>