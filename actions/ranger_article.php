<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livraison_article_id'])) {
    $id = intval($_POST['livraison_article_id']);

    // Vérifie que la ligne existe dans livraison_articles
    $stmt = $pdo->prepare("SELECT * FROM livraison_articles WHERE id = ?");
    $stmt->execute([$id]);
    $livraison = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$livraison) {
        echo "Article introuvable.";
        exit;
    }

    if ($livraison['est_range']) {
        // Déjà rangé, on peut rediriger
        $_SESSION['flash_message'] = "Cet article a déjà été rangé.";
        header('Location: ../pages/admin/rangement.php');
        exit;
    }

    // On commence la transaction
    $pdo->beginTransaction();

    try {
        // 1. Marquer comme rangé
        $updateRange = $pdo->prepare("UPDATE livraison_articles SET est_range = 1 WHERE id = ?");
        $updateRange->execute([$id]);

        // 2. Mettre à jour la quantité en stock dans la table articles
        $article_id = $livraison['article_id'];
        $quantite = $livraison['quantite'];

        $updateArticle = $pdo->prepare("UPDATE articles SET quantite_stock = quantite_stock + ? WHERE id = ?");
        $updateArticle->execute([$quantite, $article_id]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Stock mis à jour.";
        header('Location: ../pages/admin/rangement.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}
?>
