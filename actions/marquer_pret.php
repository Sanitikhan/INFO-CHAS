<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commande_id = intval($_POST['commande_id']);

    // 1. Récupérer les lots et quantités liés à la commande
    $sql = "SELECT cl.lot_id, cl.quantite_lot_commande, l.quantite_stock 
            FROM commande_lot cl 
            JOIN lots l ON cl.lot_id = l.id 
            WHERE cl.commande_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$commande_id]);
    $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$lots) {
        $_SESSION['flash_message'] = "Aucun lot trouvé pour cette commande.";
        $_SESSION['flash_type'] = "error";
        header("Location: commandes.php");
        exit();
    }

    $pdo->beginTransaction();

    try {
        foreach ($lots as $lot) {
            $lot_id = $lot['lot_id'];
            $quantite_commande = $lot['quantite_lot_commande'];
            $quantite_stock = $lot['quantite_stock'];

            if ($quantite_stock < $quantite_commande) {
                throw new Exception("Stock insuffisant pour le lot $lot_id");
            }

            // Mise à jour du stock du lot
            $sqlUpdateStock = "UPDATE lots SET quantite_stock = quantite_stock - ? WHERE id = ?";
            $pdo->prepare($sqlUpdateStock)->execute([$quantite_commande, $lot_id]);

            // Mise à jour de l'état du lot
            $sqlUpdateEtatLot = "UPDATE lots SET etat = 'prêt' WHERE id = ?";
            $pdo->prepare($sqlUpdateEtatLot)->execute([$lot_id]);

            // Mise à jour des articles dans le lot
            $sqlArticles = "SELECT article_id, quantite FROM article_lot WHERE lot_id = ?";
            $stmtArticles = $pdo->prepare($sqlArticles);
            $stmtArticles->execute([$lot_id]);
            $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

            foreach ($articles as $article) {
                $article_id = $article['article_id'];
                $quantite_par_lot = $article['quantite'];
                $quantite_totale_a_retirer = $quantite_par_lot * $quantite_commande;

                $sqlUpdateArticleStock = "UPDATE articles SET quantite_stock = quantite_stock - ? WHERE id = ?";
                $pdo->prepare($sqlUpdateArticleStock)->execute([$quantite_totale_a_retirer, $article_id]);
            }
        }

        // Mise à jour de l'état de la commande
        $sqlUpdateCommande = "UPDATE commandes SET etat_preparation = 'prêt' WHERE id = ?";
        $pdo->prepare($sqlUpdateCommande)->execute([$commande_id]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Commande marquée comme prête et stock mis à jour.";
        $_SESSION['flash_type'] = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Erreur : " . $e->getMessage();
        $_SESSION['flash_type'] = "error";
    }

    header("Location: commandes.php");
    exit();

} else {
    $_SESSION['flash_message'] = "ID de commande non fourni.";
    $_SESSION['flash_type'] = "error";
    header("Location: commandes.php");
    exit();
}
