<?php
session_start();
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';

    if ($nom && $email && $telephone) {
        $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom, email, telephone) VALUES (?, ?, ?)");
        $success = $stmt->execute([$nom, $email, $telephone]);

        if ($success) {
            header('Location: ../pages/admin/fournisseurs.php');
            exit();
        } else {
            echo "Erreur lors de l'ajout du fournisseur.";
        }
    } else {
        echo "Tous les champs sont obligatoires.";
    }
}
?>