<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'];
    $type = $_POST['type'];
    $quantite_total = $_POST['quantite_total'];
    $disponibilite = $_POST['disponibilite'];
    $reserve = $_POST['reserve'];
    $a_venir = $_POST['a_venir'];
    $etat = $_POST['etat'];
    $fournisseur_id = $_POST['fournisseur_id'];

    $stmt = $pdo->prepare("INSERT INTO lots 
        (reference, type, quantite_total, disponibilite, reserve, a_venir, etat, fournisseur_id)
        VALUES 
        (:reference, :type, :quantite_total, :disponibilite, :reserve, :a_venir, :etat, :fournisseur_id)"
    );

    $stmt->execute([
        'reference' => $reference,
        'type' => $type,
        'quantite_total' => $quantite_total,
        'disponibilite' => $disponibilite,
        'reserve' => $reserve,
        'a_venir' => $a_venir,
        'etat' => $etat,
        'fournisseur_id' => $fournisseur_id
    ]);

    echo "Lot ajouté avec succès !";
}
?>
