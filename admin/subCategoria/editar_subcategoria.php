<?php
require '../../database/database.php';


$db = new Database();
$conn = $db->getConnection();

// Verificar se a requisição é POST para salvar as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subcategoriaId = $_POST['subcategoria_id'];
    $nomeSubcategoria = $_POST['nome_subcategoria'];
    $categoriaId = $_POST['categoria_id'];

    // Utilizar a classe Database para conectar ao banco de dados
    $db = new Database();
    $conn = $db->getConnection();

    // Atualizar a subcategoria no banco de dados
    $sql = "UPDATE subcategorias SET nome = :nome, categoria_id = :categoria_id WHERE id = :subcategoria_id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->bindValue(':nome', $nomeSubcategoria);
        $stmt->bindValue(':categoria_id', $categoriaId);
        $stmt->bindValue(':subcategoria_id', $subcategoriaId);
        $stmt->execute();

        echo "Subcategoria atualizada com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao atualizar subcategoria: " . $e->getMessage();
    }
} else {
    // Caso a requisição seja GET, exibir o formulário de edição
    if (isset($_GET['subcategoria_id'])) {
        $subcategoriaId = $_GET['subcategoria_id'];

        // Conectar ao banco de dados
        $db = new Database();
        $conn = $db->getConnection();

        // Buscar a subcategoria pelo ID
        $sql = "SELECT id, nome, categoria_id FROM subcategorias WHERE id = :subcategoria_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':subcategoria_id', $subcategoriaId);
        $stmt->execute();

        $subcategoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subcategoria) {
?>
            <h1>Editar Subcategoria</h1>
            <form action="editar_subcategoria.php" method="post">
                <input type="hidden" name="subcategoria_id" value="<?php echo htmlspecialchars($subcategoria['id']); ?>">

                <label for="nome_subcategoria">Nome da Subcategoria:</label>
                <input type="text" id="nome_subcategoria" name="nome_subcategoria" value="<?php echo htmlspecialchars($subcategoria['nome']); ?>" required><br><br>

                <label for="categoria_id">Categoria:</label>
                <select id="categoria_id" name="categoria_id">
                    <?php
                    // Carregar categorias
                    $stmtCategorias = $conn->query("SELECT id, nome FROM categorias");
                    while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($categoria['id'] == $subcategoria['categoria_id']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($categoria['id']) . "' $selected>" . htmlspecialchars($categoria['nome']) . "</option>";
                    }
                    ?>
                </select><br><br>

                <input type="submit" value="Atualizar Subcategoria">
            </form>
<?php
        } else {
            echo "Subcategoria não encontrada.";
        }
    }
}
?>