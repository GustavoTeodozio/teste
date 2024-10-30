<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Conectar ao banco de dados
    $conn = new mysqli('localhost', 'root', '', 'blog');

    // Verificar se o e-mail está no banco de dados (usuários)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Gerar token e salvar no banco de dados
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_created_at = NOW() WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Configurar PHPMailer
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Habilitar depuração do SMTP
        $mail->Debugoutput = 'html'; // Formato da saída de depuração
        $mail->Host = 'smtp.gmail.com'; // Ajuste para o seu servidor de e-mail
        $mail->SMTPAuth = true;
        $mail->Username = 'gustavo.sampaio195@gmail.com';
        $mail->Password = 'nzzthdcelofwtgoc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ou PHPMailer::ENCRYPTION_STARTTLS
        $mail->Port = 465; // ou 587 para STARTTLS

        $mail->setFrom('gustavo.sampaio195@gmail.com', 'Suporte');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $url = "http://localhost/teste/admin/nova_senha.php?token=$token";
        $mail->Subject = 'Redefinição de Senha';
        $mail->Body = "Clique no link para redefinir sua senha: <a href='$url'>$url</a>";

        if ($mail->send()) {
            echo "E-mail enviado!";
        } else {
            echo "Falha ao enviar e-mail: " . $mail->ErrorInfo; // Exibir informações de erro
        }
    } else {
        echo "E-mail não encontrado.";
    }
}
