<?php
// Conexão com o banco de dados
include 'conexao.php';

if (isset($_GET['id'])) {
    $id_fornecedor = $_GET['id'];

    // Excluindo o fornecedor da tabela
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor=$id_fornecedor";
    
    // Prepara e executa a query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_fornecedor);
        if ($stmt->execute()) {
            echo "Fornecedor excluído com sucesso!";
        } else {
            echo "Erro ao excluir fornecedor: " . $conn->error;
        }
        $stmt->close();
    }

    // Redireciona para evitar que o link de exclusão seja reexecutado ao recarregar a página
    header("Location: index.php");
    exit();
}

$conn->close();
?>
