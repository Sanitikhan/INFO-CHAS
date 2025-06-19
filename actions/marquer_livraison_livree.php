<?php
session_start();
require_once '../includes/config.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['livraison_id']) &&
    isset($_SESSION['user_id']) &&
    $_SESSION['role'] === 'livreur'
) {
    $livraison_id = intval($_POST['livraison_id']);
    $user_id = $_SESSION['user_id'];

    // Only allow if this livraison is assigned to this livreur
    $stmt = $pdo->prepare("SELECT * FROM livraisons WHERE id = ? AND livreur_id = ?");
    $stmt->execute([$livraison_id, $user_id]);
    $livraison = $stmt->fetch();

    if ($livraison) {
        $stmt = $pdo->prepare("UPDATE livraisons SET statut = 'livrée', date_livraison = NOW() WHERE id = ?");
        $stmt->execute([$livraison_id]);
        $_SESSION['flash_message'] = "Livraison marquée comme livrée.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Action non autorisée.";
        $_SESSION['flash_type'] = "error";
    }
    header('Location: ../livreur/meslivraisons.php');
    exit();
}
header('Location: ../livreur/meslivraisons.php');
exit();
?>