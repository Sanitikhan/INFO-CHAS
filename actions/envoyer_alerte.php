<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message']) && !empty($_POST['role'])) {
    $message = $_POST['message'];
    $role = $_POST['role'];
    $sender = $_SESSION['username'] ?? 'Inconnu';

    $insert = $pdo->prepare("INSERT INTO alertes (role, message, sender, created_at) VALUES (?, ?, ?, NOW())");
    $insert->execute([$role, $message, $sender]);

    $_SESSION['flash_message'] = "Alerte envoyée aux utilisateurs avec le rôle '$role'.";
    header('Location: ../pages/admin/alertes.php');
    exit();
}
?>