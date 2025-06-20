<?php
session_start();
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
    <link rel="stylesheet" href="../../public/stock.css">
    <link rel="stylesheet" href="../../public/modal2.css">
    <link rel="stylesheet" href="../../public/livraisons.css">
    <link rel="stylesheet" href="../../public/notifications.css">
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
            <h1>STOCK</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-lot-input" placeholder="Rechercher un lot..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <button class="btn btn-add" id="add-lot-btn">Ajouter un lot</button>
                <?php endif; ?>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="name">Référence</option>
                        <option value="quantity">Quantité</option>
                        <option value="etat">État</option>
                        <option value="fournisseur">Fournisseur</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="form-section" id="add-lot-form-section" style="display:none;">
            <form action="../../actions/ajouter_lot.php" method="POST">
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
        <table id="lots-table" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Disponibilité</th>
                    <th>Etat</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($lots as $lot): ?>
                    <tr class="main-row" style="cursor:pointer;"
                        data-id="<?= htmlspecialchars($lot['id']) ?>"
                        data-reference="<?= htmlspecialchars($lot['reference']) ?>"
                        data-type="<?= htmlspecialchars($lot['type']) ?>"
                        data-quantite_total="<?= htmlspecialchars($lot['quantite_total']) ?>"
                        data-disponibilite="<?= htmlspecialchars($lot['disponibilite']) ?>"
                        data-reserve="<?= htmlspecialchars($lot['reserve']) ?>"
                        data-a_venir="<?= htmlspecialchars($lot['a_venir']) ?>"
                        data-etat="<?= htmlspecialchars($lot['etat']) ?>"
                        data-fournisseur_id="<?= htmlspecialchars($lot['fournisseur_id']) ?>"
                        data-emplacement="<?= isset($lot['emplacement']) ? htmlspecialchars($lot['emplacement']) : '' ?>"
                    >
                        <td><?= htmlspecialchars($lot['reference']) ?></td>
                        <td><?= htmlspecialchars($lot['type']) ?></td>
                        <td><?= htmlspecialchars($lot['quantite_total']) ?></td>
                        <td><?= htmlspecialchars($lot['disponibilite']) ?></td>
                        <td>
                            <span class="etat-square <?= htmlspecialchars($lot['etat']) ?>"></span>
                        </td>
                        <td>
                            <!-- Voir button -->
                            <button class="btn btn-voir" data-id="<?= htmlspecialchars($lot['id']) ?>">Voir</button>
                            <!-- Modifier button (only for admin or gestionnaire de stock) -->
                            <?php if (
                                (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                                (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                            ): ?>
                                <button class="btn btn-modifier" data-id="<?= htmlspecialchars($lot['id']) ?>"
                                    data-reference="<?= htmlspecialchars($lot['reference']) ?>"
                                    data-type="<?= htmlspecialchars($lot['type']) ?>"
                                    data-quantite_total="<?= htmlspecialchars($lot['quantite_total']) ?>"
                                    data-disponibilite="<?= htmlspecialchars($lot['disponibilite']) ?>"
                                    data-reserve="<?= htmlspecialchars($lot['reserve']) ?>"
                                    data-a_venir="<?= htmlspecialchars($lot['a_venir']) ?>"
                                    data-etat="<?= htmlspecialchars($lot['etat']) ?>"
                                    data-fournisseur_id="<?= htmlspecialchars($lot['fournisseur_id']) ?>"
                                    data-emplacement="<?= isset($lot['emplacement']) ? htmlspecialchars($lot['emplacement']) : '' ?>"
                                >Modifier</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="edit-lot-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); align-items:center; justify-content:center; z-index:1000;">
            <div style="background:#fff; padding:2em; border-radius:10px; min-width:300px; position:relative;">
                <button onclick="document.getElementById('edit-lot-modal').style.display='none'" style="position:absolute;top:10px;right:10px;">&times;</button>
                <h3>Modifier le lot</h3>
                <form id="edit-lot-form" method="post" action="../../actions/modifier_lot.php">
                    <input type="hidden" name="id" id="edit-lot-id">
                    <div>
                        <label>Référence :</label>
                        <input type="text" name="reference" id="edit-lot-reference" required>
                    </div>
                    <div>
                        <label>Type :</label>
                        <input type="text" name="type" id="edit-lot-type" required>
                    </div>
                    <div>
                        <label>Quantité totale :</label>
                        <input type="number" name="quantite_total" id="edit-lot-quantite" required>
                    </div>
                    <div>
                        <label>Disponibilité :</label>
                        <input type="number" name="disponibilite" id="edit-lot-disponibilite" required>
                    </div>
                    <div>
                        <label>Réservé :</label>
                        <input type="number" name="reserve" id="edit-lot-reserve">
                    </div>
                    <div>
                        <label>À venir :</label>
                        <input type="number" name="a_venir" id="edit-lot-a_venir">
                    </div>
                    <div>
                        <label>Etat :</label>
                        <select name="etat" id="edit-lot-etat">
                            <option value="vert">Vert</option>
                            <option value="orange">Orange</option>
                            <option value="rouge">Rouge</option>
                        </select>
                    </div>
                    <div>
                        <label>Fournisseur :</label>
                        <input type="number" name="fournisseur_id" id="edit-lot-fournisseur_id">
                    </div>
                    <div>
                        <label>Emplacement :</label>
                        <input type="text" name="emplacement" id="edit-lot-emplacement">
                    </div>
                    <button type="submit" class="btn">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Modal Voir -->
        <div id="voir-lot-modal" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:400px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <button onclick="document.getElementById('voir-lot-modal').style.display='none'" class="close" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</button>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Détails du lot</h2>
                <table id="voir-lot-details" style="width:100%; background:#f9f9f9; border-radius:8px; overflow:hidden;">
                    <!-- Details will be filled by JS -->
                </table>
            </div>
        </div>

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
    
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sort-select');
        const table = document.getElementById('lots-table');
        const tbody = table.querySelector('tbody');

        // Map sort type to column index
        const colIndexes = {
            name: 0,        // Référence
            quantity: 2,    // Quantité
            etat: 4,        // État
            fournisseur: null // Not shown in main row, so we'll use data attribute
        };

        sortSelect.addEventListener('change', function() {
            const sortType = this.value;
            const rows = Array.from(tbody.querySelectorAll('.main-row'));

            rows.sort((a, b) => {
                let valA, valB;
                switch (sortType) {
                    case 'name':
                        valA = a.cells[colIndexes.name].textContent.trim().toLowerCase();
                        valB = b.cells[colIndexes.name].textContent.trim().toLowerCase();
                        return valA.localeCompare(valB, undefined, {numeric: true});
                    case 'quantity':
                        valA = parseInt(a.cells[colIndexes.quantity].textContent.trim(), 10) || 0;
                        valB = parseInt(b.cells[colIndexes.quantity].textContent.trim(), 10) || 0;
                        return valA - valB;
                    case 'etat':
                        valA = a.dataset.etat.toLowerCase();
                        valB = b.dataset.etat.toLowerCase();
                        return valA.localeCompare(valB);
                    case 'fournisseur':
                        valA = a.dataset.fournisseur_id;
                        valB = b.dataset.fournisseur_id;
                        return valA.localeCompare(valB, undefined, {numeric: true});
                    default:
                        return 0;
                }
            });

            // Remove all rows (and their details rows)
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }

            // Re-add sorted rows and their details rows
            rows.forEach(row => {
                const detailsRow = row.nextElementSibling && row.nextElementSibling.classList.contains('details-row')
                    ? row.nextElementSibling
                    : null;
                tbody.appendChild(row);
                if (detailsRow) {
                    tbody.appendChild(detailsRow);
                }
            });
        });
    });

    // Voir button logic
    document.querySelectorAll('.btn-voir').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = btn.closest('tr');
            const details = [
                ['Référence', row.children[0].textContent],
                ['Type', row.children[1].textContent],
                ['Quantité', row.children[2].textContent],
                ['Disponibilité', row.children[3].textContent],
                ['Etat', row.children[4].textContent],
            ];
            // If you want to show more details, add them here using data- attributes
            let html = '<tbody>';
            details.forEach(([label, value]) => {
                html += `<tr><th>${label}</th><td>${value}</td></tr>`;
            });
            html += '</tbody>';
            document.getElementById('voir-lot-details').innerHTML = html;
            document.getElementById('voir-lot-modal').style.display = 'flex';
        });
    });

    // Modifier button logic (reuse your existing code)
    document.querySelectorAll('.btn-modifier').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-lot-id').value = btn.dataset.id;
            document.getElementById('edit-lot-reference').value = btn.dataset.reference;
            document.getElementById('edit-lot-type').value = btn.dataset.type;
            document.getElementById('edit-lot-quantite').value = btn.dataset.quantite_total;
            document.getElementById('edit-lot-disponibilite').value = btn.dataset.disponibilite;
            document.getElementById('edit-lot-reserve').value = btn.dataset.reserve;
            document.getElementById('edit-lot-a_venir').value = btn.dataset.a_venir;
            document.getElementById('edit-lot-etat').value = btn.dataset.etat;
            document.getElementById('edit-lot-fournisseur_id').value = btn.dataset.fournisseur_id;
            document.getElementById('edit-lot-emplacement').value = btn.dataset.emplacement;
            document.getElementById('edit-lot-modal').style.display = 'flex';
        });
    });

    </script>

    <script src="../../actions/search.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>