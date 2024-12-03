<?php include('valida_sessao.php'); ?>
<!-- Inclui o arquivo 'valida_sessao.php' para garantir que o usuário esteja autenticado -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/reset.css">
    <!-- link do favicon-->
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Natura</title>
</head>
<body>   

    <!-- Barra de navegação superior -->
    <nav class="navbar">
        <img class="logotipo" src="img/natura-branco.png" alt="logotipo">
        <span class="navbar-brand">Bem-vindo, <?php echo $_SESSION['usuario']; ?>!</span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </nav>

  <section>  <div id="border-box">
        <section id="login">
            <!-- logotipo Natura -->
            <!-- <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura"> -->
            

            <div id="container" class="cadastro">
                <!-- Títulos -->
                <h1>SISTEMA DE CADASTRO</h1>                
                
                <div class="container-cadastro">
                    <!-- Links para as funcionalidades do sistema -->
                    <a href="Cadastrodefornecedores.php" class="btn-itens">Cadastro de fornecedores</a>
                    <a href="Cadastrodeprodutos.php" class="btn-itens">Cadastro de produtos</a>
                    <a href="listagemdeprodutos.php" class="btn-itens">Listagem de produtos</a>

        </section>
    </div>
</body>
</html>
