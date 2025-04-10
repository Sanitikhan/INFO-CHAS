<?php
// Connexion à la base de données
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Préparation de la requête SQL
    $stmt = $pdo->prepare("INSERT INTO products (name, quantity, price, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $quantity, $price, $description]);

    echo "Produit ajouté avec succès !";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
</head>
<body>
    <h2>Ajouter un Produit</h2>
    <form method="POST" action="add_product.php">
        <label for="name">Nom du produit :</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="quantity">Quantité :</label>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <label for="price">Prix :</label>
        <input type="text" id="price" name="price" required><br><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description"></textarea><br><br>

        <button type="submit">Ajouter le produit</button>
    </form>
</body>
</html>
