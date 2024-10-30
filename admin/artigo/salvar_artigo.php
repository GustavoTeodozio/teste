<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redireciona para a página de login ou exibe uma mensagem de erro
    header('Location: login.php?message=Acesso negado');
    exit();
}
require '../../database/database.php';

$db = new Database();
$conn = $db->getConnection();


function gerarSlug($string)
{
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return rtrim($string, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $data_hora = $_POST['data_hora'];
    $categoria_id = $_POST['categoria_id'];
    $subcategoria_id = $_POST['subcategoria_id']; // Captura a subcategoria do formulário
    $nova_categoria = $_POST['nova_categoria'];

    // Verifica se uma nova categoria foi fornecida
    if (!empty($nova_categoria)) {
        try {
            $db = new Database();
            $pdo = $db->getConnection();

            // Insere a nova categoria
            $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");
            $stmt->bindParam(':nome', $nova_categoria);
            $stmt->execute();

            // Obtém o ID da nova categoria
            $categoria_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Erro ao salvar nova categoria: " . $e->getMessage());
        }
    }

    // Verifica se uma imagem foi enviada
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = basename($_FILES['imagem']['name']);
        $upload_dir = '../../uploads/';
        $upload_file = $upload_dir . $imagem;

        // Move o arquivo enviado para o diretório de uploads
        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_file)) {
            die("Erro ao fazer upload da imagem.");
        }
    }

    // Gera o slug a partir do título
    $slug = gerarSlug($titulo);

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Verifica se o artigo já existe
        $stmt = $pdo->prepare("SELECT id FROM artigos WHERE slug = :slug");
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        $artigo_existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($artigo_existente) {
            // Atualiza o artigo existente
            $stmt = $pdo->prepare("UPDATE artigos SET titulo = :titulo, conteudo = :conteudo, data_hora = :data_hora, imagem = :imagem, categoria_id = :categoria_id, subcategoria_id = :subcategoria_id WHERE slug = :slug");
        } else {
            // Insere um novo artigo
            $stmt = $pdo->prepare("INSERT INTO artigos (titulo, conteudo, data_hora, imagem, categoria_id, subcategoria_id, slug) VALUES (:titulo, :conteudo, :data_hora, :imagem, :categoria_id, :subcategoria_id, :slug)");
        }

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':conteudo', $conteudo);
        $stmt->bindParam(':data_hora', $data_hora);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':subcategoria_id', $subcategoria_id); // Adicionando a subcategoria
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        // Cria o conteúdo do novo arquivo PHP com HTML
        $novo_arquivo_conteudo = "<!DOCTYPE html>\n";
        $novo_arquivo_conteudo .= "<html lang='pt-br'>\n";
        $novo_arquivo_conteudo .= "<head>\n";
        $novo_arquivo_conteudo .= "    <meta charset='UTF-8'>\n";
        $novo_arquivo_conteudo .= "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
        $novo_arquivo_conteudo .= "    <title>" . htmlspecialchars($titulo) . "</title>\n";
        $novo_arquivo_conteudo .= "</head>\n";
        $novo_arquivo_conteudo .= "<body>\n";
        $novo_arquivo_conteudo .= '<a href="/teste/index.php" style="display: inline-block; padding: 10px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Voltar ao Início</a>';
        $novo_arquivo_conteudo .= "    <h1>" . htmlspecialchars($titulo) . "</h1>\n";
        $novo_arquivo_conteudo .= "    <img src='../uploads/" . htmlspecialchars($imagem) . "' alt='" . htmlspecialchars($titulo) . "'>\n";
        $novo_arquivo_conteudo .= $conteudo;  // Salva o conteúdo sem escapar o HTML
        $novo_arquivo_conteudo .= "    <p><strong>Data e Hora:</strong> " . htmlspecialchars($data_hora) . "</p>\n";
        $novo_arquivo_conteudo .= "    <p><small>Autor: AcessivelBank</small></p>\n"; // Adicionando o autor
        $novo_arquivo_conteudo .= "</body>\n";
        $novo_arquivo_conteudo .= "</html>";

        // Define o nome do novo arquivo PHP usando o slug
        $novo_arquivo_nome = '../../artigos/' . $slug . '.php';

        // Salva o novo arquivo PHP na pasta artigos/
        if (file_put_contents($novo_arquivo_nome, $novo_arquivo_conteudo) === false) {
            die("Erro ao criar o arquivo PHP.");
        }

        // Se o slug foi alterado, exclui o arquivo antigo
        if ($artigo_existente && $artigo_existente['slug'] !== $slug) {
            $arquivo_antigo = '../../artigos/' . $artigo_existente['slug'] . '.php';
            if (file_exists($arquivo_antigo)) {
                unlink($arquivo_antigo);
            }
        }

        header('Location: listar_artigos.php');
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar artigo: " . $e->getMessage());
    }
}
