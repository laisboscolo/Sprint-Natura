<?php

include 'conexao.php'; //variavel
// Conexão com o banco de dados (ajuste os dados conforme o seu banco)
$servername = "localhost";  // ou seu servidor de banco de dados
$username = "root";         // seu nome de usuário do MySQL
$password = "";             // sua senha do MySQL
$dbname = "natura"; // nome do seu banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Buscando os fornecedores e produtos cadastrados
$sql_fornecedor = "SELECT id_fornecedor, nome_fornecedor, email_fornecedor, telefone_fornecedor FROM fornecedor";
$sql_produto = "SELECT p.id_produto, p.nome_produto, p.descricao_produto, p.valor_produto, f.nome_fornecedor 
                 FROM produto p 
                 LEFT JOIN fornecedor f ON p.id_fornecedor = f.id_fornecedor"; // Corrigido o nome da tabela de "fornecedores" para "fornecedor"

// Executando as consultas
$result_fornecedor = $conn->query($sql_fornecedor);
$result_produto = $conn->query($sql_produto);

?>

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
    <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
    <br>
    
    <!-- Tabela de fornecedores -->
    <h2>Fornecedores Cadastrados</h2>
    <table border="3" class="tabela-listagem">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo os fornecedores cadastrados
            if ($result_fornecedor->num_rows > 0) {
                while($row = $result_fornecedor->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_fornecedor'] . "</td>";
                    echo "<td>" . $row['nome_fornecedor'] . "</td>";
                    echo "<td>" . $row['email_fornecedor'] . "</td>";
                    echo "<td>" . $row['telefone_fornecedor'] . "</td>";
                    echo "<td class='actions'>
                            <input type='button' value='Editar' onclick=\"alert('Editar fornecedor " . $row['id_fornecedor'] . "')\">
                            <input type='button' value='Excluir' onclick=\"alert('Excluir fornecedor " . $row['id_fornecedor'] . "')\">
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum fornecedor cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br><br>

    <!-- Tabela de produtos -->
    <h2>Produtos Cadastrados</h2>
    <table border="3" class="tabela-listagem">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibindo os produtos cadastrados
            if ($result_produto->num_rows > 0) {
                while($row = $result_produto->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_produto'] . "</td>";
                    echo "<td>" . $row['nome_produto'] . "</td>";
                    echo "<td>" . $row['descricao_produto'] . "</td>";
                    echo "<td>R$ " . number_format($row['valor_produto'], 2, ',', '.') . "</td>";
                    echo "<td>" . $row['nome_fornecedor'] . "</td>";
                    echo "<td class='actions'>
                            <input type='button' value='Editar' onclick=\"alert('Editar produto " . $row['id_produto'] . "')\">
                            <input type='button' value='Excluir' onclick=\"alert('Excluir produto " . $row['id_produto'] . "')\">
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php $conn->close(); ?>
</body>   
</html>
