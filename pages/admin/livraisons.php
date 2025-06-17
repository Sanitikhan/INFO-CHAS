<?php
session_start();
require_once '../../includes/config.php';

$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer les livraisons avec jointures
$sql = "SELECT l.*, f.nom as fournisseur_nom, u.username as createur_username,
        COUNT(ld.id) as nb_lots,
        SUM(CASE WHEN ld.quantite_recue > 0 THEN 1 ELSE 0 END) as lots_recus
        FROM livraisons l
        LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
        LEFT JOIN users u ON l.created_by = u.id
        LEFT JOIN livraisons_details ld ON l.id = ld.livraison_id
        GROUP BY l.id
        ORDER BY l.date_prevue DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$livraisons = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT lots.*, fournisseurs.nom AS fournisseur_nom
    FROM lots
    LEFT JOIN fournisseurs ON lots.fournisseur_id = fournisseurs.id
");
$lots = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraisons</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/form.css">
    <link rel="stylesheet" href="../../public/livraisons.css">
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
                    <a href="reapprovisionnement.php" class="nav-link">
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
                            <a href="livraisons.php" class="nav-link dropdown-link">Toutes les livraisons</a>
                        </li>
                        <li class="nav-item">
                            <a href="meslivraisons.php" class="nav-link dropdown-link">Mes livraisons</a>
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
            <input type="text" id="search-lot-input" placeholder="Rechercher une livraisons.." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <button class="btn btn-add" id="add-livraisons-btn">Ajouter une livraison</button>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="default">Sélectionner un critère</option>
                        <option value="numero">Numéro</option>
                        <option value="fournisseur">Fournisseur</option>
                        <option value="date_prevue">Date prévue</option>
                        <option value="statut">Statut</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="form-section" id="add-livraisons-form-section" style="display:none;">
            <form action="../../actions/ajouter_livraisons.php" method="POST">
                <input name="numero_livraison" placeholder="Numéro de livraison" required>
                <input name="fournisseur_id" placeholder="Fournisseur" required>
                <input name="date_prevue" type="date" placeholder="Date prévue" required>
                <select name="statut" required>
                    <option value="en_attente">En attente</option>
                    <option value="en_cours">En cours</option>
                    <option value="livree">Livrée</option>
                    <option value="probleme">Problème</option>
                </select>
                <input type="text" name="transporteur" placeholder="Transporteur" required>
                <input type="texte" name="notes" placeholder="Notes">
                <button type="submit">Ajouter la livraison</button>
            </form>
        </div>

        <!-- Affichage des livraisons existantes -->
        <h2>Livraisons</h2>
        <table id="livraisons-table" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>N° Livraison</th>
                    <th>Fournisseur</th>
                    <th>Date prévue</th>
                    <th>Statut</th>
                    <th>Progression</th>
                    <th>Actions</th>
                </tr>
            </thead>

                <tbody>
                    <?php foreach ($livraisons as $livraison): ?>
                        <tr class="main-row" data-statut="<?= $livraison['statut'] ?>" 
                            data-fournisseur="<?= strtolower($livraison['fournisseur_nom']) ?>"
                            data-date="<?= $livraison['date_prevue'] ?>">
                            
                            <td class="numero-livraison">
                                <strong><?= htmlspecialchars($livraison['numero_livraison']) ?></strong>
                            </td>
                            
                            <td><?= isset($livraison['fournisseur_nom']) && $livraison['fournisseur_nom'] !== null ? htmlspecialchars($livraison['fournisseur_nom']) : '-' ?></td>
                            
                            <td>
                                <?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>
                                <?php if ($livraison['date_livraison']): ?>
                                    <br><small>Livrée le <?= date('d/m/Y H:i', strtotime($livraison['date_livraison'])) ?></small>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <span class="statut-badge statut-<?= $livraison['statut'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $livraison['statut'])) ?>
                                </span>
                            </td>
                            
                            <td>
                                <div class="progress-bar">
                                    <?php 
                                    $progress = $livraison['nb_lots'] > 0 ? 
                                        ($livraison['lots_recus'] / $livraison['nb_lots']) * 100 : 0;
                                    ?>
                                    <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                    <span class="progress-text">
                                        <?= $livraison['lots_recus'] ?>/<?= $livraison['nb_lots'] ?> lots
                                    </span>
                                </div>
                            </td>
                            
                            <td class="actions">
                                <button class="btn btn-sm btn-info" 
                                        onclick="voirDetails(<?= $livraison['id'] ?>)">
                                    Voir
                                </button>
                                
                                <?php if ($livraison['statut'] !== 'livree' && 
                                         ($_SESSION['users_role'] === 'magasinier' || $_SESSION['users_role'] === 'admin')): ?>
                                    <button class="btn btn-sm btn-success" 
                                            onclick="window.location.href='recevoir.php?id=<?= $livraison['id'] ?>'">
                                        Recevoir
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($_SESSION['users_role'] === 'admin'): ?>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="supprimerLivraison(<?= $livraison['id'] ?>)">
                                        Suppr.
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>

        <!-- Modal détails -->
        <div id="modal-details" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="fermerModal()">&times;</span>
                <div id="details-content">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>

    </section>

    <script>
        document.getElementById('add-livraisons-btn').addEventListener('click', function() {
            const formSection = document.getElementById('add-livraisons-form-section');
            formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('sort-select').addEventListener('change', function() {
        const sortType = this.value;
        const table = document.getElementById('livraisons-table');
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('.main-row'));

        // Column indexes: adjust if your table structure is different
        const colIndexes = {
            numero: 0,
            fournisseur: 1,
            date_prevue: 2,
            statut: 3
        };

        function getCellValue(row, idx) {
            return row.cells[idx] ? row.cells[idx].textContent.trim() : '';
        }

        rows.sort((a, b) => {
            let valA, valB;
            switch (sortType) {
                case 'numero':
                    valA = getCellValue(a, colIndexes.numero);
                    valB = getCellValue(b, colIndexes.numero);
                    // If numero is numeric, sort as number
                    if (!isNaN(valA) && !isNaN(valB)) {
                        return parseInt(valA, 10) - parseInt(valB, 10);
                    }
                    return valA.localeCompare(valB, undefined, {numeric: true});
                case 'date_prevue':
                    function parseFrDate(str) {
                        const [d, m, y] = str.split('/');
                        return new Date(`${y}-${m}-${d}`);
                    }
                    valA = parseFrDate(getCellValue(a, colIndexes.date_prevue));
                    valB = parseFrDate(getCellValue(b, colIndexes.date_prevue));
                    return valA - valB;
                case 'fournisseur':
                    valA = getCellValue(a, colIndexes.fournisseur).toLowerCase();
                    valB = getCellValue(b, colIndexes.fournisseur).toLowerCase();
                    return valA.localeCompare(valB, undefined, {numeric: true});
                case 'statut':
                    valA = getCellValue(a, colIndexes.statut).toLowerCase();
                    valB = getCellValue(b, colIndexes.statut).toLowerCase();
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
    </script>
    <script src="../../actions/search.js"></script>
    <script src="../../actions/livraisons.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>