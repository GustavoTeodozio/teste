<?php
require '../../database/database.php';


$db = new Database();
$conn = $db->getConnection();


// Verificar se o ID da subcategoria foi fornecido via GET
if (isset($_GET['subcategoria_id'])) {
    $subcategoriaId = $_GET['subcategoria_id'];

    // Utilizar a classe Database para conectar ao banco de dados
    $db = new Database();
    $conn = $db->getConnection();

    // Excluir a subcategoria no banco de dados
    $sql = "DELETE FROM subcategorias WHERE id = :subcategoria_id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->bindValue(':subcategoria_id', $subcategoriaId);
        $stmt->execute();

        echo "Subcategoria apagada com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao apagar subcategoria: " . $e->getMessage();
    }
} else {
    echo "ID da subcategoria não fornecido.";
}
