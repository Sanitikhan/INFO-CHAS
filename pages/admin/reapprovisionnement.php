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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réapprovisionnement</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/reapprovisionnement.css">
    <link rel="icon" href="../../img/logo_fc.png" type="image/png">
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>

<!-- Mobile Sidebar Menu Button -->
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <!-- Sidebar Header -->
        <hearder class="sidebar-header">
            <a href="" class="header-logo">
                <img src="../../img/logo_fc.png" alt="FASHION CHIC">
                <!-- Faire en sorte que l'image soit différente quand la sidebar est collapsed -->
            </a>
            <button class="sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </hearder>

        <nav class="sidebar-nav">
            <!-- Primary Top Nav -->
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Tableau de bord</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Tableau de bord</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="stock.php" class="nav-link">
                        <span class="material-symbols-rounded">inventory_2</span>
                        <span class="nav-label">Stock</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Stock</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="reapprovisionnement.php" class="nav-link active">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="nav-label">Réapprovisionnement</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Réapprovisionnement</a>
                        </li>
                    </ul>
                </li>
                <!-- Dropdown -->
                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">local_shipping</span>
                        <span class="nav-label">Livraisons</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Livraisons</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Toutes les livraisons</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Mes livraisons</a>
                        </li>
                    </ul>
                </li>
                <!-- Dropdown -->
                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">mail</span>
                        <span class="nav-label">Messagerie</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Messagerie</a>
                        </li>
                        <li class="nav-item">
                            <a href="messages.php" class="nav-link dropdown-link">Mes messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Mes alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="fournisseurs.php" class="nav-link dropdown-link">Fournisseurs</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-rounded">calendar_today</span>
                        <span class="nav-label">Calendrier</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Calendrier</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Secondary Bottom Nav -->
            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="parameters.php" class="nav-link">
                        <span class="material-symbols-rounded">settings</span>
                        <span class="nav-label">Paramètres</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Paramètres</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <span class="material-symbols-rounded">power_settings_new</span>
                        <span class="nav-label">Déconnexion</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Déconnexion</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>

    <section class="main-content">
        <header class="header">
            <h1>Réapprovisionnement</h1>
        </header>

        <section class="btn-section">
            <input type="text" id="search-lot-input" placeholder="Rechercher un lot..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <button class="btn btn-add" id="add-lot-btn">Ajouter un lot</button>
        </section>

    <section class="content">

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
    </section>



    <script src="../../actions/search.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>