<?php 
include('valida_sessao.php');  // Inclui o arquivo que verifica se o usuário tem uma sessão válida.
include('conexao.php');         // Inclui o arquivo que faz a conexão com o banco de dados.

// Verifica se foi passado um ID para exclusão via GET
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id']; // Recebe o ID do produto que será excluído
    $sql = "DELETE FROM produtos WHERE id = '$delete_id'"; // Cria a query SQL para deletar o produto com o ID fornecido
    if ($conn->query($sql) === TRUE) { // Executa a query
        $mensagem = "Produto excluído com sucesso!"; // Se a exclusão for bem-sucedida, define a mensagem de sucesso
    } else {
        $mensagem = "Erro ao excluir produto: " . $conn->error; // Caso ocorra um erro, define a mensagem de erro com a descrição do erro
    }
}

// Consulta os fornecedores
$result_fornecedor = $conn->query("SELECT id AS id_fornecedor, nome AS nome_fornecedor, email AS email_fornecedor, telefone AS telefone_fornecedor FROM fornecedores");

// Consulta os produtos
$result_produto = $conn->query("SELECT p.id AS id_produto, p.nome AS nome_produto, p.descricao AS descricao_produto, p.preco AS valor_produto, f.nome AS nome_fornecedor FROM produtos p JOIN fornecedores f ON p.fornecedor_id = f.id");
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
