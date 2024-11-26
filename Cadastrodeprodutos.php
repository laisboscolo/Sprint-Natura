<?php
include 'conexao.php'; // Variável para a conexão com o banco

// Conexão com o banco de dados
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "natura"; // Nome do seu banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os dados do formulário
    $fornecedor = $_POST['id_fornecedor']; 
    $nome_produto = $_POST['nome_produto'];
    $descricao_produto = $_POST['descricao_produto'];
    $valor_produto = $_POST['valor_produto'];

    // Utilizando prepared statement para evitar SQL Injection
    $stmt = $conn->prepare("INSERT INTO produto (id_fornecedor, nome_produto, descricao_produto, valor_produto) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $fornecedor, $nome_produto, $descricao_produto, $valor_produto);

    if ($stmt->execute()) {
        // Recuperando o ID do produto gerado automaticamente
        $id_produto = $conn->insert_id;

        // Exibindo os dados cadastrados
        echo "<h2>Produto cadastrado com sucesso!</h2>";
        echo "<p>ID Produto: $id_produto</p>";
        echo "<p>Fornecedor (ID): $fornecedor</p>";
        echo "<p>Nome do Produto: $nome_produto</p>";
        echo "<p>Descrição: $descricao_produto</p>";
        echo "<p>Preço: R$ $valor_produto</p>";
    } else {
        echo "Erro ao cadastrar o produto: " . $conn->error;
    }

    // Fechar o prepared statement
    $stmt->close();
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Natura</title>
</head>
<body>
  <div id="border-box">
    <form id="form-login" method="POST"> 
        <section id="login">
            <!-- Logotipo Natura -->
            <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
            <br><br>
            
            <div id="container">
                <h1 class="h1-cadastro">SISTEMA DE CADASTRO</h1>
                <p class="cadastrando">Cadastro de produtos</p>

                <!-- Formulário de cadastro de produto -->
                <form action="https://formspree.io/f/mqakyayg" method="POST">
             <h2>Fornecedor (ID)</h2>
             <input type="number" name="id_fornecedor" placeholder="ID do fornecedor" required>

    <h2>Nome do Produto</h2>
    <input type="text" name="nome_produto" placeholder="Digite o nome do produto..." required>

    <h2>Descrição</h2>
    <input type="text" name="descricao_produto" placeholder="Digite a descrição..." required>

    <h2>Preço</h2>
    <input type="number" step="0.01" name="valor_produto" placeholder="Digite o preço..." required>

    <button type="submit" class="sessao-login-btn">Cadastrar</button>

    <!-- Links de navegação -->
    <a href="Cadastrodefornecedores.php" class="listagem-nao">Não cadastrou o fornecedor? Cadastre aqui.</a>
    <a href="index.php" class="sessao-login-btn-sair">Voltar</a> 
</form>

        </section>
    </form>
  </div>
</body>
</html>
