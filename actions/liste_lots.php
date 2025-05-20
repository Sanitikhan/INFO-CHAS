<?php
require_once '../includes/config.php';

$stmt = $pdo->query("SELECT * FROM lots");
$lots = $stmt->fetchAll();

foreach ($lots as $lot) {
    echo "<div>";
    echo "Référence : " . htmlspecialchars($lot['reference']) . "<br>";
    echo "Type : " . htmlspecialchars($lot['type']) . "<br>";
    echo "Quantité totale : " . $lot['quantite_total'] . "<br>";
    echo "Disponible : " . $lot['disponibilite'] . "<br>";
    echo "Réservé : " . $lot['reserve'] . "<br>";
    echo "À venir : " . $lot['a_venir'] . "<br>";
    echo "État : " . $lot['etat'] . "<br>";
    echo "</div><hr>";
}
?>
