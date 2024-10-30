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




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_categoria = $_POST['nome_categoria'];

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Insere a nova categoria no banco de dados
        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");
        $stmt->bindParam(':nome', $nome_categoria);
        $stmt->execute();

        // Redireciona após a inserção
        header('Location: listar_categorias.php');
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar categoria: " . $e->getMessage());
    }
} else {
    die("Método não suportado.");
}
