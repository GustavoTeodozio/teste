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

try {
    // Consulta para obter todos os artigos
    $stmt = $conn->query("
        SELECT id, titulo, data_hora, imagem
        FROM artigos 
        ORDER BY data_hora DESC
    ");
    $artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar artigos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Artigos</title>
</head>

<body>
    <h1>Artigos Disponíveis</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Data e Hora</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($artigos): ?>
                <?php foreach ($artigos as $artigo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($artigo['id']); ?></td>
                        <td><?php echo htmlspecialchars($artigo['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($artigo['data_hora']); ?></td>
                        <td>
                            <?php if (!empty($artigo['imagem'])): ?>
                                <img src="../../uploads/<?php echo htmlspecialchars($artigo['imagem']); ?>"
                                    alt="Imagem do Artigo"
                                    style="max-width: 100px; max-height: 100px;">
                            <?php else: ?>
                                Nenhuma imagem
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="editar.php?id=<?php echo htmlspecialchars($artigo['id']); ?>">Editar</a>
                            <a href="deletar_artigo.php?id=<?php echo htmlspecialchars($artigo['id']); ?>" onclick="return confirm('Tem certeza que deseja excluir este artigo?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhum artigo encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <a href="../artigo/criar_artigo.php">Cadastrar novo artigo</a><br>
    <a href="../categoria/criar_categoria.php">Cadastrar Categoria</a>

</body>

</html>