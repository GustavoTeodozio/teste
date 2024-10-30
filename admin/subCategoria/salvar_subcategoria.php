<?php
require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeSubcategoria = $_POST['nome_subcategoria'];
    $categoriaId = $_POST['categoria_id'];

    // Utilizar a classe Database para conectar ao banco de dados
    $db = new Database();
    $conn = $db->getConnection();

    // Inserir a nova subcategoria no banco de dados
    $sql = "INSERT INTO subcategorias (nome, categoria_id) VALUES (:nome, :categoria_id)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->bindValue(':nome', $nomeSubcategoria);
        $stmt->bindValue(':categoria_id', $categoriaId);
        $stmt->execute();

        echo "Subcategoria criada com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao criar subcategoria: " . $e->getMessage();
    }
}
