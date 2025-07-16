<?php
session_start();
require_once '../includes/config.php';
if (
    isset($_POST['id'], $_POST['reference'], $_POST['type'], $_POST['quantite_total'], $_POST['disponibilite'], $_POST['etat'])
) {
    $stmt = $pdo->prepare("UPDATE lots SET reference=?, type=?, quantite_total=?, disponibilite=?, reserve=?, a_venir=?, etat=?, fournisseur_id=? WHERE id=?");
    $stmt->execute([
        $_POST['categorie'],
        $_POST['quantite_stock'],
        $_POST['etat'],
        $_POST['fournisseur_id'],
        $_POST['emplacement'],
        $_POST['id']
    ]);
    $_SESSION['flash_message'] = "Lot modifié avec succès.";
    $_SESSION['flash_type'] = "success";
}
header('Location: ../pages/admin/stock.php');
exit();