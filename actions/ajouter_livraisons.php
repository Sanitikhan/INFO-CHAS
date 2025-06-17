<?php
require_once '../includes/config.php';
session_start();

if (
    isset($_POST['numero_livraison'], $_POST['fournisseur_id'], $_POST['date_prevue'], $_POST['statut'])
) {
    try {
        $stmt = $pdo->prepare("INSERT INTO livraisons 
            (numero_livraison, fournisseur_id, date_prevue, date_livraison, statut, transporteur, notes, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([
            $_POST['numero_livraison'],
            $_POST['fournisseur_id'],
            $_POST['date_prevue'],
            !empty($_POST['date_livraison']) ? $_POST['date_livraison'] : null,
            $_POST['statut'],
            $_POST['transporteur'] ?? null,
            $_POST['notes'] ?? null,
            $_SESSION['user_id'] ?? null
        ])) {
            $_SESSION['flash_message'] = "Livraison ajoutée avec succès !";
            header('Location: ../pages/admin/livraisons.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Champs obligatoires manquants.";
}
?>
