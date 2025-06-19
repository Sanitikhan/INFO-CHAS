<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $pdo->prepare("DELETE FROM fournisseurs WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_message'] = "Fournisseur supprimé avec succès.";
    $_SESSION['flash_type'] = "success";
    http_response_code(200);
    exit();
} else {
    http_response_code(400);
    echo "ID manquant";
    exit();
}
?>