<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h2>Login do Usuário</h2>

    <form action="login.php" method="POST">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="login">Entrar</button>
    </form>

    <?php
    session_start(); // Iniciar sessão

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        require './database/database.php';

        // Obter dados do formulário
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            // Conectar ao banco de dados
            $db = new Database();
            $conn = $db->getConnection();

            // Preparar e executar a consulta para buscar o usuário
            $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Verificar se o usuário existe
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar a senha
                if (password_verify($password, $user['password_hash'])) {
                    // Definir as variáveis de sessão
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];

                    // Redirecionar para o index.php
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<p>Senha incorreta. Tente novamente.</p>";
                }
            } else {
                echo "<p>Usuário não encontrado.</p>";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
    ?>
</body>

</html>