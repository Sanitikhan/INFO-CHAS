<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = $_POST['message'];
    $role = $_POST['role'] ?? null;
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $sender = $_SESSION['username'] ?? 'Inconnu';

    // Only one of role or user_id should be set
    $insert = $pdo->prepare("INSERT INTO alertes (role, user_id, message, sender, created_at) VALUES (?, ?, ?, ?, NOW())");
    $insert->execute([
        $role ?: null,
        $user_id,
        $message,
        $sender
    ]);

    $_SESSION['flash_message'] = "Alerte envoyée.";
    header('Location: ../pages/admin/alertes.php');
    exit();
}
?>