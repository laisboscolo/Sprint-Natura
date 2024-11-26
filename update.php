<?php
// Conexão com o banco de dados
include 'conexao.php';

if (isset($_GET['id'])) {
    $id_fornecedor = $_GET['id'];
    
    // Recuperando os dados do fornecedor a ser editado
    $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_fornecedor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Fornecedor não encontrado!";
            exit();
        }
        
        $stmt->close();
    }
} else {
    echo "ID não especificado!";
    exit();
}

$conn->close();
?>

<!-- Formulário para editar fornecedor -->
<form action="editar.php" method="POST">
    <input type="hidden" name="id_fornecedor" value="<?php echo $row['id_fornecedor']; ?>">

    <label for="nome_fornecedor">Nome:</label>
    <input type="text" name="nome_fornecedor" value="<?php echo $row['nome_fornecedor']; ?>" required><br><br>

    <label for="email_fornecedor">Email:</label>
    <input type="email" name="email_fornecedor" value="<?php echo $row['email_fornecedor']; ?>" required><br><br>

    <label for="telefone_fornecedor">Telefone:</label>
    <input type="text" name="telefone_fornecedor" value="<?php echo $row['telefone_fornecedor']; ?>" required><br><br>

    <button type="submit" name="update">Atualizar</button>
</form>

<?php
// Processa o envio do formulário para atualizar o fornecedor
if (isset($_POST['update'])) {
    $id_fornecedor = $_POST['id_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $email_fornecedor = $_POST['email_fornecedor'];
    $telefone_fornecedor = $_POST['telefone_fornecedor'];

    // Atualizando os dados do fornecedor
    $sql = "UPDATE fornecedor SET nome_fornecedor='$nome_fornecedor', email_fornecedor='$email_fornecedor', telefone_fornecedor='$telefone_fornecedor' WHERE id_fornecedor=$id_fornecedor";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $nome_fornecedor, $email_fornecedor, $telefone_fornecedor, $id_fornecedor);
        
        if ($stmt->execute()) {
            echo "Fornecedor atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar fornecedor: " . $conn->error;
        }
        
        $stmt->close();
    }

    $conn->close();
}
?>
