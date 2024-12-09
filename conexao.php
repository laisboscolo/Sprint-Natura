<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natura2";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Adiciona a coluna 'imagem' à tabela 'PRODUTOS' se ela não existir
$sql = "SHOW COLUMNS FROM produtos LIKE 'imagem'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
$sql = "ALTER TABLE produtos ADD COLUMN imagem VARCHAR(255)";
$conn->query($sql);
}

// Adiciona a coluna 'imagem' à tabela 'FORNECEDORES' se ela não existir
$sql = "SHOW COLUMNS FROM fornecedores LIKE 'imagem'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
$sql = "ALTER TABLE fornecedores ADD COLUMN imagem VARCHAR(255)";
$conn->query($sql);
}
?>