<?php
session_start();
require_once '../../includes/config.php';

try {
    $stmt = $pdo->query("SELECT * FROM commandes");
    $commandes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $commandes = [];
}

// Fetch all users with role 'livreur'
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'livreur'");
$stmt->execute();
$livreurs = $stmt->fetchAll();

$detailsHtml = '';
if (isset($_GET['details'])) {
    $commande_id = intval($_GET['details']);

    // Fetch commande info
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch();

    // Fetch lots for this commande
    $stmt = $pdo->prepare("
        SELECT cl.*, l.reference, l.type
        FROM commande_lots cl
        LEFT JOIN lots l ON cl.lot_id = l.id
        WHERE cl.commande_id = ?
    ");
    $stmt->execute([$commande_id]);
    $lots = $stmt->fetchAll();

    ob_start();
    if ($commande) {
        ?>
        <h2>Commande #<?= htmlspecialchars($commande['reference']) ?></h2>
        <p><strong>Préparateur :</strong> <?= htmlspecialchars($commande['preparateur']) ?></p>
        <p><strong>Livreur :</strong> <?= htmlspecialchars($commande['livreur']) ?></p>
        <p><strong>Date commande :</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p><strong>Date livraison :</strong> <?= htmlspecialchars($commande['date_livraison']) ?></p>
        <p><strong>État :</strong>
            <span class="statut-badge statut-<?= htmlspecialchars($commande['etat']) ?>">
                <?= ucfirst(str_replace('_', ' ', $commande['etat'])) ?>
            </span>
        </p>
        <h3>Lots commandés</h3>
        <table>
            <thead>
                <tr>
                    <th>Référence lot</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>État</th>
                    <th>Lieu stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lots as $lot): ?>
                <tr>
                    <td><?= htmlspecialchars($lot['reference']) ?></td>
                    <td><?= htmlspecialchars($lot['type']) ?></td>
                    <td><?= htmlspecialchars($lot['quantite']) ?></td>
                    <td><?= htmlspecialchars($lot['etat']) ?></td>
                    <td><?= htmlspecialchars($lot['lieu_stock']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "<p>Aucune information trouvée pour cette commande.</p>";
    }
    $detailsHtml = ob_get_clean();
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
    <link rel="stylesheet" href="../../public/stock.css">
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
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <!-- Dropdown -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link active dropdown-toggle">
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
                                <a href="commandes.php" class="nav-link active dropdown-link">Commandes</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
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
                                <a class="nav-link active dropdown-title">Livraisons</a>
                            </li>
                            <li class="nav-item">
                                <a href="livraisons.php" class="nav-link active dropdown-link">Toutes les livraisons</a>
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
            <h1>COMMANDES</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-commande-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <button class="btn btn-add" id="add-commande-btn">Ajouter une commande</button>
                <?php endif; ?>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="reference">Référence</option>
                        <option value="date_commande">Date de commande</option>
                        <option value="date_livraison">Date de livraison</option>
                        <option value="preparateur">Préparateur</option>
                        <option value="etat">État</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="form-section" id="add-commande-form-section" style="display:none; color: #fff;">
            <form action="../../actions/ajouter_commande.php" method="POST">
                <input type="text" name="reference" placeholder="Référence" required>
                <input type="text" name="preparateur" placeholder="Préparateur">
                <select name="livreur" required>
                    <option value="">Sélectionner un livreur</option>
                    <?php foreach ($livreurs as $livreur): ?>
                        <option value="<?= htmlspecialchars($livreur['username']) ?>">
                            <?= htmlspecialchars($livreur['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="date_commande">Date de commande :</label>
                <input type="date" name="date_commande" placeholder="Date commande">
                <label for="date_livraison">Date de livraison :</label>
                <input type="date" name="date_livraison" placeholder="Date livraison">
                <select name="etat" required>
                    <option value="">Sélectionner un état</option>
                    <option value="en-attente">En attente</option>
                    <option value="en cours">En cours</option>
                    <option value="livree">Livrée</option>
                    <option value="probleme">Problème</option>
                </select>
                <button type="submit">Ajouter la commande</button>
            </form>
        </div>

        <!-- Affichage des commandes existants -->
        <table id="commandes-table" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Préparateur</th>
                    <th>Livreur</th>
                    <th>Date commande</th>
                    <th>Date livraison</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['reference']) ?></td>
                        <td><?= htmlspecialchars($commande['preparateur']) ?></td>
                        <td><?= htmlspecialchars($commande['livreur']) ?></td>
                        <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                        <td><?= htmlspecialchars($commande['date_livraison']) ?></td>
                        <td>
                                <span class="statut-badge statut-<?= $commande['etat'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $commande['etat'])) ?>
                                </span>
                            </td>
                        <td>
                            <a href="?details=<?= $commande['id'] ?>" class="btn btn-view">Voir</a>
                            <button class="btn btn-edit"
                                data-id="<?= $commande['id'] ?>"
                                data-reference="<?= htmlspecialchars($commande['reference']) ?>"
                                data-preparateur="<?= htmlspecialchars($commande['preparateur']) ?>"
                                data-livreur="<?= htmlspecialchars($commande['livreur']) ?>"
                                data-date_commande="<?= htmlspecialchars($commande['date_commande']) ?>"
                                data-date_livraison="<?= htmlspecialchars($commande['date_livraison']) ?>"
                                data-etat="<?= htmlspecialchars($commande['etat']) ?>"
                            >Modifier</button>
                            <button class="btn btn-delete" data-id="<?= $commande['id'] ?>">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="modal-details" class="modal" style="display: <?= !empty($detailsHtml) ? 'block' : 'none' ?>;">
            <div class="modal-content">
                <span class="close" onclick="fermerModal()">&times;</span>
                <div id="details-content">
                    <?= $detailsHtml ?>
                </div>
            </div>
        </div>

        <!-- Modal Modifier Commande -->
        <div id="modal-edit-commande" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerEditCommandeModal()">&times;</span>
                <h2>Modifier la commande</h2>
                <form id="edit-commande-form" method="POST" action="../../actions/modifier_commande.php" style="color: #fff;">
                    <input type="hidden" name="id" id="edit-commande-id">
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-reference">Référence</label>
                        <input name="reference" id="edit-commande-reference" required>
                    </div>
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-preparateur">Préparateur</label>
                        <input name="preparateur" id="edit-commande-preparateur">
                    </div>
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-livreur">Livreur</label>
                        <select name="livreur" id="edit-commande-livreur" required>
                            <option value="">Sélectionner un livreur</option>
                            <?php foreach ($livreurs as $livreur): ?>
                                <option value="<?= htmlspecialchars($livreur['username']) ?>">
                                    <?= htmlspecialchars($livreur['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-date-commande">Date commande</label>
                        <input type="date" name="date_commande" id="edit-commande-date-commande">
                    </div>
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-date-livraison">Date livraison</label>
                        <input type="date" name="date_livraison" id="edit-commande-date-livraison">
                    </div>
                    <div style="margin-bottom: 1em;">
                        <label for="edit-commande-etat">État</label>
                        <select name="etat" id="edit-commande-etat" required>
                            <option value="en-attente">En attente</option>
                            <option value="en cours">En cours</option>
                            <option value="livree">Livrée</option>
                            <option value="probleme">Problème</option>
                        </select>
                    </div>
                    <button type="submit">Enregistrer</button>
                </form>
            </div>
        </div>

        

    </section>

    <script>
    document.querySelectorAll('#commandes-table .main-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('details-row')) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            }
        });
    });

    document.getElementById('add-commande-btn').addEventListener('click', function() {
        const formSection = document.getElementById('add-commande-form-section');
        formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
    });

    document.querySelectorAll('#commandes-table .main-row').forEach(function(row) {
        row.addEventListener('click', function() {
            // Fill the modal with the commande's data
            document.getElementById('edit-commande-id').value = row.dataset.id;
            document.getElementById('edit-commande-reference').value = row.dataset.reference;
            document.getElementById('edit-commande-type').value = row.dataset.type;
            document.getElementById('edit-commande-quantite').value = row.dataset.quantite_total;
            document.getElementById('edit-commande-disponibilite').value = row.dataset.disponibilite;
            document.getElementById('edit-commande-reserve').value = row.dataset.reserve;
            document.getElementById('edit-commande-a_venir').value = row.dataset.a_venir;
            document.getElementById('edit-commande-etat').value = row.dataset.etat;
            document.getElementById('edit-commande-fournisseur_id').value = row.dataset.fournisseur_id;
            document.getElementById('edit-commande-emplacement').value = row.dataset.emplacement;

            document.getElementById('edit-commande-modal').style.display = 'flex';
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // SORT COMMANDES TABLE
        const sortSelect = document.getElementById('sort-select');
        const table = document.getElementById('commandes-table');
        const tbody = table.querySelector('tbody');

        sortSelect.addEventListener('change', function() {
            const sortType = this.value;
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                let valA, valB;
                switch (sortType) {
                    case 'reference':
                        valA = a.children[1].textContent.trim().toLowerCase();
                        valB = b.children[1].textContent.trim().toLowerCase();
                        return valA.localeCompare(valB, undefined, {numeric: true});
                    case 'date_commande':
                        valA = a.children[4].textContent.trim();
                        valB = b.children[4].textContent.trim();
                        return valA.localeCompare(valB);
                    case 'date_livraison':
                        valA = a.children[5].textContent.trim();
                        valB = b.children[5].textContent.trim();
                        return valA.localeCompare(valB);
                    case 'preparateur':
                        valA = a.children[2].textContent.trim().toLowerCase();
                        valB = b.children[2].textContent.trim().toLowerCase();
                        return valA.localeCompare(valB);
                    case 'etat':
                        valA = a.children[6].textContent.trim().toLowerCase();
                        valB = b.children[6].textContent.trim().toLowerCase();
                        return valA.localeCompare(valB);
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

    function fermerModal() {
        document.getElementById('modal-details').style.display = 'none';
        // Remove ?details=... from URL without reloading
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('details');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    }

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('modal-edit-commande').style.display = 'block';
        document.getElementById('edit-commande-id').value = this.dataset.id;
        document.getElementById('edit-commande-reference').value = this.dataset.reference;
        document.getElementById('edit-commande-preparateur').value = this.dataset.preparateur;
        document.getElementById('edit-commande-livreur').value = this.dataset.livreur;
        document.getElementById('edit-commande-date-commande').value = this.dataset.date_commande;
        document.getElementById('edit-commande-date-livraison').value = this.dataset.date_livraison;
        document.getElementById('edit-commande-etat').value = this.dataset.etat;
    });
});

function fermerEditCommandeModal() {
    document.getElementById('modal-edit-commande').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-commande-input');
    const table = document.getElementById('commandes-table');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        rows.forEach(row => {
            // Combine all cell text in the row
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(filter) ? '' : 'none';
        });
    });
});

    </script>

    <script src="../../actions/search.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>