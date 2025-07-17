<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livraison_id'])) {
    $livraisonId = intval($_POST['livraison_id']);

    // Récupérer les lots de cette livraison
    $stmt = $pdo->prepare("
        SELECT ll.lot_id, ll.quantite AS quantite_lot
        FROM livraison_lot ll
        WHERE ll.livraison_id = ?
    ");
    $stmt->execute([$livraisonId]);
    $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lots as $lot) {
        $lotId = $lot['lot_id'];
        $quantiteLot = $lot['quantite_lot'];

        // Récupérer les articles dans ce lot
        $stmtArticles = $pdo->prepare("
            SELECT al.article_id, al.quantite
            FROM article_lot al
            WHERE al.lot_id = ?
        ");
        $stmtArticles->execute([$lotId]);
        $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

        foreach ($articles as $article) {
            $articleId = $article['article_id'];
            $quantiteDansLot = $article['quantite'];

            // Calculer la quantité totale à ajouter
            $quantiteTotale = $quantiteDansLot * $quantiteLot;

            // Mettre à jour la quantité dans la table articles
            $stmtUpdate = $pdo->prepare("
                UPDATE articles
                SET quantite = quantite + ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([$quantiteTotale, $articleId]);
        }
    }

    // Mettre à jour l'état de la livraison
    $pdo->prepare("UPDATE livraisons SET statut = 'rangée' WHERE id = ?")->execute([$livraisonId]);

    // Redirection ou message
    header("Location: ../pages/admin/rangement.php?success=1");
    exit;
} else {
    // page erreur
    header("Location: ../pages/admin/rangement.php?error=1");
    exit;
}
