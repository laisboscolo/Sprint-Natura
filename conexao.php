<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natura";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Não feche a conexão logo após criá-la. A conexão será fechada no final do script.
?>
