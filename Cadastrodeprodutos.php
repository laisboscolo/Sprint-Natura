<?php
// Inclui o arquivo que valida a sessão do usuário
include('valida_sessao.php');
// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

// Função para redimensionar e salvar a imagem
function redimensionarESalvarImagem($arquivo, $largura = 80, $altura = 80) {
    $diretorio_destino = "img/";
    if (!file_exists($diretorio_destino)) {
        mkdir($diretorio_destino, 0777, true);
    }
    $nome_arquivo = uniqid() . '_' . basename($arquivo["name"]);
    $caminho_completo = $diretorio_destino . $nome_arquivo;
    $tipo_arquivo = strtolower(pathinfo($caminho_completo, PATHINFO_EXTENSION));

    // Verifica se é uma imagem válida
    $check = getimagesize($arquivo["tmp_name"]);
    if ($check === false) {
        return "O arquivo não é uma imagem válida.";
    }

    // Verifica o tamanho do arquivo (limite de 5MB)
    if ($arquivo["size"] > 5000000) {
        return "O arquivo é muito grande. O tamanho máximo permitido é 5MB.";
    }

    // Permite apenas alguns formatos de arquivo
    if (!in_array($tipo_arquivo, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
    }

    // Cria uma nova imagem a partir do arquivo enviado
    switch ($tipo_arquivo) {
        case 'jpg':
        case 'jpeg':
            $imagem_original = imagecreatefromjpeg($arquivo["tmp_name"]);
            break;
        case 'png':
            $imagem_original = imagecreatefrompng($arquivo["tmp_name"]);
            break;
        case 'gif':
            $imagem_original = imagecreatefromgif($arquivo["tmp_name"]);
            break;
        default:
            return "Tipo de imagem não suportado.";
    }

    // Obtém as dimensões originais da imagem
    $largura_original = imagesx($imagem_original);
    $altura_original = imagesy($imagem_original);

    // Calcula as novas dimensões mantendo a proporção
    $ratio = min($largura / $largura_original, $altura / $altura_original);
    $nova_largura = $largura_original * $ratio;
    $nova_altura = $altura_original * $ratio;

    // Cria uma nova imagem com as dimensões calculadas
    $nova_imagem = imagecreatetruecolor($nova_largura, $nova_altura);

    // Redimensiona a imagem original para a nova imagem
    imagecopyresampled($nova_imagem, $imagem_original, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

    // Salva a nova imagem
    switch ($tipo_arquivo) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($nova_imagem, $caminho_completo, 90);
            break;
        case 'png':
            imagepng($nova_imagem, $caminho_completo);
            break;
        case 'gif':
            imagegif($nova_imagem, $caminho_completo);
            break;
    }

    // Libera a memória
    imagedestroy($imagem_original);
    imagedestroy($nova_imagem);

    return $caminho_completo;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $fornecedor_id = $_POST['fornecedor_id_produto'];
    $nome = $_POST['nome_produto'];
    $descricao = $_POST['descricao_produto'];
    $preco = str_replace(',', '.', $_POST['preco_produto']); // Converte vírgula para ponto

    // Processa o upload da imagem
    $imagem = "";
    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] == 0) {
        $resultado_upload = redimensionarESalvarImagem($_FILES['imagem_produto']);
        if (strpos($resultado_upload, 'img/') === 0) {
            $imagem = $resultado_upload;
        } else {
            $mensagem_erro = $resultado_upload;
        }
    }

    // Prepara a query SQL para inserção ou atualização
    if ($id) {
        // Se o ID existe, é uma atualização
        $sql = "UPDATE produtos SET fornecedor_id=?, nome=?, descricao=?, preco=?";
        $params = [$fornecedor_id, $nome, $descricao, $preco];
        if ($imagem) {
            $sql .= ", imagem=?";
            $params[] = $imagem;
        }
        $sql .= " WHERE id=?";
        $params[] = $id;

        // Prepara e executa a query
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
        $mensagem = "Produto atualizado com sucesso!";
    } else {
        // Se não há ID, é uma nova inserção
        $sql = "INSERT INTO produtos (fornecedor_id, nome, descricao, preco, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $fornecedor_id, $nome, $descricao, $preco, $imagem);
        $mensagem = "Produto cadastrado com sucesso!";
    }

    // Executa a query e verifica se houve erro
    if ($stmt->execute()) {
        $class = "success";
    } else {
        $mensagem = "Erro: " . $stmt->error;
        $class = "error";
    }
}

