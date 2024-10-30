<?php
require '../../database/database.php';
// Verifica se o usuário é administrador

$db = new Database();
$conn = $db->getConnection();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Subcategoria</title>
    <script>
        function validarFormulario(event) {
            var nomeSubcategoria = document.getElementById('nome_subcategoria').value.trim();
            if (nomeSubcategoria === '') {
                event.preventDefault();
                alert('O campo de nome da subcategoria não pode estar vazio.');
                return false;
            }

            var categoriaSelecionada = document.getElementById('categoria').value;
            if (categoriaSelecionada === '') {
                event.preventDefault();
                alert('Você precisa selecionar uma categoria para a subcategoria.');
                return false;
            }
        }
    </script>
</head>

<body>
    <h1>Criar Subcategoria</h1>
    <form action="salvar_subcategoria.php" method="post" onsubmit="validarFormulario(event)">
        <label for="nome_subcategoria">Nome da Subcategoria:</label>
        <input type="text" id="nome_subcategoria" name="nome_subcategoria" required><br><br>

        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria_id" required>
            <option value="">Escolha uma categoria</option>
            <?php
            require_once '../../database/database.php';

            // Cria uma instância da classe Database
            $db = new Database();
            $pdo = $db->getConnection(); // Obtém a conexão PDO

            // Carregar categorias
            try {
                $stmt = $pdo->query("SELECT id, nome FROM categorias");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nome']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Erro ao carregar categorias</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Criar Subcategoria">
    </form>

    <br>
    <a href="listar_subcategorias.php">Voltar para a lista de subcategorias</a>
</body>

</html>