<?php
session_start();
require_once '../includes/config.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'gestionnaire de livraison' &&
    isset($_POST['livraison_id'])
) {
    $livraison_id = intval($_POST['livraison_id']);

    $stmt = $pdo->prepare("UPDATE livraisons SET statut = 'livrée', date_livraison = NOW() WHERE id = ?");
    $stmt->execute([$livraison_id]);

    $_SESSION['flash_message'] = "Livraison confirmée comme livrée.";
    $_SESSION['flash_type'] = "success";
    http_response_code(200);
    exit();
}
http_response_code(400);
echo "Erreur";
exit();
?>