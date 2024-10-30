<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para a página de login ou exibe uma mensagem de erro
    header('Location: login.php?message=Acesso negado');
    exit();
}
require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $artigo_id = (int)$_GET['id'];

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Obtém o artigo e suas categorias
        $stmt = $pdo->prepare("SELECT * FROM artigos WHERE id = :id");
        $stmt->bindParam(':id', $artigo_id, PDO::PARAM_INT);
        $stmt->execute();
        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$artigo) {
            die("Artigo não encontrado.");
        }

        $stmt = $pdo->prepare("SELECT * FROM categorias");
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar o artigo: " . $e->getMessage());
    }
} else {
    die("ID do artigo inválido.");
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_antigo = $artigo['titulo'];  // Título antigo
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $categoria_id = $_POST['categoria_id'];

    // Atualiza os campos de título, conteúdo e categoria
    $stmt = $pdo->prepare("
        UPDATE artigos
        SET titulo = :titulo, conteudo = :conteudo, categoria_id = :categoria_id
        WHERE id = :id
    ");
    $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->bindParam(':conteudo', $conteudo, PDO::PARAM_STR);
    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $artigo_id, PDO::PARAM_INT);
    $stmt->execute();

    // Renomeia o arquivo se o título foi alterado
    if ($titulo_antigo !== $titulo) {
        $slug_antigo = slugify($titulo_antigo);
        $slug_novo = slugify($titulo);

        $arquivo_antigo = "../artigos/$slug_antigo.php";
        $arquivo_novo = "../artigos/$slug_novo.php";

        if (file_exists($arquivo_antigo)) {
            rename($arquivo_antigo, $arquivo_novo);
        }
    }

    // Verifica se um novo arquivo de imagem foi enviado
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Move o arquivo para a pasta de uploads
        $uploadDir = __DIR__ . '/../../uploads/';
        $uploadFile = $uploadDir . basename($_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile);

        // Exclui a imagem antiga, se existir
        if (!empty($artigo['imagem'])) {
            $imagem_antiga = $uploadDir . $artigo['imagem'];
            if (file_exists($imagem_antiga)) {
                unlink($imagem_antiga);
            }
        }

        // Atualiza o campo de imagem no banco de dados
        $imagem = $_FILES['imagem']['name'];
        $stmt = $pdo->prepare("
            UPDATE artigos
            SET imagem = :imagem
            WHERE id = :id
        ");
        $stmt->bindParam(':imagem', $imagem, PDO::PARAM_STR);
        $stmt->bindParam(':id', $artigo_id, PDO::PARAM_INT);
        $stmt->execute();

        // Atualiza o valor do campo de imagem no array $artigo
        $artigo['imagem'] = $imagem;
    }

    // Redireciona para listar_artigo.php após a atualização
    header("Location: listar_artigos.php");
    exit;
}

function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Artigo</title>
</head>

<body>
    <h1>Editar Artigo</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <!-- Campo oculto para passar o ID do artigo -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($artigo['id']); ?>">

        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($artigo['titulo']); ?>" required><br><br>

        <label for="conteudo">Conteúdo:</label>
        <textarea id="conteudo" name="conteudo" rows="10" cols="30" required><?php echo htmlspecialchars($artigo['conteudo']); ?></textarea><br><br>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" id="data_hora" name="data_hora" value="<?php echo htmlspecialchars($artigo['data_hora']); ?>" readonly required><br><br>

        <label for="imagem">Imagem:</label>
        <?php if (!empty($artigo['imagem'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($artigo['imagem']); ?>" alt="Imagem do Artigo" style="max-width: 100px; max-height: 100px;"><br><br>
        <?php endif; ?>
        <input type="file" id="imagem" name="imagem"><br><br>

        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria_id">
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo htmlspecialchars($categoria['id']); ?>" <?php echo $categoria['id'] == $artigo['categoria_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Salvar Alterações">
    </form>
</body>

</html>