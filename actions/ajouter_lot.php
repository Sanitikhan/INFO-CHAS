<?php
session_start();
require_once '../includes/config.php';

if (
    isset($_POST['nom'], $_POST['categorie'], $_POST['quantite_stock'], $_POST['etat'], $_POST['fournisseur_id'], $_POST['articles'])
) {
    // Insert lot
    $stmt = $pdo->prepare("INSERT INTO lots (categorie, quantite_stock, etat, fournisseur_id, emplacement) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['categorie'],
        $_POST['quantite_stock'],
        $_POST['etat'],
        $_POST['fournisseur_id'],
        $_POST['emplacement'] ?? null
    ]);
    $lot_id = $pdo->lastInsertId();

    // Link articles to lot
    foreach ($_POST['articles'] as $article_id) {
        $stmt2 = $pdo->prepare("INSERT INTO article_lot (article_id, lot_id) VALUES (?, ?)");
        $stmt2->execute([$article_id, $lot_id]);
    }

    $_SESSION['flash_message'] = "Lot créé avec succès.";
    $_SESSION['flash_type'] = "success";
    header('Location: ../pages/admin/lots.php');
    exit();
}
?>