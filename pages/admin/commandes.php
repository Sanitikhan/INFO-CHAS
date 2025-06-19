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
            <h1>COMMANDES</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-lot-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
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
                        <option value="name">Référence</option>
                        <option value="quantity">Quantité</option>
                        <option value="etat">État</option>
                        <option value="fournisseur">Fournisseur</option>
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
                    <th>ID</th>
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
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['reference']) ?></td>
                        <td><?= htmlspecialchars($commande['preparateur']) ?></td>
                        <td><?= htmlspecialchars($commande['livreur']) ?></td>
                        <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                        <td><?= htmlspecialchars($commande['date_livraison']) ?></td>
                        <td><?= htmlspecialchars($commande['etat']) ?></td>
                        <td>
                            <button class="btn btn-view" data-id="<?= $commande['id'] ?>">Voir</button>
                            <button class="btn btn-edit" data-id="<?= $commande['id'] ?>">Modifier</button>
                            <button class="btn btn-delete" data-id="<?= $commande['id'] ?>">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        

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
    </script>

    <script src="../../actions/search.js"></script>
    <script src="../../actions/script.js"></script>
</body>
</html>