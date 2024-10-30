<?php
require_once 'auth/auth.php';
require '../database/database.php';

verificarSessao('admin');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>

<body>
    <h1>Bem-vindo, <?php
                    // Verifique se o nome está definido na sessão
                    if (isset($_SESSION['name'])) {
                        echo htmlspecialchars($_SESSION['name']);
                    } else {
                        echo 'Usuário';
                    }
                    ?></h1>

    <!-- Conteúdo da página de administração -->
    <a href="./artigo/criar_artigo.php">Cadastrar Artigos</a><br>
    <a href="./artigo/listar_artigos.php">Listar Artigos</a><br>
    <a href="logout.php">Sair</a><br>
    <a href="./categoria/criar_categoria.php">Criar Categoria</a><br>
    <a href="./categoria/listar_categorias.php">Listar Categoria</a><br>
    <a href="./subCategoria/criar_subcategoria.php">Criar Sub Categoria</a><br>
    <a href="./subCategoria/listar_subcategoria.php">Listar Sub Categoria</a><br>

</body>

</html>