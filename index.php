<?php include('valida_sessao.php'); ?>
<!-- Inclui o arquivo 'valida_sessao.php' para garantir que o usuário esteja autenticado -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reset.css">
    <!-- link do favicon-->
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Natura</title>
</head>
<body>   
    <div id="border-box">
        <section id="login">
            <!-- logotipo Natura -->
            <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
            <br><br>

            <!-- Títulos -->
            <h1>SISTEMA DE CADASTRO</h1>
            <h3>Bem-vindo administrador(a)</h3>
            
            <div class="container-cadastro">
                <!-- Links para as funcionalidades do sistema -->
                <a href="Cadastrodeprodutos.php" class="item">Cadastro de produtos</a>
                <a href="Cadastrodefornecedores.php" class="item">Cadastro de fornecedores</a>
                <a href="listagemdeprodutos.php" class="item">Listagem de produtos</a>
            </div>
            
            <!-- Link para sair e voltar à página de login -->
            <a href="login.php" class="sessao-login-btn-sair">Voltar</a>
        </section>
    </div>
</body>
</html>
