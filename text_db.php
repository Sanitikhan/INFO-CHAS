<?php
include 'includes/config.php';

try {
    $query = $pdo->query("SHOW TABLES");
    echo "Connexion réussie. Tables dans la base de données :<br>";
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Tables_in_stock_management'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
