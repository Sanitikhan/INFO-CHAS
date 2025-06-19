<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Delete related details first
    $stmt = $pdo->prepare("DELETE FROM livraisons_details WHERE livraison_id = ?");
    $stmt->execute([$id]);

    // Delete the livraison
    $stmt = $pdo->prepare("DELETE FROM livraisons WHERE id = ?");
    $stmt->execute([$id]);

    // Optionally log activity...

    http_response_code(200);
    exit();
} else {
    http_response_code(400);
    echo "ID manquant";
    exit();
}
?>