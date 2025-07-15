<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Supprimer un article de la base de données
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_message'] = "Article supprimé avec succès.";
    $_SESSION['flash_type'] = "error";
    http_response_code(200);
    exit();
} else {
    http_response_code(400);
    echo "ID manquant";
    exit();
}
?>