
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <form method="POST" action="verifica_login.php">
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <a href="redefinir_senha.php">Redefinir Senha</a>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($erro)): ?>
        <p><?php echo $erro; ?></p>
    <?php endif; ?>
</body>

</html>