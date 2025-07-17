<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'])) {
    $id_commande = intval($_POST['id_commande']);

    // 1. Récupérer les détails de la commande (id_article, quantite_commandee)
    $sql = "SELECT id_article, quantite FROM commandes WHERE id_commande = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_commande]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commande) {
        die("Commande introuvable");
    }

    $id_article = $commande['id_article'];
    $quantite_commande = $commande['quantite'];

    // 2. Mettre à jour l'état de la commande en 'pret'
    $sqlUpdateEtat = "UPDATE commandes SET etat = 'pret' WHERE id_commande = ?";
    $stmtUpdateEtat = $pdo->prepare($sqlUpdateEtat);
    $stmtUpdateEtat->execute([$id_commande]);

    // 3. Mettre à jour la quantité en stock dans la table lots
    $sqlUpdateStock = "UPDATE lots SET quantite_stock = quantite_stock - ? WHERE id_article = ?";
    $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);
    $stmtUpdateStock->execute([$quantite_commande, $id_article]);

    echo "Commande mise à jour en 'pret' et stock ajusté.";

} else {
    echo "ID de commande non fourni.";
}
?>
