<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para a página de login ou exibe uma mensagem de erro
    header('Location: login.php?message=Acesso negado');
    exit();
}

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
    <title>Criar Categoria</title>
    <script>
        function validarFormulario(event) {
            var nomeCategoria = document.getElementById('nome_categoria').value.trim();
            if (nomeCategoria === '') {
                event.preventDefault();
                alert('O campo de nome da categoria não pode estar vazio.');
                return false;
            }
        }
    </script>
</head>

<body>
    <h1>Criar Categoria</h1>
    <form action="salvar_categoria.php" method="post" onsubmit="validarFormulario(event)">
        <label for="nome_categoria">Nome da Categoria:</label>
        <input type="text" id="nome_categoria" name="nome_categoria" required><br><br>

        <input type="submit" value="Criar Categoria">
    </form>

    <br>
    <a href="listar_categorias.php">Voltar para a lista de categorias</a><br>
    <a href="../artigo/criar_artigo.php">Cadastrar novo artigo</a><br>
</body>

</html>