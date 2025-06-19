<?php
require_once '../includes/config.php';
session_start();

if (
    isset($_POST['nom'], $_POST['email'], $_POST['telephone'])
) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO fournisseurs (
                nom,
                email,
                telephone
            ) VALUES (?, ?, ?)
        ");

        if ($stmt->execute([
            $_POST['nom'],
            $_POST['email'],
            $_POST['telephone']
        ])) {
            $_SESSION['flash_message'] = "Fournisseur ajouté avec succès.";
            $_SESSION['flash_type'] = "success";
            header('Location: ../pages/admin/fournisseurs.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Champs obligatoires manquants.";
}
?>