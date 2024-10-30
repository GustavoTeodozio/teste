<?php
// Verificar se o token está presente na URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    echo "Token inválido.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Redefinir Senha</title>
</head>
<body>
    <h2>Definir Nova Senha</h2>
    <form method="POST" action="nova_senha.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="password">Nova Senha:</label>
        <input type="password" name="password" required>
        <label for="confirm_password">Confirme a Nova Senha:</label>
        <input type="password" name="confirm_password" required>
        <button type="submit">Alterar Senha</button>
    </form>
</body>
</html>
