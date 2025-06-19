<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Delete related commande_lots first
    $stmt = $pdo->prepare("DELETE FROM commande_lots WHERE commande_id = ?");
    $stmt->execute([$id]);

    // Delete the commande
    $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_message'] = "Commande supprimée avec succès.";
    http_response_code(200);
    echo 'ok';
    exit();
} else {
    http_response_code(400);
    echo "ID manquant";
    exit();
}
?>