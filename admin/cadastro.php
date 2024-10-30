<?php
session_start();

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line");
}

$admin_name = "teo";
$admin_email = "gustateo46@gmail.com";
$admin_password = "Admin123";

$password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

// Instancia a classe Database
require __DIR__ . "../../database/database.php";
$db = new Database();
$pdo = $db->getConnection();

try {
    // Verifique se um administrador já existe
    $sql = "SELECT * FROM users WHERE role = 'admin'";
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        die("An admin user already exists");
    }

    // Insere o novo administrador
    $sql = "INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, 'admin')";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':name', $admin_name);
    $stmt->bindParam(':email', $admin_email);
    $stmt->bindParam(':password_hash', $password_hash);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        die("Failed to create admin user.");
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Código de erro para violação de chave única (e.g., email duplicado)
        die("Email already taken");
    } else {
        die("Database error: " . $e->getMessage());
    }
}
