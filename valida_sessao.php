<?php
// Inicia a sessão para verificar se o usuário está autenticado.
session_start();

// Verifica se a variável de sessão 'usuario' não está definida.
if (!isset($_SESSION['usuario'])) {
    // Se o usuário não estiver logado, redireciona para a página de login.
    header('Location: login.php');
    
    // Garante que o restante do código não seja executado após o redirecionamento.
    exit();
}
?>
