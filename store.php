<?php

include 'conexao.php'; //variavel

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Verifica se o formulário foi enviado
    $nome = $_POST['nome']; 
    $email = $_POST['email']; /
    $password = $_POST['senha']; 
    $sql = "INSERT INTO usuarios (nome, email) VALUES ('$nome', '$email')"; // Prepara a consulta

    // Executa a consulta e verifica se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); 
    } else {
        echo "Erro: " . $conn->error; // Mostra erro, se houver
    }
}


?>