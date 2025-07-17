<?php
session_start();
require_once '../includes/config.php';

// Récupérer données du formulaire
$date_commande = $_POST['date_commande'] ?? null;
$date_prevue_envoi = $_POST['date_prevue_envoi'] ?? null;
$etat_preparation = $_POST['etat_preparation'] ?? null;
$lots = $_POST['lots'] ?? [];
$quantites = $_POST['quantite_lot_commande'] ?? [];

// Valider les données (exemple rapide)
if(empty($_POST['date_commande']) || empty($_POST['date_prevue_envoi']) || empty($_POST['etat_preparation'])) {
    echo "Tous les champs doivent être remplis";
    exit;
}

// Insérer la commande
$stmt = $pdo->prepare("INSERT INTO commandes (date_commande, date_prevue_envoi, etat_preparation) VALUES (?, ?, ?)");
$stmt->execute([$date_commande, $date_prevue_envoi, $etat_preparation]);
$commande_id = $pdo->lastInsertId();

// Insérer les lots liés
foreach ($lots as $lot_id => $value) {
    $qte = (int)($quantites[$lot_id] ?? 1);
    if ($qte > 0) {
        $stmt2 = $pdo->prepare("INSERT INTO commande_lot (commande_id, lot_id, quantite_lot_commande) VALUES (?, ?, ?)");
        $stmt2->execute([$commande_id, $lot_id, $qte]);
    }
}

header("Location: ../pages/admin/commandes.php");
exit;
?>