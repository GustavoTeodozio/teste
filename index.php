<?php
session_start(); // Certifique-se de iniciar a sessão
require './database/database.php';

$db = new Database();
$conn = $db->getConnection();

$termoPesquisa = $_GET['pesquisa'] ?? '';
$categoriaId = $_GET['categoria_id'] ?? '';
$subcategoriaId = $_GET['subcategoria_id'] ?? '';

echo '<form method="GET" action="">
        <input type="text" name="pesquisa" placeholder="Pesquisar artigos..." value="' . htmlspecialchars($termoPesquisa) . '">
        <button type="submit">Buscar</button>
      </form>';

echo '<a href="index.php" style="display: inline-block; padding: 10px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Voltar ao Início</a>';

// Consulta SQL para buscar artigos
$sql = "SELECT 
            artigos.id, 
            artigos.titulo, 
            artigos.imagem, 
            artigos.slug, 
            artigos.conteudo, 
            categorias.nome AS categoria_nome,
            COUNT(CASE WHEN uld.like_dislike = 1 THEN 1 END) AS likes,
            COUNT(CASE WHEN uld.like_dislike = -1 THEN 1 END) AS dislikes
        FROM artigos 
        INNER JOIN categorias ON artigos.categoria_id = categorias.id
        LEFT JOIN user_likes_dislikes uld ON artigos.id = uld.artigo_id
        WHERE 1=1";

if (!empty($termoPesquisa)) {
    $sql .= " AND artigos.titulo LIKE :termo";
}

if (!empty($categoriaId)) {
    $sql .= " AND artigos.categoria_id = :categoria_id";
}

if (!empty($subcategoriaId)) {
    $sql .= " AND artigos.subcategoria_id = :subcategoria_id";
}

$sql .= " GROUP BY artigos.id"; // Agrupar pelos artigos para contagem correta

$stmt = $conn->prepare($sql);

if (!empty($termoPesquisa)) {
    $stmt->bindValue(':termo', '%' . $termoPesquisa . '%');
}
if (!empty($categoriaId)) {
    $stmt->bindValue(':categoria_id', $categoriaId);
}
if (!empty($subcategoriaId)) {
    $stmt->bindValue(':subcategoria_id', $subcategoriaId);
}

$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $artigoId = $row['id'];
        $caminhoImagem = './uploads/' . htmlspecialchars($row['imagem']);
        $tituloArtigo = htmlspecialchars($row['titulo']);
        $slugArtigo = htmlspecialchars($row['slug']);
        $conteudoArtigo = strip_tags($row['conteudo']);
        $categoriaNome = htmlspecialchars($row['categoria_nome']);
        $likeCount = $row['likes'] ?? 0; // Atribuição padrão em caso de NULL
        $dislikeCount = $row['dislikes'] ?? 0; // Atribuição padrão em caso de NULL

        echo "<div class='artigo'>";
        echo "<h2>$tituloArtigo</h2>";

        if (file_exists($caminhoImagem)) {
            echo "<a href='./artigos/" . $slugArtigo . ".php'>";
            echo "<img src='$caminhoImagem' alt='$tituloArtigo' style='max-width: 200px; max-height: 200px;'>";
            echo "</a>";
        } else {
            echo "<p>Imagem não encontrada: " . htmlspecialchars($row['imagem']) . "</p>";
        }

        $previaConteudo = substr($conteudoArtigo, 0, 10) . '...';
        echo "<p>$previaConteudo</p>";
        echo "<p><strong>Categoria:</strong> $categoriaNome</p>";

        echo "<p><strong>Likes:</strong> <span id='like-count-$artigoId'>$likeCount</span></p>";
        echo "<p><strong>Dislikes:</strong> <span id='dislike-count-$artigoId'>$dislikeCount</span></p>";

        // Verificar se o usuário está logado
        if (isset($_SESSION['user_id'])) {
            echo "<button onclick='toggleLikeDislike($artigoId, \"like\")'>Curtir</button>";
            echo "<button onclick='toggleLikeDislike($artigoId, \"dislike\")'>Não Curtir</button>";
        } else {
            echo "<p><em>Você precisa estar logado para curtir ou não curtir um artigo. <a href='login.php'>Faça login aqui</a>.</em></p>";
        }
        echo "</div>";
        echo "<p><a href='admin/login.php'>Faça login como admin aqui</a>.</em></p>";
        echo "<p><a href='logout.php'>Logout</a>.</em></p>";
        
    }
} else {
    echo "Nenhum artigo encontrado.";
}
?>

<script>
    function toggleLikeDislike(artigoId, action) {
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

        if (!userId) {
            alert("Você precisa estar logado para curtir ou não curtir um artigo.");
            return;
        }

        fetch('like_dislike_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `artigo_id=${artigoId}&action=${action}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    document.getElementById(`like-count-${artigoId}`).textContent = data.likes;
                    document.getElementById(`dislike-count-${artigoId}`).textContent = data.dislikes;
                } else {
                    console.error(data.error);
                }
            });
    }
</script>