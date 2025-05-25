<?php
session_start();
require_once '../includes/config.php';

if (
    isset($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['role'])
) {
    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->execute([$_POST['username'], $_POST['email'], $_POST['role'], $_POST['user_id']]);
    header('Location: ../pages/parameters.php');
    exit();
}
?>