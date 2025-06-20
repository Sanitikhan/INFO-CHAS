<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'];
    $type = $_POST['type'];
    $quantite_total = $_POST['quantite_total'];
    $disponibilite = $_POST['disponibilite'];
    $reserve = $_POST['reserve'] ?? 0;
    $a_venir = $_POST['a_venir'] ?? 0;
    $etat = $_POST['etat'];
    $fournisseur_id = $_POST['fournisseur_id'];
    $emplacement = $_POST['emplacement_id'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO lots 
        (reference, type, quantite_total, disponibilite, reserve, a_venir, etat, fournisseur_id, emplacement_id)
        VALUES 
        (:reference, :type, :quantite_total, :disponibilite, :reserve, :a_venir, :etat, :fournisseur_id, :emplacement_id)"
    );

    $stmt->execute([
        'reference' => $reference,
        'type' => $type,
        'quantite_total' => $quantite_total,
        'disponibilite' => $disponibilite,
        'reserve' => $reserve,
        'a_venir' => $a_venir,
        'etat' => $etat,
        'fournisseur_id' => $fournisseur_id,
        'emplacement_id' => $emplacement
    ]);

    $_SESSION['flash_message'] = "Lot ajouté avec succès.";
    $_SESSION['flash_type'] = "success";
}
header('Location: ../pages/admin/stock.php');
exit();
?>
