<?php
require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();

// Verifica se foi passado um categoria_id via GET
if (isset($_GET['categoria_id'])) {
    $categoriaId = $_GET['categoria_id'];

    // Cria uma instância da classe Database
    $db = new Database();
    $pdo = $db->getConnection(); // Obtém a conexão PDO

    // Carregar subcategorias da categoria selecionada
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM subcategorias WHERE categoria_id = :categoria_id");
        $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->execute();
        $subcategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retorna as subcategorias em formato JSON
        echo json_encode($subcategorias);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
}
