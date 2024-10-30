<?php
session_start(); // Inicie a sessão no início do script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../database/database.php';

    $db = new Database();
    $conn = $db->getConnection();

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifique se o email e a senha foram enviados
    if (!empty($email) && !empty($senha)) {
        // Verifique se o usuário existe com o email fornecido
        $sql = "SELECT id, name, password_hash, role FROM users WHERE email = :email"; // Inclua 'role' se quiser
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifique a senha
            if (password_verify($senha, $row['password_hash'])) {
                // Armazene as informações do usuário na sessão
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                // Armazene a role se a coluna existir no banco de dados
                $_SESSION['role'] = $row['role']; // Comente esta linha se não houver 'role' na tabela

                // Redirecionar para a página padrão após o login bem-sucedido
                header('Location: admin_dashboard.php'); // Altere para a página que deseja redirecionar
                exit();
            } else {
                $erro = 'E-mail ou senha inválidos.';
            }
        } else {
            $erro = 'E-mail ou senha inválidos.';
        }
    } else {
        $erro = 'Por favor, preencha todos os campos.';
    }
}
