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
    $stmt = $pdo->query("SELECT id, nome FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar categorias: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Categorias</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <h1>Categorias Disponíveis</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($categorias): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($categoria['id']); ?></td>
                        <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                        <td>
                            <a href="editar_categoria.php?id=<?php echo htmlspecialchars($categoria['id']); ?>">Editar</a>
                            <a href="deletar_categoria.php?id=<?php echo htmlspecialchars($categoria['id']); ?>" onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhuma categoria encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <a href="criar_categoria.php">Criar nova categoria</a><br>
    <a href="../artigo/criar_artigo.php">Cadastrar novo artigo</a>
</body>

</html>