<?php
session_start();
require './database/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artigoId = $_POST['artigo_id'];
    $userId = $_POST['user_id'];
    $action = $_POST['action']; // "like" ou "dislike"

    try {
        // Verificar a interação atual do usuário
        $stmtCheck = $conn->prepare("SELECT like_dislike FROM user_likes_dislikes WHERE artigo_id = :artigo_id AND user_id = :user_id");
        $stmtCheck->bindParam(':artigo_id', $artigoId);
        $stmtCheck->bindParam(':user_id', $userId);
        $stmtCheck->execute();

        $currentVote = $stmtCheck->fetchColumn();

        if ($currentVote === false) {
            // Nenhum voto anterior: inserir novo voto
            $likeDislikeValue = ($action === "like") ? 1 : -1;
            $stmtInsert = $conn->prepare("INSERT INTO user_likes_dislikes (artigo_id, user_id, like_dislike) VALUES (:artigo_id, :user_id, :like_dislike)");
            $stmtInsert->bindParam(':artigo_id', $artigoId);
            $stmtInsert->bindParam(':user_id', $userId);
            $stmtInsert->bindParam(':like_dislike', $likeDislikeValue);
            $stmtInsert->execute();
        } elseif ($currentVote == 1 && $action === "dislike") {
            // O usuário já deu like, atualizar para dislike
            $stmtUpdate = $conn->prepare("UPDATE user_likes_dislikes SET like_dislike = -1 WHERE artigo_id = :artigo_id AND user_id = :user_id");
            $stmtUpdate->bindParam(':artigo_id', $artigoId);
            $stmtUpdate->bindParam(':user_id', $userId);
            $stmtUpdate->execute();
        } elseif ($currentVote == -1 && $action === "like") {
            // O usuário já deu dislike, atualizar para like
            $stmtUpdate = $conn->prepare("UPDATE user_likes_dislikes SET like_dislike = 1 WHERE artigo_id = :artigo_id AND user_id = :user_id");
            $stmtUpdate->bindParam(':artigo_id', $artigoId);
            $stmtUpdate->bindParam(':user_id', $userId);
            $stmtUpdate->execute();
        }

        // Contar os likes e dislikes
        returnResponse($artigoId);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function returnResponse($artigoId)
{
    global $conn;

    // Contar likes
    $stmtCountLikes = $conn->prepare("SELECT COUNT(*) FROM user_likes_dislikes WHERE artigo_id = :artigo_id AND like_dislike = 1");
    $stmtCountLikes->bindParam(':artigo_id', $artigoId);
    $stmtCountLikes->execute();
    $likesCount = $stmtCountLikes->fetchColumn();

    // Contar dislikes
    $stmtCountDislikes = $conn->prepare("SELECT COUNT(*) FROM user_likes_dislikes WHERE artigo_id = :artigo_id AND like_dislike = -1");
    $stmtCountDislikes->bindParam(':artigo_id', $artigoId);
    $stmtCountDislikes->execute();
    $dislikesCount = $stmtCountDislikes->fetchColumn();

    echo json_encode(['likes' => $likesCount, 'dislikes' => $dislikesCount]);
}
