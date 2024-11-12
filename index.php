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
        <form id="form-login">
            <section id="login">
                <!-- logotipo Natura -->
                <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
                <br>
                <br>
                <h2>SISTEMA DE CADASTRO</h2>
                <h3>Bem-vindo administrador(a)</h3>
                <div class="container-cadastro">
                    <a href="Cadastrodeprodutos.php" class="item">Cadastro de produtos</a>
                    <a href="Cadastrodefornecedores.php" class="item">Cadastro de fornecedores</a>
                    <a href="listagemdeprodutos.php" class="item">Listagem de produtos</a>
                </div>
                <a href="login.php" class="sessao-login-btn-sair">Voltar</a>
            </section>
        </form>
    </div>

    <!-- PHP para Conexão e Leitura -->
    <div>
        <?php
        include 'conexao.php';
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");

        if ($result->num_rows > 0) {
            include 'read.php'; 
        } else {
            echo "<p style='color:red;'>Erro: A tabela 'usuarios' não existe no banco de dados.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
