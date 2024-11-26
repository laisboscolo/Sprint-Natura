
<?php
// Conexão com o banco de dados
include 'conexao.php'; // Inclua o arquivo de conexão

// Criando a conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natura"; // Nome do seu banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificando se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os dados do formulário e validando
    $nome_fornecedor = mysqli_real_escape_string($conn, $_POST['nome_fornecedor']);
    $email_fornecedor = mysqli_real_escape_string($conn, $_POST['email_fornecedor']);
    $telefone_fornecedor = mysqli_real_escape_string($conn, $_POST['telefone_fornecedor']);

    // Inserindo os dados na tabela fornecedor usando prepared statement
    $sql = "INSERT INTO fornecedor (nome_fornecedor, email_fornecedor, telefone_fornecedor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome_fornecedor, $email_fornecedor, $telefone_fornecedor);

    if ($stmt->execute()) {
        echo "<h2>Fornecedor cadastrado com sucesso!</h2>";
        
        // Exibindo os registros após o cadastro
        echo "<h3>Fornecedores Cadastrados:</h3>";
        $sql = "SELECT * FROM fornecedor";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id_fornecedor'] . "</td>
                        <td>" . $row['nome_fornecedor'] . "</td>
                        <td>" . $row['email_fornecedor'] . "</td>
                        <td>" . $row['telefone_fornecedor'] . "</td>
                        <td>
                            <a href=''>Editar</a> |
                            <a href=''>Excluir</a>
                        </td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Nenhum fornecedor cadastrado!</p>";
        }
        
    } else {
        echo "Erro ao cadastrar fornecedor: " . $stmt->error;
    }

    $stmt->close();
}

// Exclusão de fornecedor
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id_fornecedor = $_GET['delete'];
    
    // Proteção contra SQL Injection
    $id_fornecedor = (int)$id_fornecedor;  // Certificando que é um número inteiro
    
    // Preparando a consulta de exclusão
    $sql_delete = "DELETE FROM fornecedor WHERE id_fornecedor = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_fornecedor);

    if ($stmt_delete->execute()) {
        echo "<p>Fornecedor excluído com sucesso.</p>";
    } else {
        echo "<p>Erro ao excluir fornecedor.</p>";
    }

    $stmt_delete->close();
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
    <form id="form-login" method="POST" action="https://formspree.io/f/mqakyayg"> 
        <section id="login">
            <!-- Logotipo Natura -->
            <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
            <br><br>
            
            <div id="container">
                <h1 class="h1-cadastro">SISTEMA DE CADASTRO</h1>
                <p class="cadastrando">Cadastro de produtos</p>

                <!-- Formulário de cadastro de produto -->
                <label for="">Fornecedor (ID)</label>
                <input type="number" name="id_fornecedor" placeholder="ID do fornecedor" required>

                <label for="">Nome do Produto</label>
                <input type="text" name="nome_produto" placeholder="Digite o nome do produto..." required>

                <label for="">Descrição</label>
                <input type="text" name="descricao_produto" placeholder="Digite a descrição..." required>

                <label for="">Preço</label>
                <input type="number" step="0.01" name="valor_produto" placeholder="Digite o preço..." required>

               <label for="">email do usuario</label>
                <input type="email" name="_replyto" placeholder="Digite seu e-mail..." required>

                <!-- Opcional: Campo para redirecionamento após envio -->
                <input type="hidden" name="_next" value="http://seusite.com/obrigado">

                <!-- Opcional: Campo para assunto -->
                <input type="hidden" name="_subject" value="Novo Cadastro de Produto">

                <button type="submit" class="sessao-login-btn">Cadastrar</button>

                <!-- Links de navegação -->
                <a href="Cadastrodefornecedores.php" class="listagem-nao">Não cadastrou o fornecedor? Cadastre aqui.</a>
                <a href="index.php" class="sessao-login-btn-sair">Voltar</a> 
            </div>
        </section>
    </form>
  </div>
</body>
</html>
