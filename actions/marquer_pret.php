<?php
$pdo = new PDO('mysql:host=localhost;dbname=stock_management;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $id = intval($_POST['commande_id']);

    $stmt = $pdo->prepare("UPDATE commandes SET etat_preparation = 'prêt' WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: ../pages/admin/commandes.php');
    exit;
}
