<?php
session_start();
require_once '../includes/config.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'gestionnaire de livraison' &&
    isset($_POST['livraison_id'], $_POST['livreur_id'])
) {
    $livraison_id = intval($_POST['livraison_id']);
    $livreur_id = intval($_POST['livreur_id']);

    $stmt = $pdo->prepare("UPDATE livraisons SET livreur_id = ? WHERE id = ?");
    $stmt->execute([$livreur_id, $livraison_id]);

    $_SESSION['flash_message'] = "Livreur attribué avec succès.";
    $_SESSION['flash_type'] = "success";
    http_response_code(200);
    exit();
}
http_response_code(400);
echo "Erreur";
exit();
?>