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
    if($check === false) {
        return "O arquivo não é uma imagem válida.";
    }

    // Verifica o tamanho do arquivo (limite de 5MB)
    if ($arquivo["size"] > 5000000) {
        return "O arquivo é muito grande. O tamanho máximo permitido é 5MB.";
    }

    // Permite apenas alguns formatos de arquivo
    if($tipo_arquivo != "jpg" && $tipo_arquivo != "png" && $tipo_arquivo != "jpeg" && $tipo_arquivo != "gif" ) {
        return "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
    }

    // Cria uma nova imagem a partir do arquivo enviado
    if ($tipo_arquivo == "jpg" || $tipo_arquivo == "jpeg") {
        $imagem_original = imagecreatefromjpeg($arquivo["tmp_name"]);
    } elseif ($tipo_arquivo == "png") {
        $imagem_original = imagecreatefrompng($arquivo["tmp_name"]);
    } elseif ($tipo_arquivo == "gif") {
        $imagem_original = imagecreatefromgif($arquivo["tmp_name"]);
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
    if ($tipo_arquivo == "jpg" || $tipo_arquivo == "jpeg") {
        imagejpeg($nova_imagem, $caminho_completo, 90);
    } elseif ($tipo_arquivo == "png") {
        imagepng($nova_imagem, $caminho_completo);
    } elseif ($tipo_arquivo == "gif") {
        imagegif($nova_imagem, $caminho_completo);
    }

    // Libera a memória
    imagedestroy($imagem_original);
    imagedestroy($nova_imagem);

    return $caminho_completo;
}

// Verifica se o formulário de fornecedor foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se é para cadastrar ou atualizar fornecedor
    if (isset($_POST['fornecedor_id'])) {
        $fornecedor_id = $_POST['fornecedor_id'];
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

        // Se o ID do fornecedor existir, é uma atualização
        if ($fornecedor_id) {
            $sql = "UPDATE fornecedores SET nome=?, email=?, telefone=?";
            $params = [$nome, $email, $telefone];
            if ($imagem) {
                $sql .= ", imagem=?";
                $params[] = $imagem;
            }
            $sql .= " WHERE id=?";
            $params[] = $fornecedor_id;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $mensagem = "Fornecedor atualizado com sucesso!";
        } else {
            // Se não houver ID, é uma nova inserção
            $sql = "INSERT INTO fornecedores (nome, email, telefone, imagem) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $telefone, $imagem);
            $mensagem = "Fornecedor cadastrado com sucesso!";
        }

        if ($stmt->execute()) {
            $class = "success";
        } else {
            $mensagem = "Erro: " . $stmt->error;
            $class = "error";
        }
    }

    // Verifica se o formulário de produto foi enviado
    $produto_id = $_POST['id'] ?? '';
    $fornecedor_id_produto = $_POST['fornecedor_id_produto'];
    $nome_produto = $_POST['nome_produto'];
    $descricao_produto = $_POST['descricao_produto'];
    $preco_produto = str_replace(',', '.', $_POST['preco_produto']);

    // Processa o upload da imagem do produto
    $imagem_produto = "";
    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] == 0) {
        $resultado_upload_produto = redimensionarESalvarImagem($_FILES['imagem_produto']);
        if (strpos($resultado_upload_produto, 'img/') === 0) {
            $imagem_produto = $resultado_upload_produto;
        } else {
            $mensagem_erro_produto = $resultado_upload_produto;
        }
    }

    // Inserção ou atualização de produto
    if ($produto_id) {
        $sql_produto = "UPDATE produtos SET fornecedor_id=?, nome=?, descricao=?, preco=?";
        $params_produto = [$fornecedor_id_produto, $nome_produto, $descricao_produto, $preco_produto];
        if ($imagem_produto) {
            $sql_produto .= ", imagem=?";
            $params_produto[] = $imagem_produto;
        }
        $sql_produto .= " WHERE id=?";
        $params_produto[] = $produto_id;
        $stmt_produto = $conn->prepare($sql_produto);
        $stmt_produto->bind_param(str_repeat('s', count($params_produto)), ...$params_produto);
        $mensagem_produto = "Produto atualizado com sucesso!";
    } else {
        $sql_produto = "INSERT INTO produtos (fornecedor_id, nome, descricao, preco, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt_produto = $conn->prepare($sql_produto);
        $stmt_produto->bind_param("issss", $fornecedor_id_produto, $nome_produto, $descricao_produto, $preco_produto, $imagem_produto);
        $mensagem_produto = "Produto cadastrado com sucesso!";
    }

    if ($stmt_produto->execute()) {
        $class_produto = "success";
    } else {
        $mensagem_produto = "Erro: " . $stmt_produto->error;
        $class_produto = "error";
    }
}

