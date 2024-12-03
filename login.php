<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);

    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND senha='$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['usuario'] = $usuario;
        header('Location: index.php');
    } else {
        $error = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Natura</title>
</head>
<body>
  <div id="border-box">
    <form id="form-login" method="POST">
        <section id="login">                    

            <div class="container-login">
                <img class="logo-natura" src="img/natura-logo.png" alt="Logo Natura">
                <h2 class="login-texto">Usuário</h2>
                <input type="text" name="usuario" placeholder="E-mail ou Número" required>
                
                <h2 class="login-texto">Senha</h2>
                <input type="password" name="senha" placeholder="Digite sua senha" required>

                <!-- Botão de envio -->
                <button type="submit" class="sessao-login-btn">Entrar</button>

                <!-- Exibir erro, se existir -->
                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>

            </div>
        </section>
    </form>
  </div>
</body>
</html>
