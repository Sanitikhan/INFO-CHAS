<?php
require_once '../includes/config.php';
if (
    isset($_POST['id'], $_POST['reference'], $_POST['type'], $_POST['quantite_total'], $_POST['disponibilite'], $_POST['etat'])
) {
    $stmt = $pdo->prepare("UPDATE lots SET reference=?, type=?, quantite_total=?, disponibilite=?, reserve=?, a_venir=?, etat=?, fournisseur_id=? WHERE id=?");
    $stmt->execute([
        $_POST['reference'],
        $_POST['type'],
        $_POST['quantite_total'],
        $_POST['disponibilite'],
        $_POST['reserve'],
        $_POST['a_venir'],
        $_POST['etat'],
        $_POST['fournisseur_id'],
        $_POST['id']
    ]);
}
header('Location: ../pages/admin/stock.php');
exit();