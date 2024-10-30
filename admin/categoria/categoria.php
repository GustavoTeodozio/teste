<?php
require '../../database/database.php';
// Verifica se o usuário é administrador
$db = new Database();
$conn = $db->getConnection();

// Verifica se o ID da categoria foi passado na URL
$categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($categoryId > 0) {
    try {
        // Verifica se a categoria existe
        $stmt = $pdo->prepare("SELECT nome FROM categorias WHERE id = :categoria_id");
        $stmt->bindParam(':categoria_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($categoria) {
            echo "<h1>Artigos da Categoria: " . htmlspecialchars($categoria['nome']) . "</h1>";

            // Busca os artigos relacionados à categoria
            $stmt = $pdo->prepare("SELECT * FROM artigos WHERE categoria_id = :categoria_id");
            $stmt->bindParam(':categoria_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            $artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verifica se há artigos para exibir
            if ($artigos) {
                foreach ($artigos as $artigo) {
                    echo "<h2>" . htmlspecialchars($artigo['titulo']) . "</h2>";
                    echo "<p>" . htmlspecialchars($artigo['conteudo']) . "</p>";
                    echo "<p><strong>Data e Hora:</strong> " . htmlspecialchars($artigo['data_hora']) . "</p>";
                    if (!empty($artigo['imagem'])) {
                        echo "<img src='../uploads/" . htmlspecialchars($artigo['imagem']) . "' alt='Imagem' width='200'>";
                    }
                    echo "<hr>";
                }
            } else {
                echo "<p>Nenhum artigo encontrado para esta categoria.</p>";
            }
        } else {
            echo "<p>Categoria não encontrada.</p>";
        }
    } catch (PDOException $e) {
        echo "Erro ao carregar artigos: " . $e->getMessage();
    }
} else {
    echo "<p>ID de categoria inválido.</p>";
}
