<?php
session_start();

// Corrigindo o caminho do autoloader do Composer
require '../vendor/autoload.php';
?>
<form method="POST" action="password_reset.php">
    <label for="email">Digite seu e-mail:</label>
    <input type="email" name="email" required>
    <button type="submit">Enviar Link de Redefinição</button>
</form>