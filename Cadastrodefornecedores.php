<?php
// Inclui o arquivo que valida a sessão do usuário
include('valida_sessao.php');
// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

// Função para redimensionar e salvar a imagem
function redimensionarESalvarImagem($arquivo, $largura = 80, $altura = 80) {
    // Verifica se o arquivo foi enviado sem erro
    if ($arquivo['error'] !== 0) {
        return "Erro no envio da imagem.";
    }

    // Verifica o tipo do arquivo
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($extensao, $tipos_permitidos)) {
        return "Extensão de imagem não permitida. Apenas jpg, jpeg, png e gif são aceitos.";
    }

    // Define o diretório para onde a imagem será salva
    $diretorio = 'img/uploads/';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Cria um nome único para o arquivo
    $novo_nome = uniqid('img_', true) . '.' . $extensao;
    $caminho_imagem = $diretorio . $novo_nome;

    // Faz o upload da imagem
    if (move_uploaded_file($arquivo['tmp_name'], $caminho_imagem)) {
        // Aqui podemos redimensionar a imagem, se necessário
        if (in_array($extensao, ['jpg', 'jpeg'])) {
            $imagem = imagecreatefromjpeg($caminho_imagem);
        } elseif ($extensao == 'png') {
            $imagem = imagecreatefrompng($caminho_imagem);
        } elseif ($extensao == 'gif') {
            $imagem = imagecreatefromgif($caminho_imagem);
        }

        // Redimensiona a imagem
        $imagem_redimensionada = imagecreatetruecolor($largura, $altura);
        imagecopyresampled($imagem_redimensionada, $imagem, 0, 0, 0, 0, $largura, $altura, imagesx($imagem), imagesy($imagem));

        // Salva a imagem redimensionada
        if (in_array($extensao, ['jpg', 'jpeg'])) {
            imagejpeg($imagem_redimensionada, $caminho_imagem);
        } elseif ($extensao == 'png') {
            imagepng($imagem_redimensionada, $caminho_imagem);
        } elseif ($extensao == 'gif') {
            imagegif($imagem_redimensionada, $caminho_imagem);
        }

        // Libera a memória
        imagedestroy($imagem);
        imagedestroy($imagem_redimensionada);

        return 'img/uploads/' . $novo_nome; // Retorna o caminho relativo da imagem
    } else {
        return "Falha ao mover o arquivo para o diretório de destino.";
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    // Processa o upload da imagem
    $imagem = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $resultado_upload = redimensionarESalvarImagem($_FILES['imagem']);
        if (strpos($resultado_upload, 'img/') === 0) {
            $imagem = $resultado_upload;
        } else {
            $mensagem_erro = $resultado_upload;
        }
    }

    // Prepara a query SQL para inserção ou atualização
    if ($id) {
        // Se o ID existe, é uma atualização
        $sql = "UPDATE fornecedores SET nome='$nome', email='$email', telefone='$telefone'";
        if ($imagem) {
            $sql .= ", imagem='$imagem'";
        }
        $sql .= " WHERE id='$id'";
        $mensagem = "Fornecedor atualizado com sucesso!";
    } else {
        // Se não há ID, é uma nova inserção
        $sql = "INSERT INTO fornecedores (nome, email, telefone, imagem) VALUES ('$nome', '$email', '$telefone', '$imagem')";
        $mensagem = "Fornecedor cadastrado com sucesso!";
    }

    // Executa a query e verifica se houve erro
    if ($conn->query($sql) !== TRUE) {
        $mensagem = "Erro: " . $conn->error;
    }
}

// Verifica se foi solicitada a exclusão de um fornecedor
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Verifica se o fornecedor tem produtos cadastrados
    $check_produtos = $conn->query("SELECT COUNT(*) as count FROM produtos WHERE fornecedor_id = '$delete_id'")->fetch_assoc();
    
    if ($check_produtos['count'] > 0) {
        $mensagem = "Não é possível excluir este fornecedor pois existem produtos cadastrados para ele.";
    } else {
        $sql = "DELETE FROM fornecedores WHERE id='$delete_id'";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Fornecedor excluído com sucesso!";
        } else {
            $mensagem = "Erro ao excluir fornecedor: " . $conn->error;
        }
    }
}

// Busca todos os fornecedores para listar na tabela
$fornecedores = $conn->query("SELECT * FROM fornecedores");

// Se foi solicitada a edição de um fornecedor, busca os dados dele
$fornecedor = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $fornecedor = $conn->query("SELECT * FROM fornecedores WHERE id='$edit_id'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Cadastro de fornecedores</title>
</head>
<body>

    <!-- Barra de navegação superior -->
    <header><nav class="navbar">
        <img class="logotipo" src="img/natura-branco.png" alt="logotipo">
        <span class="navbar-brand">Bem-vindo, <?php echo $_SESSION['usuario']; ?>!</span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </nav></header>

    <div id="container">
        <h2>Cadastro de Fornecedor</h2>
        <!-- Formulário para cadastro/edição de fornecedor -->
        <form method="post" action="" enctype="multipart/form-data">
            <!-- Hidden field for the ID of the supplier -->
            <input type="hidden" name="id" value="<?php echo $fornecedor['id'] ?? ''; ?>">
            
            <!-- Name Input -->
            <div class="input-container">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo $fornecedor['nome'] ?? ''; ?>" required>
            </div>

            <!-- Email Input -->
            <div class="input-container">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $fornecedor['email'] ?? ''; ?>" required>
            </div>

            <!-- Phone Input -->
            <div class="input-container">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" value="<?php echo $fornecedor['telefone'] ?? ''; ?>">
            </div>

            <!-- Image Upload -->
            <div class="input-container" id="cadastro">
                <label for="imagem">Imagem:</label>
                <input type="file" name="imagem" id="imagem" accept="image/*">
                
                <?php if (isset($fornecedor['imagem']) && $fornecedor['imagem']): ?>
                    <div class="current-image">
                        <img src="<?php echo $fornecedor['imagem']; ?>" alt="Imagem atual do fornecedor" class="update-image">
                        <p>Imagem atual do fornecedor</p>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit"><?php echo $fornecedor ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>

        <!-- Exibe mensagens de sucesso ou erro -->
        <?php
        if (isset($mensagem)) echo "<p class='message " . (strpos($mensagem, 'Erro') !== false ? "error" : "success") . "'>$mensagem</p>";
        if (isset($mensagem_erro)) echo "<p class='message error'>$mensagem_erro</p>";
        ?>
    </div>

    <div id="container-listagem-for"><h2 class="listagem-cadastro">Listagem de Fornecedores</h2>
        <!-- Tabela para listar os fornecedores cadastrados -->
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $fornecedores->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telefone']; ?></td>
                <td>
                    <?php if ($row['imagem']): ?>
                        <img src="<?php echo $row['imagem']; ?>" alt="Imagem do fornecedor" class="thumbnail">
                    <?php else: ?>
                        Sem imagem
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit_id=<?php echo $row['id']; ?>">Editar</a>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="btn-actions">
        <a href="index.php" class="sessao-login-btn">Voltar</a>
    </div>
</body>
</html>
