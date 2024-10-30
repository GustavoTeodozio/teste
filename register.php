<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuário</title>
</head>

<body>
    <h2>Registrar Novo Usuário</h2>

    <form action="register.php" method="POST">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="register">Registrar</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        require './database/database.php';

        // Obter dados do formulário
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = 'user'; // Role padrão
        $createdAt = date('Y-m-d H:i:s');

        // Hash da senha
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Conectar ao banco de dados
            $db = new Database();
            $conn = $db->getConnection();

            // Preparar e executar a consulta de inserção
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:name, :email, :password_hash, :role, :created_at)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':created_at', $createdAt);

            if ($stmt->execute()) {
                echo "Usuário registrado com sucesso!";
            } else {
                echo "Erro ao registrar o usuário.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
    ?>
</body>

</html>