// Verifica se foi solicitada a exclusão de um produto
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM produtos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $mensagem = "Produto excluído com sucesso!";
        $class = "success";
    } else {
        $mensagem = "Erro ao excluir produto: " . $stmt->error;
        $class = "error";
    }
}

// Busca todos os produtos para listar na tabela
$produtos = $conn->query("SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, f.nome AS fornecedor_nome FROM produtos p JOIN fornecedores f ON p.fornecedor_id = f.id");

// Se foi solicitada a edição de um produto, busca os dados dele
$produto = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produto = $result->fetch_assoc();
    $stmt->close();
}

// Função para listar fornecedores
function listar_fornecedores($conn) {
    return $conn->query("SELECT id, nome FROM fornecedores");
}

// Verifica se existe uma mensagem para exibir
$mensagem = isset($mensagem) ? $mensagem : '';
$class = isset($class) ? $class : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
    <link rel="shortcut icon" href="img/natura-108.png">
    <title>Cadastro de Produtos</title>
</head>
<body>
    <!-- Barra de navegação superior -->
    <header><nav class="navbar">
        <img class="logotipo" src="img/natura-branco.png" alt="logotipo">
        <span class="navbar-brand">Bem-vindo, <?php echo $_SESSION['usuario']; ?>!</span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </nav></header>
    <div class="container" style="width: 900px;">

        <?php if (!empty($mensagem)): ?>
            <div class="<?= htmlspecialchars($class); ?>"><?= htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div id="container">
            <h2>Cadastro de Produto</h2>
            <!-- Formulário para cadastro/edição de produto -->
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= isset($produto['id']) ? htmlspecialchars($produto['id']) : ''; ?>">

                <div class="input-container2">
                    <label for="fornecedor_id_produto">Fornecedor:</label>
                    <select name="fornecedor_id_produto" required>
                        <option value="">Selecione o fornecedor</option>
                        <?php 
                        $result_fornecedores = listar_fornecedores($conn);
                        while ($fornecedor = $result_fornecedores->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($fornecedor['id']); ?>" <?= isset($produto['fornecedor_id']) && $produto['fornecedor_id'] == $fornecedor['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($fornecedor['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-container2">
                    <label for="nome_produto">Nome do Produto:</label>
                    <input type="text" name="nome_produto" value="<?= isset($produto['nome']) ? htmlspecialchars($produto['nome']) : ''; ?>" required>
                </div>

                <div class="input-container2">
                    <label for="descricao_produto">Descrição:</label>
                    <textarea name="descricao_produto" required><?= isset($produto['descricao']) ? htmlspecialchars($produto['descricao']) : ''; ?></textarea>
                </div>

                <div class="input-container2">
                    <label for="preco_produto">Preço:</label>
                    <input type="text" name="preco_produto" value="<?= isset($produto['preco']) ? number_format($produto['preco'], 2, ',', '.') : ''; ?>" required>
                </div>

                <div class="input-container2">
                    <label for="imagem_produto">Imagem:</label>
                    <input type="file" name="imagem_produto">
                </div>

                <button type="submit">Cadastrar</button>
            </form>
        </div>

        <div id="container-listagem-for">
            <h2 class="listagem-cadastro">Listagem de Produtos</h2>
            <!-- Tabela para listar os produtos cadastrados -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Fornecedor</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $produtos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo $row['descricao']; ?></td>
                        <td><?php echo 'R$ ' . number_format($row['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $row['fornecedor_nome']; ?></td>
                        <td>
                            <?php if ($row['imagem']): ?>
                                <img src="<?php echo $row['imagem']; ?>" alt="Imagem do produto" class="thumbnail">
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
                </tbody>
            </table>
        </div>
    </div>

    <div class="btn-actions">
        <a href="index.php" class="sessao-login-btn">Voltar</a>
    </div>

</body>
</html>
