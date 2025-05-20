<?php
require_once '../includes/config.php';

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
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/form.css">
    <link rel="icon" href="../img/logo_fc.png" type="image/png">
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
                <img src="../img/logo_fc.png" alt="FASHION CHIC">
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
                    <a href="index.html" class="nav-link">
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
                            <a href="#" class="nav-link dropdown-link">Mes messages</a>
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
        <h1>Bienvenue dans le stock</h1>

        <section class="btn-section">
            <button class="btn btn-search" id="search-lot-btn">Rechercher</button>
            <button class="btn btn-add" id="add-lot-btn">Ajouter un lot</button>
        </section>

        <div class="form-section">
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
        <table border="1" cellpadding="8">
            <tr>
                <th>Référence</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Disponibilité</th>
                <th>Réservé</th>
                <th>A venir</th>
                <th>Fournisseur</th>
                <th>Etat</th>
            </tr>
            <?php foreach ($lots as $lot): ?>
                <tr>
                    <td><?= htmlspecialchars($lot['reference']) ?></td>
                    <td><?= htmlspecialchars($lot['type']) ?></td>
                    <td><?= htmlspecialchars($lot['quantite_total']) ?></td>
                    <td><?= htmlspecialchars($lot['disponibilite']) ?></td>
                    <td><?= htmlspecialchars($lot['reserve']) ?></td>
                    <td><?= htmlspecialchars($lot['a_venir']) ?></td>
                    <td><?= htmlspecialchars($lot['fournisseur_id']) ?></td>
                    <td><?= htmlspecialchars($lot['etat']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Lot</th>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Disponibilité</th>
                    <th>Réservé</th>
                    <th>A venir</th>
                    <th>Fournisseur</th>
                    <th>Etat</th>
                </tr>
            </thead>
            <tbody>
                <tr class="main-row">
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <!-- Hidden Details Row -->
                <tr class="details-row" <!--style="display: none;-->">
                    <td colspan="9">
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>Lieu de stock</th>
                                    <th>Emplacement</th>
                                    <th>Quantité</th>
                                    <th>Disponibilité</th>
                                    <th>Réservé</th>
                                    <th>A venir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>lds_1</td>
                                    <td>emp_1</td>
                                    <td>qtt_1</td>
                                    <td>dispo_1</td>
                                    <td>res_1</td>
                                    <td>ven_1</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
                <tr>
                    <td>lot_1</td>
                    <td>ref_1</td>
                    <td>typ_1</td>
                    <td>qtt_1</td>
                    <td>disp_1</td>
                    <td>res_1</td>
                    <td>ven_1</td>
                    <td>fou_1</td>
                    <td>etat_1</td>
                </tr>
            </tbody>
        </table>

    </section>

    <script src="../actions/script.js"></script>
</body>
</html>