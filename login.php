<?php 
session_start();
include('conexao.php'); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);  // Mantendo md5

    // Protegendo contra SQL Injection usando Prepared Statements
    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND senha = ?";
    // Preparando a consulta
    $stmt = $conn->prepare($sql);

    if ($stmt) {  // Verifica se a preparação foi bem-sucedida
        $stmt->bind_param("ss", $usuario, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['usuario'] = $usuario;
            header('Location: index.php');
            exit();
        } else {
            $error = "Usuário ou senha inválidos.";
        }

        $stmt->close();
    } else {
        // Caso não consiga preparar a consulta
        $error = "Erro ao preparar a consulta.";
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
    <form id="form-login" method="POST">
        <section id="login">
            <!-- Logotipo Natura -->
            <img class="logo-natura" src="img/natura-branco.png" alt="Logo Natura">
            <br><br>

            <div id="container">
                <h2>Usuário</h2>
                <input type="text" name="usuario" placeholder="E-mail ou Número" required>
                
                <h2>Senha</h2>
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
