<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom_article = $_POST['nom_article'];
    $reference = $_POST['reference'];
    $categorie = $_POST['categorie'];
    $couleur = $_POST['couleur'];
    $taille = $_POST['taille'];
    $quantite_stock = $_POST['quantite_stock'];
    $etat = $_POST['etat'];

    $stmt = $pdo->prepare("UPDATE articles SET nom_article=?, reference=?, categorie=?, couleur=?, taille=?, quantite_stock=?, etat=? WHERE id=?");
    $stmt->execute([
        $nom_article,
        $reference,
        $categorie,
        $couleur,
        $taille,
        $quantite_stock,
        $etat,
        $id
    ]);

    $_SESSION['flash_message'] = "Article modifié avec succès.";
    header('Location: ../pages/admin/articles.php');
    exit();
}
?>