<?php
session_start(); // Iniciar a sessão
session_unset(); // Remover todas as variáveis da sessão
session_destroy(); // Destruir a sessão

// Redirecionar para a página de login ou index
header("Location: login.php");
exit();
