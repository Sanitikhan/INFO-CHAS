<?php
session_start();
require_once('../../includes/config.php');

// Fetch all lots
$stmt = $pdo->query("SELECT * FROM lots");
$lots = $stmt->fetchAll();

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $lot_id = $_POST['lot_id'];
    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Add lot to cart (you can add quantity logic if needed)
    $_SESSION['cart'][] = $lot_id;
    // Optional: Redirect to avoid resubmission
    header('Location: reapprovisionnement.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réapprovisionnement</title>
    <style>
        table { width: 90%; margin: 2em auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        .btn { padding: 6px 16px; border: none; background: #4caf50; color: #fff; border-radius: 4px; cursor: pointer; }
        .btn:disabled { background: #aaa; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Réapprovisionnement - Lots disponibles</h1>
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Type</th>
                <th>Quantité totale</th>
                <th>Disponibilité</th>
                <th>Fournisseur</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lots as $lot): ?>
                <tr>
                    <td><?= htmlspecialchars($lot['reference']) ?></td>
                    <td><?= htmlspecialchars($lot['type']) ?></td>
                    <td><?= htmlspecialchars($lot['quantite_total']) ?></td>
                    <td><?= htmlspecialchars($lot['disponibilite']) ?></td>
                    <td><?= htmlspecialchars($lot['fournisseur_id']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn"
                                <?php if (isset($_SESSION['cart']) && in_array($lot['id'], $_SESSION['cart'])) echo 'disabled'; ?>>
                                <?= (isset($_SESSION['cart']) && in_array($lot['id'], $_SESSION['cart'])) ? 'Ajouté' : 'Ajouter au panier' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="text-align:center; margin-top:2em;">
        <a href="cart.php" class="btn">Voir mon panier</a>
    </div>
</body>
</html>