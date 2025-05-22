<?php
session_start(); // Start the session

require_once '../includes/config.php'; // Adjust the path to your DB connection file

if (
    isset($_POST['send_message']) &&
    isset($_POST['receveur_id'], $_POST['objet'], $_POST['corps']) &&
    !empty($_POST['receveur_id']) &&
    !empty($_POST['objet']) &&
    !empty($_POST['corps'])
) {
    $expediteur_id = $_SESSION['user_id'];
    $receveur_id = $_POST['receveur_id'];
    $objet = $_POST['objet'];
    $corps = $_POST['corps'];

    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, receveur_id, objet, corps) VALUES (?, ?, ?, ?)");
    $stmt->execute([$expediteur_id, $receveur_id, $objet, $corps]);
    // Optionally redirect or set a success message here
    header('Location: ../pages/messages.php');
    exit();
} else if (isset($_POST['send_message'])) {
    // Optionally handle the error: missing fields
    // Example: echo "Tous les champs sont obligatoires.";
}

// Fetch received messages for the logged-in user
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT m.*, u.username AS sender_name 
        FROM messages m 
        JOIN users u ON m.expediteur_id = u.id 
        WHERE m.receveur_id = ? 
        ORDER BY m.id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $received_messages = $stmt->fetchAll();
} else {
    $received_messages = [];
}
?>