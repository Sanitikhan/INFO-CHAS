<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'] ?? '';
    $preparateur = $_POST['preparateur'] ?? null;
    $livreur = $_POST['livreur'] ?? null;
    $date_commande = $_POST['date_commande'] ?? null;
    $date_livraison = $_POST['date_livraison'] ?? null;
    $etat = $_POST['etat'] ?? null;

    if ($reference) {
        $stmt = $pdo->prepare("INSERT INTO commandes (reference, preparateur, livreur, date_commande, date_livraison, etat) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$reference, $preparateur, $livreur, $date_commande, $date_livraison, $etat]);
    }
    header('Location: ../pages/admin/commandes.php');
    exit();
}
?>