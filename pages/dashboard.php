<?php
session_start();
echo "<pre>"; print_r($_SESSION); echo "</pre>";

// Connexion à la base de données
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer tous les produits
$products = $pdo->query("SELECT * FROM products")->fetchAll();

// Récupérer tous les fournisseurs
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();

// Récupérer toutes les commandes
$orders = $pdo->query("SELECT * FROM orders")->fetchAll();

// Ajout d'un produit
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO products (name, quantity, price, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_name, $quantity, $price, $description]);

    // Redirection pour éviter la soumission multiple
    header("Location: dashboard.php");
    exit();
}

// Ajout d'un fournisseur
if (isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("INSERT INTO suppliers (name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$supplier_name, $email, $phone]);

    // Redirection pour éviter la soumission multiple
    header("Location: dashboard.php");
    exit();
}

// Ajout d'une commande
if (isset($_POST['add_order'])) {
    // Vérification et récupération des données du formulaire
    $product_id = $_POST['product_id'] ?? null;
    $supplier_id = $_POST['supplier_id'] ?? null;
    $quantity_ordered = $_POST['quantity_ordered'] ?? null;
    $status = $_POST['status'] ?? null;

    // Debugging : Afficher les variables pour vérifier
    echo "Product ID: " . $product_id . "<br>";
    echo "Supplier ID: " . $supplier_id . "<br>";
    echo "Quantity Ordered: " . $quantity_ordered . "<br>";
    echo "Status: " . $status . "<br>";

    // Vérifier si toutes les valeurs sont bien définies
    if ($product_id && $supplier_id && $quantity_ordered && $status) {
        // Insertion dans la base de données
        $stmt = $pdo->prepare("INSERT INTO orders (product_id, supplier_id, quantity, status, order_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$product_id, $supplier_id, $quantity_ordered, $status]);

        // Redirection pour éviter la soumission multiple
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erreur : Certaines valeurs sont manquantes.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/styles.css">
    <title>Tableau de Bord - Gestion de Stock</title>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="#products">Produits</a>
        <a href="#suppliers">Fournisseurs</a>
        <a href="#orders">Commandes</a>
        <a href="logout.php">Se Déconnecter</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Bienvenue, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Votre rôle : <?php echo $_SESSION['role']; ?></p>

        <h1 id="products">Tableau de Bord - Gestion de Stock</h1>

    <!-- Affichage des produits -->
    <h2>Liste des Produits</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Quantité</th>
                <th>Prix</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['price']; ?> €</td>
                    <td><?php echo $product['description']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Ajouter un Produit -->
    <h2>Ajouter un Produit</h2>
    <form action="dashboard.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center; width: 50%; margin: 0 auto; background-color: var(--secondary-color); padding: 20px; border-radius: 10px;">
        <!-- Left column -->
        <label for="product_name" style="grid-column: 1; grid-row: 1;">Nom du Produit:</label>
        <input type="text" id="product_name" name="product_name" required style="grid-column: 1; grid-row: 2;">

        <label for="quantity" style="grid-column: 1; grid-row: 3;">Quantité:</label>
        <input type="number" id="quantity" name="quantity" required style="grid-column: 1; grid-row: 4;">

        <!-- Right column -->
        <label for="price" style="grid-column: 2; grid-row: 1;">Prix (€):</label>
        <input type="number" step="0.01" id="price" name="price" required style="grid-column: 2; grid-row: 2;">

        <label for="description" style="grid-column: 2; grid-row: 3;">Description:</label>
        <textarea id="description" name="description" required style="grid-column: 2; grid-row: 4;"></textarea>

        <!-- Button spanning both columns -->
        <button type="submit" name="add_product" style="grid-column: 1 / span 2; grid-row: 5;">Ajouter le Produit</button>
    </form>

    <!-- Affichage des fournisseurs -->
    <h2>Liste des Fournisseurs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?php echo $supplier['id']; ?></td>
                    <td><?php echo $supplier['name']; ?></td>
                    <td><?php echo $supplier['email']; ?></td>
                    <td><?php echo $supplier['phone']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Ajouter un Fournisseur -->
    <h2>Ajouter un Fournisseur</h2>
    <form action="dashboard.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center; width: 50%; margin: 0 auto; background-color: var(--secondary-color); padding: 20px; border-radius: 10px;">
        <!-- Left column -->
        <label for="supplier_name" style="grid-column: 1; grid-row: 1;">Nom du Fournisseur:</label>
        <input type="text" id="supplier_name" name="supplier_name" required style="grid-column: 1; grid-row: 2;">

        <label for="email" style="grid-column: 1; grid-row: 3;">Email:</label>
        <input type="email" id="email" name="email"  required style="grid-column: 1; grid-row: 4;">

        <!-- Right column -->
        <label for="phone" style="grid-column: 2; grid-row: 1;">Téléphone:</label>
        <input type="text" id="phone" name="phone" required style="grid-column: 2; grid-row: 2;">

        <!-- Button spanning both columns -->
        <button type="submit" name="add_supplier" style="grid-column: 1 / span 2;">Ajouter le Fournisseur</button>
    </form>

    <!-- Affichage des commandes -->
    <h2>Liste des Commandes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Fournisseur</th>
                <th>Quantité</th>
                <th>Statut</th>
                <th>Date de Commande</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php
                    // Récupérer le nom du produit
                    $product = $pdo->query("SELECT name FROM products WHERE id = " . $order['product_id'])->fetch();
                    // Récupérer le nom du fournisseur
                    $supplier = $pdo->query("SELECT name FROM suppliers WHERE id = " . $order['supplier_id'])->fetch();
                ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $supplier['name']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Ajouter une Commande -->
    <h2>Ajouter une Commande</h2>
    <form action="dashboard.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center; width: 50%; margin: 0 auto; background-color: var(--secondary-color); padding: 20px; border-radius: 10px;">
        <!-- Left column -->
        <label for="product_id" style="grid-column: 1; grid-row: 1;">Produit:</label>
        <select id="product_id" name="product_id" required style="grid-column: 1; grid-row: 2;">
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="supplier_id" style="grid-column: 1; grid-row: 3;">Fournisseur:</label>
        <select id="supplier_id" name="supplier_id" required style="grid-column: 1; grid-row: 4;">
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Right column -->
        <label for="quantity_ordered" style="grid-column: 2; grid-row: 1;">Quantité Commandée:</label>
        <input type="number" id="quantity_ordered" name="quantity_ordered" required style="grid-column: 2; grid-row: 2;">

        <label for="status" style="grid-column: 2; grid-row: 3;">Statut:</label>
        <select id="status" name="status" required style="grid-column: 2; grid-row: 4;">
            <option value="pending">En Attente</option>
            <option value="shipped">Expédiée</option>
            <option value="delivered">Livrée</option>
        </select>

        <!-- Button spanning both columns -->
        <button type="submit" name="add_order" style="grid-column: 1 / span 2;">Ajouter la Commande</button>
    </form>

</body>
</html>
