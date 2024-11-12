<?php
include 'conexao.php'; // Inclui o arquivo de conexão

if (isset($_GET['id'])) { // Verifica se o ID foi passado
    $id = $_GET['id']; // Recebe o ID
    $sql = "SELECT * FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // Bind do ID como inteiro
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc(); // Obtém os dados do usuário

    // Verifica se o usuário existe
    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit();
    }
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = trim($_POST['senha']);

    // Validação simples de campos
    if (empty($nome) || empty($email)) {
        echo "Nome e e-mail são obrigatórios.";
        exit();
    }

    // Se a senha foi fornecida, criptografa antes de atualizar
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        // Atualiza a senha no banco de dados, junto com o nome e email
        $sql = "UPDATE usuarios SET nome=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $email, $password, $id); // Bind de parâmetros com a senha
    } else {
        // Se a senha não foi fornecida, não altere o campo de senha
        $sql = "UPDATE usuarios SET nome=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $id); // Bind sem senha
    }

    // Executa a query e verifica se a atualização foi bem-sucedida
    if ($stmt->execute()) {
        header("Location: index.php"); // Redireciona se a atualização for bem-sucedida
        exit(); // Certifica-se de que o código não continua executando
    } else {
        echo "Erro: " . $stmt->error; // Mostra erro, se houver
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Atualizar Usuário</title>
</head>
<body>
    <h1>Atualizar Usuário</h1>
    <form action="" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
        
        <label>Senha:</label>
        <input type="password" name="senha" placeholder="Nova Senha">
        
        <input type="submit" value="Atualizar">
    </form>
    
    <a href="index.php">Cancelar</a> <!-- Link para voltar -->
</body>
</html>