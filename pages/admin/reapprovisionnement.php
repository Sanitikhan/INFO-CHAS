<?php
session_start();
require_once('../../includes/config.php');

// Fetch all lots
$stmt = $pdo->query("SELECT * FROM lots");
$lots = $stmt->fetchAll();

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $lot_id = $_POST['lot_id'];
    $quantity = max(1, intval($_POST['quantity']));
    // Fetch the lot to get its fournisseur_id
    $stmt = $pdo->prepare("SELECT fournisseur_id FROM lots WHERE id = ?");
    $stmt->execute([$lot_id]);
    $lot = $stmt->fetch();
    if ($lot) {
        $fournisseur_id = $lot['fournisseur_id'];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Group by fournisseur, then by lot
        if (!isset($_SESSION['cart'][$fournisseur_id])) {
            $_SESSION['cart'][$fournisseur_id] = [];
        }
        $_SESSION['cart'][$fournisseur_id][$lot_id] = $quantity;
    }
    header('Location: reapprovisionnement.php');
    exit();
}

// Fetch all lots with fournisseur name
$stmt = $pdo->query("
    SELECT lots.*, fournisseurs.nom AS fournisseur_nom
    FROM lots
    LEFT JOIN fournisseurs ON lots.fournisseur_id = fournisseurs.id
");
$lots = $stmt->fetchAll();

$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réapprovisionnement</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/reapprovisionnement.css">
    <link rel="stylesheet" href="../../public/cart.css">
    <link rel="icon" href="../../img/logo_w.png" type="image/png">
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
                <img src="../../img/logo_w.png" alt="FASHION CHIC">
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
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'livreur'): ?>
                    <!-- Dropdown for livreur -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">local_shipping</span>
                            <span class="nav-label">Livraisons</span>
                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Livraisons</a>
                            </li>
                            <li class="nav-item">
                                <a href="livraisons.php" class="nav-link dropdown-link">Toutes les livraisons</a>
                            </li>
                            <li class="nav-item">
                                <a href="../livreur/meslivraisons.php" class="nav-link dropdown-link">Mes livraisons</a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Simple link for others -->
                    <li class="nav-item">
                        <a href="livraisons.php" class="nav-link">
                            <span class="material-symbols-rounded">local_shipping</span>
                            <span class="nav-label">Livraisons</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Livraisons</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
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
                            <a href="alertes.php" class="nav-link dropdown-link">Alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="fournisseurs.php" class="nav-link dropdown-link">Fournisseurs</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="calendrier.php" class="nav-link">
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
                    <a href="../logout.php" class="nav-link">
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
            <div class="btn-section-right">
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <div style="text-align:center;">
                        <button class="btn" id="open-cart-modal">Voir mon panier</button>
                    </div>
                    <button class="btn btn-add" id="add-lot-btn">Ajouter un lot</button>
                <?php endif; ?>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="name">Référence</option>
                        <option value="quantity">Quantité</option>
                        <option value="type">Type</option>
                        <option value="fournisseur">Fournisseur</option>
                    </select>
                </div>
            </div>
        </section>

    <section class="content">
        
    <table id="lots-table">
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
                    <td><?= htmlspecialchars($lot['fournisseur_nom']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" style="width:60px;" required>
                            <button type="submit" name="add_to_cart" class="btn"
                                <?php if (isset($_SESSION['cart']) && array_key_exists($lot['id'], $_SESSION['cart'])) echo 'disabled'; ?>>
                                <?= (isset($_SESSION['cart']) && array_key_exists($lot['id'], $_SESSION['cart'])) ? 'Ajouté' : 'Ajouter au panier' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </section>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="close-cart-modal">&times;</span>
            <h2>Mon Panier</h2>
            <?php
            $cart = $_SESSION['cart'] ?? [];
            if (empty($cart)): ?>
                <p>Votre panier est vide.</p>
            <?php else: ?>
                <?php foreach ($cart as $fournisseur_id => $lots): ?>
                    <h3>
                        Fournisseur: 
                        <?php
                        $stmt = $pdo->prepare("SELECT nom FROM fournisseurs WHERE id = ?");
                        $stmt->execute([$fournisseur_id]);
                        echo htmlspecialchars($stmt->fetchColumn());
                        ?>
                    </h3>
                    <ul>
                        <?php if (is_array($lots)): ?>
                            <?php foreach ($lots as $lot_id => $quantity): ?>
                                <li>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT reference, type FROM lots WHERE id = ?");
                                    $stmt->execute([$lot_id]);
                                    $lot = $stmt->fetch();
                                    ?>
                                    <?= htmlspecialchars($lot['reference']) ?> (<?= htmlspecialchars($lot['type']) ?>) - <strong>Quantité : <?= $quantity ?></strong>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                <?php endforeach; ?>
                <form method="post">
                    <button type="submit" name="place_order" class="btn">Passer la commande</button>
                </form>
            <?php endif; ?>
        </div>
    </div>


    <script src="../../actions/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sort-select');
            const table = document.getElementById('lots-table');
            const tbody = table.querySelector('tbody');

            // Map sort type to column index
            const colIndexes = {
                name: 0,        // Référence
                type: 1,        // Type
                quantity: 2,    // Quantité
                fournisseur: 4  // Fournisseur (adjust if needed)
            };

            sortSelect.addEventListener('change', function() {
                const sortType = this.value;
                const rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    let valA = a.cells[colIndexes[sortType]].textContent.trim().toLowerCase();
                    let valB = b.cells[colIndexes[sortType]].textContent.trim().toLowerCase();

                    if (sortType === 'quantity') {
                        return (parseInt(valA, 10) || 0) - (parseInt(valB, 10) || 0);
                    }
                    return valA.localeCompare(valB, undefined, {numeric: true});
                });

                // Remove and re-add sorted rows
                while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
                rows.forEach(row => tbody.appendChild(row));
            });
        });

        document.getElementById('open-cart-modal').onclick = function() {
            document.getElementById('cart-modal').style.display = 'block';
        };
        document.getElementById('close-cart-modal').onclick = function() {
            document.getElementById('cart-modal').style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target == document.getElementById('cart-modal')) {
                document.getElementById('cart-modal').style.display = 'none';
            }
        };
</script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Place your order logic here (insert into commandes, etc.)
    unset($_SESSION['cart']);
    $_SESSION['flash_message'] = "Commande passée avec succès !";
    header('Location: reapprovisionnement.php');
    exit();
}
?>