<?php
// Connexion à la base de données
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    // Préparation de la requête SQL
    $stmt = $pdo->prepare("INSERT INTO orders (product_id, supplier_id, quantity, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $supplier_id, $quantity, $status]);

    echo "Commande ajoutée avec succès !";
}

// Récupérer la liste des produits et fournisseurs
$products = $pdo->query("SELECT id, name FROM products")->fetchAll();
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Commande</title>
</head>
<body>
    <h2>Ajouter une Commande</h2>
    <form method="POST" action="add_order.php">
        <label for="product_id">Produit :</label>
        <select id="product_id" name="product_id" required>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="supplier_id">Fournisseur :</label>
        <select id="supplier_id" name="supplier_id" required>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="quantity">Quantité :</label>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <label for="status">Statut de la commande :</label>
        <select id="status" name="status">
            <option value="en cours">En cours</option>
            <option value="livré">Livré</option>
            <option value="annulé">Annulé</option>
        </select><br><br>

        <button type="submit">Ajouter la commande</button>
    </form>
</body>
</html>
