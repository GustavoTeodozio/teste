<?php
require '../../database/database.php';


$db = new Database();
$conn = $db->getConnection();
// Buscar todas as subcategorias
$sql = "SELECT subcategorias.id, subcategorias.nome, categorias.nome AS categoria_nome 
        FROM subcategorias 
        JOIN categorias ON subcategorias.categoria_id = categorias.id";
$stmt = $conn->query($sql);

echo "<h1>Lista de Subcategorias</h1><ul>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>";
    echo htmlspecialchars($row['nome']) . " (Categoria: " . htmlspecialchars($row['categoria_nome']) . ")";
    echo " - <a href='editar_subcategoria.php?subcategoria_id=" . $row['id'] . "'>Editar</a>";
    echo " | <a href='apagar_subcategoria.php?subcategoria_id=" . $row['id'] . "' onclick=\"return confirm('Tem certeza que deseja apagar?');\">Apagar</a>";
    echo "</li>";
}

echo "</ul>";
