<?php
session_start();
function verificarSessao($requiredRole = null)
{
    // Verifica se o usuário está logado
    if (!isset($_SESSION['id'])) {
        // Se não estiver logado, redireciona para a página de login
        header('Location: login.php');
        exit();
    }

    if ($requiredRole !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole)) {
        header('Location: admin_dashboard.php'); 
        exit();
    }
}
