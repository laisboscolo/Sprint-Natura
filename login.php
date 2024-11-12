<?php
include 'conexao.php'; //variavel
    // Verificar se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        if ($email == 'email_usuario' && $senha == 'senha_usuario') {
            $mensagem = "Login bem-sucedido!";
        } else {
            $mensagem = "E-mail ou senha inválidos!";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Natura</title>
</head>
<body>
  <div id="border-box">
    <form id="form-login" method="POST"> <!-- Alterei para POST para enviar os dados -->
        <section id="login">
            <!-- Logotipo Natura -->
            <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
            <br><br>

            <div id="container">
                <h2>Usuário</h2>
                <input type="email" name="email" placeholder="E-mail ou Número" required>
                
                <h2>Senha</h2>
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                
                <a href="index.php" class="sessao-login-btn">Entrar</a>

                </form>
                
                <a href="https://support.microsoft.com/pt-br/account-billing/redefinir-uma-senha-esquecida-de-conta-microsoft-eff4f067-5042-c1a3-fe72-b04d60556c37">Esqueceu a senha?</a>
            </div>

            <?php
            // Exibir mensagem se o login falhar ou for bem-sucedido
            if (isset($mensagem)) {
                echo "<div class='mensagem'>$mensagem</div>";
            }
            ?>
        </section>
    </form>
  </div>
</body>
</html>
