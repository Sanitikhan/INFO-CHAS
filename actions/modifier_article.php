<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom_article = $_POST['nom_article'];
    $reference = $_POST['reference'];
    $categorie = $_POST['categorie'];
    $quantite_stock = $_POST['quantite_stock'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $etat = $_POST['etat'];

    $stmt = $pdo->prepare("UPDATE articles SET nom_article=?, reference=?, categorie=?, quantite_stock=?, prix_unitaire=?, etat=? WHERE id=?");
    $stmt->execute([
        $nom_article,
        $reference,
        $categorie,
        $quantite_stock,
        $prix_unitaire,
        $etat,
        $id
    ]);

    $_SESSION['flash_message'] = "Article modifié avec succès.";
    header('Location: ../pages/admin/articles.php');
    exit();
}
?>