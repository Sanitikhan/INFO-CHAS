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
        die("Aucun lot trouvé pour cette commande.");
    }

    // 2. Démarrer une transaction pour assurer cohérence
    $pdo->beginTransaction();

    try {
        // 3. Mettre à jour chaque lot
        foreach ($lots as $lot) {
            $lot_id = $lot['lot_id'];
            $quantite_commande = $lot['quantite_lot_commande'];
            $quantite_stock = $lot['quantite_stock'];

            if ($quantite_stock < $quantite_commande) {
                throw new Exception("Stock insuffisant pour le lot $lot_id");
            }

            // Mettre à jour la quantité stock
            $sqlUpdateStock = "UPDATE lots SET quantite_stock = quantite_stock - ? WHERE id = ?";
            $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);
            $stmtUpdateStock->execute([$quantite_commande, $lot_id]);

            // Mettre à jour l'état du lot
            $sqlUpdateEtatLot = "UPDATE lots SET etat = 'prêt' WHERE id = ?";
            $stmtUpdateEtatLot = $pdo->prepare($sqlUpdateEtatLot);
            $stmtUpdateEtatLot->execute([$lot_id]);
        }

        // 4. Mettre à jour l'état de la commande
        $sqlUpdateCommande = "UPDATE commandes SET etat_preparation = 'prêt' WHERE id = ?";
        $stmtUpdateCommande = $pdo->prepare($sqlUpdateCommande);
        $stmtUpdateCommande->execute([$commande_id]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Stock mis à jour.";
        $_SESSION['flash_type'] = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
    }

} else {
    echo "ID de commande non fourni.";
}
