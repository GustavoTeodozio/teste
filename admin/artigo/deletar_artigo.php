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
// Verifica se o ID do artigo foi enviado via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $artigo_id = (int)$_GET['id'];

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Obtém o caminho da imagem e o slug associados ao artigo
        $stmt = $pdo->prepare("SELECT imagem, slug FROM artigos WHERE id = :id");
        $stmt->bindParam(':id', $artigo_id, PDO::PARAM_INT);
        $stmt->execute();
        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($artigo) {
            // Exclui o artigo do banco de dados
            $stmt = $pdo->prepare("DELETE FROM artigos WHERE id = :id");
            $stmt->bindParam(':id', $artigo_id, PDO::PARAM_INT);
            $stmt->execute();

            // Exclui a imagem associada, se existir
            if (!empty($artigo['imagem'])) {
                $imagem_path = '../../uploads/' . $artigo['imagem'];
                if (file_exists($imagem_path)) {
                    unlink($imagem_path);
                }
            }

            // Exclui o arquivo PHP associado, se existir
            if (!empty($artigo['slug'])) {
                $arquivo_path = '/../artigos/' . $artigo['slug'] . '.php';
                if (file_exists($arquivo_path)) {
                    unlink($arquivo_path);
                }
            }

            header('Location: listar_artigos.php');
            exit;
        } else {
            echo "Artigo não encontrado.";
        }
    } catch (PDOException $e) {
        die("Erro ao excluir artigo: " . $e->getMessage());
    }
} else {
    echo "ID do artigo inválido.";
}
