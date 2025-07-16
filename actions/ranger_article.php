<?php
$pdo = new PDO('mysql:host=localhost;dbname=stock_management;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livraison_article_id'])) {
    $id = intval($_POST['livraison_article_id']);

    $stmt = $pdo->prepare("UPDATE livraison_articles SET est_range = 1 WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: rangement.php'); // redirection
    exit;
}
?>