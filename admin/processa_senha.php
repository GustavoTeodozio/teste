<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe o token e as senhas do formulário
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifica se as senhas coincidem
    if ($new_password !== $confirm_password) {
        echo "As senhas não coincidem.";
        exit();
    }

    // Conectar ao banco de dados
    $conn = new mysqli('localhost', 'root', '', 'blog');

    // Verificar se o token é válido (e opcionalmente verificar se não expirou)
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_created_at >= (NOW() - INTERVAL 1 HOUR)");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token é válido, permitir alteração da senha
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Hash da nova senha
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Atualizar a senha no banco e remover o token
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, token_created_at = NULL WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $user_id);

        if ($stmt->execute()) {
            echo "Senha alterada com sucesso!";
        } else {
            echo "Erro ao alterar a senha.";
        }
    } else {
        echo "Token inválido ou expirado.";
    }
}
