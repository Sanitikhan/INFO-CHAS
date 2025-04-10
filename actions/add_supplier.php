<?php
// Connexion à la base de données
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Préparation de la requête SQL
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $phone]);

    echo "Fournisseur ajouté avec succès !";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Fournisseur</title>
</head>
<body>
    <h2>Ajouter un Fournisseur</h2>
    <form method="POST" action="add_supplier.php">
        <label for="name">Nom du fournisseur :</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Téléphone :</label>
        <input type="text" id="phone" name="phone"><br><br>

        <button type="submit">Ajouter le fournisseur</button>
    </form>
</body>
</html>
