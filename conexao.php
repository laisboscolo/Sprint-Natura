<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natura";

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Função para adicionar coluna caso não exista
function addColumnIfNotExists($conn, $table, $column, $type) {
    $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Erro ao verificar coluna em $table: " . $conn->error);
    }

    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE $table ADD COLUMN $column $type";
        if ($conn->query($sql) === false) {
            die("Erro ao adicionar coluna em $table: " . $conn->error);
        }
    }
}

// Adiciona a coluna 'imagem' na tabela 'PRODUTOS', se ela não existir
addColumnIfNotExists($conn, 'produtos', 'imagem', 'VARCHAR(255)');

// Adiciona a coluna 'imagem' na tabela 'FORNECEDORES', se ela não existir
addColumnIfNotExists($conn, 'fornecedores', 'imagem', 'VARCHAR(255)');

// Fecha a conexão
$conn->close();
?>
