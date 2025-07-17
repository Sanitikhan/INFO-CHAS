<?php
session_start();
require_once '../includes/config.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin/rangement.php'); // Page de redirection par défaut
    exit;
}

// Récupérer et nettoyer les données du formulaire
$id = !empty($_POST['id']) ? intval($_POST['id']) : null;
$numero_livraison = trim($_POST['numero_livraison'] ?? '');
$date_livraison = $_POST['date_livraison'] ?? '';
$fournisseur_id = intval($_POST['fournisseur_id'] ?? 0);
$etat = trim($_POST['etat'] ?? '');
$commentaire = trim($_POST['commentaire'] ?? '');
$date_reception = $_POST['date_reception'] ?? '';

// Validation basique
$errors = [];

if ($numero_livraison === '') {
    $errors[] = "Le numéro de livraison est obligatoire.";
}
if (!$date_livraison) {
    $errors[] = "La date de livraison est obligatoire.";
}
if ($fournisseur_id <= 0) {
    $errors[] = "Le fournisseur est obligatoire.";
}
$validEtats = ['en attente', 'en cours', 'livrée', 'annulée'];
if (!in_array($etat, $validEtats)) {
    $errors[] = "L'état de la livraison est invalide.";
}
if (!$date_reception) {
    $errors[] = "La date de réception est obligatoire.";
}

if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('<br>', $errors);
    header('Location: ../pages/admin/rangement.php');
    exit;
}

try {
    if ($id === null) {
        // Insert sans ID (auto-increment)
        $stmt = $pdo->prepare("INSERT INTO livraisons (numero_livraison, date_livraison, fournisseur_id, etat, commentaire, date_reception) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$numero_livraison, $date_livraison, $fournisseur_id, $etat, $commentaire, $date_reception]);
    } else {
        // Insert avec ID spécifié
        $stmt = $pdo->prepare("INSERT INTO livraisons (id, numero_livraison, date_livraison, fournisseur_id, etat, commentaire, date_reception) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $numero_livraison, $date_livraison, $fournisseur_id, $etat, $commentaire, $date_reception]);
    }

    $_SESSION['flash_message'] = "Livraison ajoutée avec succès !";
} catch (PDOException $e) {
    $_SESSION['flash_message'] = "Erreur lors de l'ajout de la livraison : " . $e->getMessage();
}

// Redirection vers la page de rangement (ou la page où tu affiches les livraisons)
header('Location: ../pages/admin/rangement.php');
exit;