// Exclusão de fornecedor ou produto
if (isset($_GET['delete_fornecedor_id'])) {
    $delete_fornecedor_id = $_GET['delete_fornecedor_id'];
    $sql = "DELETE FROM fornecedores WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_fornecedor_id);
    if ($stmt->execute()) {
        $mensagem = "Fornecedor excluído com sucesso!";
        $class = "success";
    } else {
        $mensagem = "Erro ao excluir fornecedor: " . $stmt->error;
        $class = "error";
    }
} elseif (isset($_GET['delete_produto_id'])) {
    $delete_produto_id = $_GET['delete_produto_id'];
    $sql_produto = "DELETE FROM produtos WHERE id=?";
    $stmt_produto = $conn->prepare($sql_produto);
    $stmt_produto->bind_param("i", $delete_produto_id);
    if ($stmt_produto->execute()) {
        $mensagem_produto = "Produto excluído com sucesso!";
        $class_produto = "success";
    } else {
        $mensagem_produto = "Erro ao excluir produto: " . $stmt_produto->error;
        $class_produto = "error";
    }
}

// Função para listar fornecedores
function listar_fornecedores($conn) {
    $sql = "SELECT * FROM fornecedores";
    return $conn->query($sql);
}

// Função para listar produtos
function listar_produtos($conn) {
    $sql = "SELECT produtos.*, fornecedores.nome AS fornecedor_nome FROM produtos JOIN fornecedores ON produtos.fornecedor_id = fornecedores.id";
    return $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
</head>
<body>
    <h1>Cadastro de Produtos</h1>

    <?php if (isset($mensagem)): ?>
        <div class="<?= $class; ?>"><?= $mensagem; ?></div>
    <?php endif; ?>

    <!-- Formulário de Produto -->
    <h2>Cadastrar Produto</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $produto_id ?? ''; ?>">
        <label for="fornecedor_id_produto">Fornecedor:</label>
        <select name="fornecedor_id_produto" required>
            <option value="">Selecione o fornecedor</option>
            <?php 
            $result_fornecedores = listar_fornecedores($conn);
            while ($fornecedor = $result_fornecedores->fetch_assoc()): ?>
                <option value="<?= $fornecedor['id']; ?>" <?= isset($fornecedor_id_produto) && $fornecedor_id_produto == $fornecedor['id'] ? 'selected' : ''; ?>><?= $fornecedor['nome']; ?></option>
            <?php endwhile; ?>
        </select><br>
        <label for="nome_produto">Nome do Produto:</label>
        <input type="text" name="nome_produto" value="<?= $nome_produto ?? ''; ?>" required><br>
        <label for="descricao_produto">Descrição:</label>
        <textarea name="descricao_produto" required><?= $descricao_produto ?? ''; ?></textarea><br>
        <label for="preco_produto">Preço:</label>
        <input type="text" name="preco_produto" value="<?= $preco_produto ?? ''; ?>" required><br>
        <label for="imagem_produto">Imagem:</label>
        <input type="file" name="imagem_produto"><br>
        <button type="submit">Salvar Produto</button>
    </form>

    <!-- Tabela de Fornecedores -->
    <h2>Fornecedores Cadastrados</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result_fornecedores = listar_fornecedores($conn);
            while ($fornecedor = $result_fornecedores->fetch_assoc()): ?>
                <tr>
                    <td><?= $fornecedor['nome']; ?></td>
                    <td><?= $fornecedor['email']; ?></td>
                    <td><?= $fornecedor['telefone']; ?></td>
                    <td>
                        <a href="?edit_fornecedor_id=<?= $fornecedor['id']; ?>">Editar</a>
                        <a href="?delete_fornecedor_id=<?= $fornecedor['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Tabela de Produtos -->
    <h2>Produtos Cadastrados</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Fornecedor</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result_produtos = listar_produtos($conn);
            while ($produto = $result_produtos->fetch_assoc()): ?>
                <tr>
                    <td><?= $produto['nome']; ?></td>
                    <td><?= $produto['fornecedor_nome']; ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="?edit_produto_id=<?= $produto['id']; ?>">Editar</a>
                        <a href="?delete_produto_id=<?= $produto['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
