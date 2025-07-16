<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/

$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

$articles = $pdo->query("
    SELECT a.*, 
        GROUP_CONCAT(CONCAT('Lot n°', l.id) SEPARATOR ', ') AS lots
    FROM articles a
    LEFT JOIN article_lot al ON a.id = al.article_id
    LEFT JOIN lots l ON al.lot_id = l.id
    GROUP BY a.id
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch lots with their linked articles
$lots = $pdo->query("
    SELECT l.*, 
        f.nom AS fournisseur_nom,
        GROUP_CONCAT(DISTINCT a.nom_article SEPARATOR ', ') AS articles
    FROM lots l
    LEFT JOIN article_lot al ON l.id = al.lot_id
    LEFT JOIN articles a ON al.article_id = a.id
    LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
    GROUP BY l.id
")->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['lot_basket'])) {
    $_SESSION['lot_basket'] = [];
}

// Add article to basket (only one reference allowed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_basket'])) {
    $article_id = intval($_POST['article_id']);
    $_SESSION['lot_basket'] = [$article_id]; // Only one article in basket
    header('Location: lots.php');
    exit();
}

// Remove article from basket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_basket'])) {
    $_SESSION['lot_basket'] = [];
    header('Location: lots.php');
    exit();
}

// Create lot from basket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_lot'])) {
    $categorie = $_POST['categorie'];
    $etat = $_POST['etat'];
    $fournisseur_id = $_POST['fournisseur_id'];
    $emplacement = $_POST['emplacement'] ?? null;
    $article_id = $_SESSION['lot_basket'][0];
    $quantities = $_POST['quantities']; // [color][size] => quantity

    // Calculate total quantity
    $quantite_stock = 0;
    foreach ($quantities as $color => $sizes) {
        foreach ($sizes as $size => $qty) {
            $quantite_stock += intval($qty);
        }
    }

    // Insert lot
    $stmt = $pdo->prepare("INSERT INTO lots (categorie, quantite_stock, etat, fournisseur_id, emplacement) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$categorie, $quantite_stock, $etat, $fournisseur_id, $emplacement]);
    $lot_id = $pdo->lastInsertId();

    // Link article/color/size/quantity to lot (create a new table if needed)
    foreach ($quantities as $color => $sizes) {
        foreach ($sizes as $size => $qty) {
            if (intval($qty) > 0) {
                $stmt2 = $pdo->prepare("INSERT INTO article_lot (article_id, lot_id, couleur, taille, quantite) VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([$article_id, $lot_id, $color, $size, intval($qty)]);
            }
        }
    }

    $_SESSION['lot_basket'] = [];
    $_SESSION['flash_message'] = "Lot créé avec succès !";
    header('Location: lots.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lots</title>
    <link rel="stylesheet" href="../../public/style.css">
    <!--<link rel="stylesheet" href="../../public/form.css">-->
    <link rel="stylesheet" href="../../public/stock.css">
    <link rel="stylesheet" href="../../public/modaledit.css">
    <link rel="stylesheet" href="../../public/livraisons.css">
    <link rel="stylesheet" href="../../public/notifications.css">
    <link rel="icon" href="../../img/logo_w.png" type="image/png">
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-message
        <?= isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'success' ? 'flash-success' : '' ?>
        <?= isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'error' ? 'flash-error' : '' ?>"
        id="flash-message">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
<?php endif; ?>
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
                <!-- Dropdown -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle active">
                            <span class="material-symbols-rounded">inventory_2</span>
                            <span class="nav-label">Stock</span>
                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                        </a>
                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Stock</a>
                            </li>
                            <li class="nav-item">
                                <a href="lots.php" class="nav-link dropdown-link active">Lots</a>
                            </li>
                            <li class="nav-item">
                                <a href="articles.php" class="nav-link dropdown-link">Articles</a>
                            </li>
                        </ul>
                    </li>
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <!-- Dropdown -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">shopping_cart</span>
                            <span class="nav-label">Réapprovisionnement</span>
                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                        </a>
                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Réapprovisionnement</a>
                            </li>
                            <li class="nav-item">
                                <a href="reapprovisionnement.php" class="nav-link dropdown-link">Réapprovisionnement</a>
                            </li>
                            <li class="nav-item">
                                <a href="commandes.php" class="nav-link dropdown-link">Commandes</a>
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
            <h1>LOTS</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-article-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <button class="btn" id="open-basket-modal">Voir mon panier</button>
                <button class="btn btn-add" id="add-articles-btn">Ajouter un lot</button>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="nom_article">Nom</option>
                        <option value="reference">Référence</option>
                        <option value="categorie">Catégorie</option>
                        <option value="etat">État</option>
                        <option value="couleur">Couleur</option>
                        <option value="taille">Taille</option>
                        <option value="quantite_stock">Quantité en stock</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="form-section" id="add-articles-form-section" style="display:none;">
            <form action="../../actions/ajouter_articles.php" method="POST">
                <select name="categorie" required>
                    <option value="Top">Top</option>
                    <option value="Bas">Bas</option>
                    <option value="Dessus">Dessus</option>
                    <option value="Ensemble">Ensemble</option>
                </select>
                <input name="quantite_stock" type="number" placeholder="Quantité en stock" required>
                <select name="etat" required>
                    <option value="vert">Vert</option>
                    <option value="orange">Orange</option>
                    <option value="rouge">Rouge</option>
                </select>
                <select name="fournisseur_id" required>
                    <?php
                    $fournisseurs = $pdo->query("SELECT id, nom FROM fournisseurs")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fournisseurs as $f) {
                        echo '<option value="'.$f['id'].'">'.htmlspecialchars($f['nom']).'</option>';
                    }
                    ?>
                </select>
                <input name="emplacement" placeholder="Emplacement">
                <label>Articles du lot :</label>
                <select name="articles[]" multiple required style="min-width:200px;">
                    <?php
                    $articlesList = $pdo->query("SELECT id, nom_article FROM articles")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($articlesList as $a) {
                        echo '<option value="'.$a['id'].'">'.htmlspecialchars($a['nom_article']).'</option>';
                    }
                    ?>
                </select>
                <button type="submit">Ajouter un lot</button>
            </form>
        </div>

        <!-- Basket Modal -->
        <div id="basket-modal" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <span class="close" id="close-basket-modal" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</span>
                <h2>Mon panier d'articles</h2>
                <?php if (!empty($_SESSION['lot_basket'])): ?>
                    <?php
                    $article_id = $_SESSION['lot_basket'][0];
                    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
                    $stmt->execute([$article_id]);
                    $article = $stmt->fetch();

                    // Define available colors and sizes (or fetch from DB if dynamic)
                    $colors = ['Rouge','Bleu','Jaune','Vert','Orange','Violet','Marron','Beige','Gris','Noir','Blanc','Rose'];
                    $sizes = ['XS','S','M','L','XL','30','32','34','36','38','40','42','44','46','48'];
                    ?>
                    <h2><?= htmlspecialchars($article['nom_article']) ?> (<?= htmlspecialchars($article['reference']) ?>)</h2>
                    <form method="post">
                        <table>
                            <thead>
                                <tr>
                                    <th>Couleur</th>
                                    <?php foreach ($sizes as $size): ?>
                                        <th><?= $size ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($colors as $color): ?>
                                <tr>
                                    <td><?= $color ?></td>
                                    <?php foreach ($sizes as $size): ?>
                                        <td>
                                            <input type="number" min="0" name="quantities[<?= $color ?>][<?= $size ?>]" style="width:40px;">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Lot info fields -->
                        <select name="categorie" required>
                            <option value="Top">Top</option>
                            <option value="Bas">Bas</option>
                            <option value="Dessus">Dessus</option>
                            <option value="Ensemble">Ensemble</option>
                        </select>
                        <select name="etat" required>
                            <option value="vert">Vert</option>
                            <option value="orange">Orange</option>
                            <option value="rouge">Rouge</option>
                        </select>
                        <select name="fournisseur_id" required>
                            <?php
                            $fournisseurs = $pdo->query("SELECT id, nom FROM fournisseurs")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($fournisseurs as $f) {
                                echo '<option value="'.$f['id'].'">'.htmlspecialchars($f['nom']).'</option>';
                            }
                            ?>
                        </select>
                        <input name="emplacement" placeholder="Emplacement">
                        <button type="submit" name="create_lot" class="btn">Créer le lot</button>
                    </form>
                <?php else: ?>
                    <p>Votre panier est vide.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Article List for Basket -->
        <h2>Articles disponibles</h2>
        <table id="articles-table" border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Référence</th>
                    <th>Catégorie</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= htmlspecialchars($article['nom_article']) ?></td>
                    <td><?= htmlspecialchars($article['reference']) ?></td>
                    <td><?= htmlspecialchars($article['categorie']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <button type="submit" name="add_to_basket" class="btn"
                                <?php
                                $basket_reference = null;
                                if (!empty($_SESSION['lot_basket'])) {
                                    $stmt = $pdo->prepare("SELECT reference FROM articles WHERE id = ?");
                                    $stmt->execute([$_SESSION['lot_basket'][0]]);
                                    $basket_reference = $stmt->fetchColumn();
                                }
                                $disable = false;
                                if (in_array($article['id'], $_SESSION['lot_basket'])) $disable = true;
                                if ($basket_reference && $article['reference'] !== $basket_reference) $disable = true;
                                echo $disable ? 'disabled' : '';
                                ?>>
                                <?= in_array($article['id'], $_SESSION['lot_basket']) ? 'Ajouté' : 'Ajouter au panier' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table id="lots-table" border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Lot</th>
                    <th>Catégorie</th>
                    <th>Quantité en stock</th>
                    <th>Etat</th>
                    <th>Fournisseur</th>
                    <th>Emplacement</th>
                    <th>Articles du lot</th>
                    <th>Actions</th>
                </tr>
            </thead>

                <tbody>
                    <?php foreach ($lots as $lot): ?>
                        <?php
                        $lotId = $lot['id'];

                        // On va chercher les articles liés à CE lot
                        $stmt = $pdo->prepare("
                            SELECT 
                                a.nom_article, 
                                al.couleur, 
                                al.taille, 
                                al.quantite
                            FROM article_lot al
                            JOIN articles a ON al.article_id = a.id
                            WHERE al.lot_id = ?
                        ");
                        $stmt->execute([$lotId]);
                        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $dataArticles = htmlspecialchars(json_encode($articles), ENT_QUOTES);
                        ?>
                    <tr data-etat="<?= htmlspecialchars($lot['etat']) ?>">
                        <td>Lot n°<?= $lot['id'] ?></td>
                        <td><?= htmlspecialchars($lot['categorie']) ?></td>
                        <td><?= $lot['quantite_stock'] ?></td>
                        <td>
                            <span class="etat-square <?= htmlspecialchars($lot['etat']) ?>"></span>
                            <?= ucfirst($lot['etat']) ?>
                        </td>
                        <td>
                            <?php
                            // Fetch fournisseur name
                            $fournisseurNom = '';
                            if ($lot['fournisseur_id']) {
                                $stmtF = $pdo->prepare("SELECT nom FROM fournisseurs WHERE id = ?");
                                $stmtF->execute([$lot['fournisseur_id']]);
                                $fournisseurNom = $stmtF->fetchColumn();
                            }
                            echo htmlspecialchars($fournisseurNom);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($lot['emplacement']) ?></td>
                        <td><?= htmlspecialchars($lot['articles']) ?></td>
                        <td class="actions">
                            <!-- Add your action buttons here, e.g. Voir, Modifier, Supprimer -->
                            <button class="btn btn-voir"
                                data-etat="<?= htmlspecialchars($lot['etat']) ?>"
                                data-id="<?= htmlspecialchars($lot['id']) ?>"
                                data-fournisseur_nom="<?= htmlspecialchars($lot['fournisseur_nom']) ?>"
                                data-categorie="<?= htmlspecialchars($lot['categorie']) ?>"
                                data-quantite_stock="<?= $lot['quantite_stock'] ?>"
                                data-emplacement="<?= htmlspecialchars($lot['emplacement']) ?>"
                                data-articles='<?= $dataArticles ?>'
                                data-created_at="<?= htmlspecialchars($article['created_at'] ?? '-') ?>"
                            >Voir</button>
                            <button
                                class="btn btn-edit"
                                data-categorie="<?= htmlspecialchars($lot['categorie']) ?>"
                                data-quantite_stock="<?= $lot['quantite_stock'] ?>"
                                data-etat="<?= $lot['etat'] ?>"
                                data-fournisseur_nom="<?= htmlspecialchars($lot['fournisseur_nom']) ?>"
                                data-emplacement="<?= htmlspecialchars($lot['emplacement']) ?>"
                            >Modifier</button>
                            <button class="btn btn-delete" style="padding: 5px 10px;">Supprimer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>

        <!-- Modal détails -->
        <div id="modal-details" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <button onclick="fermerModal()" class="close" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</button>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Détails du lot</h2>
                <table id="details-content" style="width:100%; background:#f9f9f9; border-radius:8px; overflow:hidden;">
                    <!-- Details will be filled by JS -->
                </table>
            </div>
        </div>

        <div id="modal-edit" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <span class="close" onclick="fermerEditModal()" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</span>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Modifier le lot</h2>
                <form id="edit-lot-form" method="POST" action="../../actions/modifier_lot.php">
                    <input type="hidden" name="id" id="edit-id">
                    <div style="margin-bottom: 10px">
                        <label for="edit-categorie">Catégorie</label>
                        <select name="categorie" id="edit-categorie" required>
                            <option value="Top">Top</option>
                            <option value="Bas">Bas</option>
                            <option value="Dessus">Dessus</option>
                            <option value="Ensemble">Ensemble</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 10px">
                        <label for="edit-quantite_stock">En stock</label>
                        <input name="quantite_stock" id="edit-quantite_stock" required>
                    </div>
                    <div style="margin-bottom: 10px">
                        <label for="edit-etat">État</label>
                        <select name="etat" id="edit-etat" required>
                            <option value="vert">Vert</option>
                            <option value="orange">Orange</option>
                            <option value="rouge">Rouge</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 10px">
                        <label for="edit-fournisseur_id">Fournisseur</label>
                        <select name="fournisseur_id" id="edit-fournisseur_id" required>
                            <?php
                            $fournisseurs = $pdo->query("SELECT id, nom FROM fournisseurs")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($fournisseurs as $f) {
                                echo '<option value="'.$f['id'].'">'.htmlspecialchars($f['nom']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 10px">
                        <label for="edit-emplacement">Emplacement</label>
                        <input name="emplacement" id="edit-emplacement" placeholder="Emplacement">
                    </div>
                    
                    <button type="submit">Enregistrer</button>
                </form>
            </div>
        </div>

        <div id="modal-supprimer" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerSupprimerModal()">&times;</span>
                <h2>Confirmer la suppression</h2>
                <p>Voulez-vous vraiment supprimer cet article ?</p>
                <button id="btn-confirm-supprimer" class="btn btn-confirm-delete">Oui, supprimer</button>
                <button type="button" class="btn" onclick="fermerSupprimerModal()">Annuler</button>
            </div>
        </div>

    </section>

    <script>

        document.getElementById('open-basket-modal').onclick = function() {
            document.getElementById('basket-modal').style.display = 'block';
        };
        document.getElementById('close-basket-modal').onclick = function() {
            document.getElementById('basket-modal').style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target == document.getElementById('basket-modal')) {
                document.getElementById('basket-modal').style.display = 'none';
            }
        };

        document.getElementById('add-articles-btn').addEventListener('click', function() {
            const formSection = document.getElementById('add-articles-form-section');
            formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
        });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('sort-select').addEventListener('change', function() {
            const sortType = this.value;
            const table = document.getElementById('articles-table');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Column indexes for your articles table
            const colIndexes = {
                nom_article: 0,
                reference: 1,
                categorie: 2,
                couleur: 3,
                taille: 4,
                quantite_stock: 5,
                lots: 6,
                etat: 7
            };

            function getCellValue(row, idx) {
                return row.cells[idx] ? row.cells[idx].textContent.trim() : '';
            }

            rows.sort((a, b) => {
                let valA, valB;
                switch (sortType) {
                    case 'quantite_stock':
                        valA = parseInt(getCellValue(a, colIndexes[sortType]), 10) || 0;
                        valB = parseInt(getCellValue(b, colIndexes[sortType]), 10) || 0;
                        return valA - valB;
                    case 'etat':
                        valA = a.dataset.etat ? a.dataset.etat.toLowerCase() : '';
                        valB = b.dataset.etat ? b.dataset.etat.toLowerCase() : '';
                        return valA.localeCompare(valB);
                    case 'nom_article':
                    case 'reference':
                    case 'categorie':
                    case 'couleur':
                    case 'taille':
                        valA = getCellValue(a, colIndexes[sortType]).toLowerCase();
                        valB = getCellValue(b, colIndexes[sortType]).toLowerCase();
                        return valA.localeCompare(valB, undefined, {numeric: true});
                    default:
                        return 0;
                }
            });

            // Remove all rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }

            // Re-add sorted rows
            rows.forEach(row => {
                tbody.appendChild(row);
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500); // Remove after fade out
            }, 5000); // 5 seconds
        }
    });

    /* Search 'article' */
    document.getElementById('search-article-input').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#articles-table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
    });

    // Voir button logic for articles
    document.querySelectorAll('.btn-voir').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const row = btn.closest('tr');
            const etat = row.dataset.etat;
            const etatLabel = btn.dataset.etat.charAt(0).toUpperCase() + btn.dataset.etat.slice(1);
            const articles = JSON.parse(btn.dataset.articles);

            // Regroupement : Article > Couleur > Taille x Quantité
            const grouped = {};

            articles.forEach(({ nom_article, couleur, taille, quantite }) => {
                if (!grouped[nom_article]) grouped[nom_article] = {};
                if (!grouped[nom_article][couleur]) grouped[nom_article][couleur] = [];
                grouped[nom_article][couleur].push(`${taille} x${quantite}`);
            });

            // 🔧 Génération HTML structuré
            let articlesHtml = '<ul>';
            Object.entries(grouped).forEach(([article, couleurs]) => {
                articlesHtml += `<li><strong>${article}</strong><ul>`;
                Object.entries(couleurs).forEach(([couleur, tailles]) => {
                    articlesHtml += `<li><em>${couleur}</em><ul>`;
                    tailles.forEach(info => {
                        articlesHtml += `<li>${info}</li>`;
                    });
                    articlesHtml += '</ul></li>';
                });
                articlesHtml += '</ul></li>';
            });
            articlesHtml += '</ul>';

            const details = [
                ['Lot n°', btn.dataset.id],
                ['Catégorie', btn.dataset.categorie],
                ['Quantité en stock', btn.dataset.quantite_stock],
                ['Fournisseur', btn.dataset.fournisseur_nom],
                ['Emplacement', btn.dataset.emplacement],
                ['Articles du lot', articlesHtml],
                [
                    'État',
                    `<span class="etat-square ${etat}"></span> ${etatLabel}`
                ],
                ['Date de création', btn.dataset.created_at],
            ];
            let html = '<tbody>';
            details.forEach(([label, value]) => {
                html += `<tr><th>${label}</th><td>${value}</td></tr>`;
            });
            html += '</tbody>';
            document.getElementById('details-content').innerHTML = html;
            document.getElementById('modal-details').style.display = 'flex';
        });
    });

    function fermerModal() {
        document.getElementById('modal-details').style.display = 'none';
    }

    // Modal edit for lots
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('modal-edit').style.display = 'block';
            document.getElementById('edit-categorie').value = this.dataset.categorie;
            document.getElementById('edit-quantite_stock').value = this.dataset.quantite_stock;
            document.getElementById('edit-etat').value = this.dataset.etat;
            document.getElementById('edit-fournisseur_nom').value = this.dataset.fournisseur_nom;
            document.getElementById('edit-emplacement').value = this.dataset.emplacement;
        });
    });

    // Modal delete
    let articleToDelete = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Only attach to table delete buttons
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                articleToDelete = this.dataset.id;
                document.getElementById('modal-supprimer').style.display = 'block';
            });
        });

        // Attach event to the confirm button in the modal
        const confirmBtn = document.getElementById('btn-confirm-supprimer');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (!erticleToDelete) return;
                fetch('../../actions/supprimer_article.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(articleToDelete)
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression.');
                    }
                });
            });
        }
    });

    function fermerSupprimerModal() {
        document.getElementById('modal-supprimer').style.display = 'none';
    }

    function fermerEditModal() {
        document.getElementById('modal-edit').style.display = 'none';
    }

    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>