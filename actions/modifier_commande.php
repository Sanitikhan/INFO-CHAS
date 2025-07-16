<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $dateCommande = $_POST['date_commande'] ?? null;
    $datePrevue = $_POST['date_prevue_envoi'] ?? null;
    $etat = $_POST['etat_preparation'] ?? null;

    if (empty($_POST['date_commande']) || empty($_POST['date_prevue_envoi']) || empty($_POST['etat_preparation'])) {
    die('Tous les champs doivent être remplis.');
}

    $sql = "UPDATE commandes SET date_commande = ?, date_prevue_envoi = ?, etat_preparation = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dateCommande, $datePrevue, $etat, $id]);

    // Redirection ou message de succès
    header("Location: ../pages/admin/commandes.php?success=1");
    exit;
}
?>