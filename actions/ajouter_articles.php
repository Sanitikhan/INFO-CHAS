<?php
require_once '../includes/config.php';
session_start();

if (
    isset($_POST['nom_article'], $_POST['reference'], $_POST['categorie'], $_POST['couleur'], $_POST['taille'], $_POST['quantite_stock'], $_POST['etat'])
) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO articles (
                nom_article,
                reference,
                categorie,
                couleur,
                taille,
                quantite_stock,
                etat
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([
            $_POST['nom_article'],
            $_POST['reference'],
            $_POST['categorie'],
            $_POST['couleur'],
            $_POST['taille'],
            $_POST['quantite_stock'] ?? null,
            $_POST['etat']
        ])) {
            $_SESSION['flash_message'] = "Article ajouté avec succès !";
            header('Location: ../pages/admin/articles.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Champs obligatoires manquants.";
}
?>
