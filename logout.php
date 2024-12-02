<?php
// Inicia a sessão para garantir que ela está ativa.
session_start();

// Destroi todas as variáveis da sessão, encerrando a sessão do usuário.
session_destroy();

// Redireciona o usuário para a página de login.
header('Location: login.php');

// Garante que nenhum código adicional seja executado após o redirecionamento.
exit();
?>
