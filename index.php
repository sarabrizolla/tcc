<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

        <title> INICIO | Site de Vendas </title>
    </head>
    <body>
        <!-- MENU SUPERIOR INICIA AQUI! -->
        <nav class="navbar navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
          <div class="container-fluid">
            <a class="navbar-brand" href="#"> Site de vendas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="index.php"> Início </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link active" href="loja.php"> Lojas </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link active" href="contato.php"> Contato </a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Categorias
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="jogos_acao.php">Ação</a></li>
                    <li><a class="dropdown-item" href="#">Corridas</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Esportes</a></li>
                  </ul>
                </li>
              </ul>
             
              <a href="login/">
                <button type="button" class="btn btn-success"> PAINEL ADMINISTRATIVO </button>
              </a>
              
            </div>
          </div>
        </nav>
        <!-- E TERMINA AQUI! -->

        <h1> ESSA É A PÁGINA DE INÍCIO </h1>
    </body>
</html>