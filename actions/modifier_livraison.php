<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $numero = $_POST['numero_livraison'];
    $fournisseur_id = $_POST['fournisseur_id'];
    $date_prevue = $_POST['date_prevue'];
    $statut = $_POST['statut'];
    $livreur_id = $_POST['livreur_id'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("UPDATE livraisons SET numero_livraison=?, fournisseur_id=?, date_prevue=?, statut=?, livreur_id=?, notes=? WHERE id=?");
    $stmt->execute([$numero, $fournisseur_id, $date_prevue, $statut, $livreur_id, $notes, $id]);

    $_SESSION['flash_message'] = "Livraison modifiée avec succès.";
    header('Location: ../pages/admin/livraisons.php');
    exit();
}
?>