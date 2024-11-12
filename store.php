<?php

include 'conexao.php'; //variavel

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Verifica se o formulário foi enviado
    $nome = $_POST['nome']; // Recebe o nome
    $email = $_POST['email']; // Recebe o email
    $password = $_POST['senha']; // Recebe a senha
    $sql = "INSERT INTO usuarios (nome, email) VALUES ('$nome', '$email')"; // Prepara a consulta

    // Executa a consulta e verifica se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); // Redireciona para a página principal
    } else {
        echo "Erro: " . $conn->error; // Mostra erro, se houver
    }
}


?>