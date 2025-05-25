<?php
require_once '../../includes/config.php';

try {
    $stmt = $pdo->query("SELECT * FROM lots");
    $lots = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $lots = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/form.css">
    <link rel="stylesheet" href="../../public/lot.css">
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
                    <a href="stock.php" class="nav-link active">
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
                    <a href="#" class="nav-link">
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
            <h1>STOCK</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-lot-input" placeholder="Rechercher un lot..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <button class="btn btn-add" id="add-lot-btn">Ajouter un lot</button>
        </section>

        <div class="form-section" id="add-lot-form-section" style="display:none;">
            <form action="../actions/ajouter_lot.php" method="POST">
                <input type="text" name="reference" placeholder="Référence" required>
                <select name="type" placeholder="Type">
                    <option value="TOP">Top</option>
                    <option value="BAS">Bas</option>
                    <option value="ENS">Ensemble</option>
                    <option value="DSS">Dessus</option>
                </select>
                <input type="number" name="quantite_total" placeholder="Quantité totale">
                <input type="number" name="disponibilite" placeholder="Disponible">
                <input type="number" name="reserve" placeholder="Réservé">
                <input type="number" name="a_venir" placeholder="À venir">
                <select name="etat">
                    <option value="vert">Vert</option>
                    <option value="orange">Orange</option>
                    <option value="rouge">Rouge</option>
                </select>
                <input type="number" name="fournisseur_id" placeholder="Fournisseur">
                <button type="submit">Ajouter le lot</button>
            </form>
        </div>

        <!-- Affichage des lots existants -->
        <h2>Lots enregistrés</h2>
        <table id="lots-table" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Disponibilité</th>
                    <th>Etat</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($lots as $lot): ?>
                    <tr class="main-row" style="cursor:pointer;">
                        <td><?= htmlspecialchars($lot['reference']) ?></td>
                        <td><?= htmlspecialchars($lot['type']) ?></td>
                        <td><?= htmlspecialchars($lot['quantite_total']) ?></td>
                        <td><?= htmlspecialchars($lot['disponibilite']) ?></td>
                        <td>
                            <span class="etat-square <?= htmlspecialchars($lot['etat']) ?>"></span>
                        </td>
                    </tr>
                    <tr class="details-row" style="display:none; background:#f9f9f9;">
                        <td colspan="8">
                            <table style="width:100%; background:#f9f9f9;">
                                <thead>
                                    <tr>
                                        <th>Fournisseur</th>
                                        <th>Réservé</th>
                                        <th>À venir</th>
                                        <th>Emplacement</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <tr>
                                        <td><?= htmlspecialchars($lot['fournisseur_id']) ?></td>
                                        <td><?= htmlspecialchars($lot['reserve']) ?></td>
                                        <td><?= htmlspecialchars($lot['a_venir']) ?></td>
                                        <td><?= isset($lot['emplacement']) ? htmlspecialchars($lot['emplacement']) : '-' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </section>

    <script>
    document.querySelectorAll('#lots-table .main-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('details-row')) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            }
        });
    });

    document.getElementById('add-lot-btn').addEventListener('click', function() {
    const formSection = document.getElementById('add-lot-form-section');
    formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
    });
    </script>

    <script src="../../actions/search.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>