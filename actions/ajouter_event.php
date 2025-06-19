<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';
    if ($title && $date) {
        $stmt = $pdo->prepare("INSERT INTO evenements (title, date) VALUES (?, ?)");
        $stmt->execute([$title, $date]);
        echo 'ok';
        exit;
    }
}
http_response_code(400);
echo 'Erreur';