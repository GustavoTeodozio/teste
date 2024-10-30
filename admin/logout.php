<?php
session_start();

// Destrua todas as variáveis da sessão
$_SESSION = array();

// Se você quiser destruir completamente a sessão, exclua também o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrua a sessão
session_destroy();

// Redirecione o usuário para a página de login ou inicial
header('Location: login.php');
exit;
