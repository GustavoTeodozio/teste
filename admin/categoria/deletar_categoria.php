<?php
require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();



// Verifica se o ID da categoria foi passado pela URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categoria_id = (int)$_GET['id'];

    $db = new Database();
    $pdo = $db->getConnection();

    try {
        // Prepara a consulta para excluir a categoria
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindParam(':id', $categoria_id, PDO::PARAM_INT);

        // Executa a consulta
        if ($stmt->execute()) {
            // Redireciona para a página de listagem de categorias com uma mensagem de sucesso
            header("Location: listar_categorias.php?success=Categoria excluída com sucesso.");
            exit;
        } else {
            // Redireciona com uma mensagem de erro
            header("Location: listar_categorias.php?error=Falha ao excluir a categoria.");
            exit;
        }
    } catch (PDOException $e) {
        die("Erro ao excluir categoria: " . $e->getMessage());
    }
} else {
    // Se o ID não é válido, redireciona com uma mensagem de erro
    header("Location: listar_categorias.php?error=ID inválido.");
    exit;
}
