<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $reference = $_POST['reference'];
    $preparateur = $_POST['preparateur'];
    $livreur = $_POST['livreur'];
    $date_commande = $_POST['date_commande'];
    $date_livraison = $_POST['date_livraison'];
    $etat = $_POST['etat'];

    $stmt = $pdo->prepare("UPDATE commandes SET reference=?, preparateur=?, livreur=?, date_commande=?, date_livraison=?, etat=? WHERE id=?");
    $stmt->execute([$reference, $preparateur, $livreur, $date_commande, $date_livraison, $etat, $id]);

    $_SESSION['flash_message'] = "Commande modifiée avec succès.";
    header('Location: ../pages/admin/commandes.php');
    exit();
}
?>