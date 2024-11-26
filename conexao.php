<?php
    $servername = "localhost"; // Acessa o servidor
    $username = "root"; // Usuário
    $password = ""; // Senha
    $dbname = "natura"; // Nome do banco de dados

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo "Erro de conexão: " . $conn->connect_error;
 }   
    else {
        echo "";
 }

?>
