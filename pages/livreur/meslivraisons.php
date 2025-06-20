<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}


// Fetch only the livraisons for this livreur
$stmt = $pdo->prepare("
    SELECT l.*, f.nom AS fournisseur_nom
    FROM livraisons l
    LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
    WHERE l.livreur_id = ?
    ORDER BY l.date_prevue DESC
");
$stmt->execute([$_SESSION['user_id']]);
$livraisons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes livraisons</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/form.css">
    <link rel="stylesheet" href="../../public/livraisons.css">
    <link rel="icon" href="../../img/logo_w.png" type="image/png">
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-message <?= isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'success' ? 'flash-success' : '' ?><?= isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'error' ? ' flash-error' : '' ?>" id="flash-message">
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
                    <a href="../admin/dashboard.php" class="nav-link">
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
                    <a href="../admin/stock.php" class="nav-link">
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
                                <a href="../admin/reapprovisionnement.php" class="nav-link dropdown-link">Réapprovisionnement</a>
                            </li>
                            <li class="nav-item">
                                <a href="../admin/commandes.php" class="nav-link dropdown-link">Commandes</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'livreur'): ?>
                    <!-- Dropdown for livreur -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link active dropdown-toggle">
                            <span class="material-symbols-rounded">local_shipping</span>
                            <span class="nav-label">Livraisons</span>
                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Livraisons</a>
                            </li>
                            <li class="nav-item">
                                <a href="../admin/livraisons.php" class="nav-link dropdown-link">Toutes les livraisons</a>
                            </li>
                            <li class="nav-item">
                                <a href="../livreur/meslivraisons.php" class="nav-link active dropdown-link">Mes livraisons</a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Simple link for others -->
                    <li class="nav-item">
                        <a href="../admin/livraisons.php" class="nav-link">
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
                            <a href="../admin/messages.php" class="nav-link dropdown-link">Mes messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="../admin/alertes.php" class="nav-link dropdown-link">Alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="../admin/fournisseurs.php" class="nav-link dropdown-link">Fournisseurs</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../admin/calendrier.php" class="nav-link">
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
                    <a href="../admin/parameters.php" class="nav-link">
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
            <h1>MES LIVRAISONS</h1>
        </header>

        <section class="btn-section">
            <input type="text" id="search-livraison-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="numero">Numéro</option>
                        <option value="fournisseur">Fournisseur</option>
                        <option value="date_prevue">Date prévue</option>
                        <option value="statut">Statut</option>
                    </select>
                </div>
            </div>
        </section>

        <table border="1">
            <thead>
                <tr>
                    <th>N° Livraison</th>
                    <th>Fournisseur</th>
                    <th>Date prévue</th>
                    <th>Statut</th>
                    <th>Actions</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livraisons as $livraison): ?>
                    <tr>
                        <td><?= htmlspecialchars($livraison['numero_livraison']) ?></td>
                        <td><?= htmlspecialchars($livraison['fournisseur_nom']) ?></td>
                        <td><?= htmlspecialchars($livraison['date_prevue']) ?></td>
                        <td>
                            <span class="statut-badge statut-<?= $livraison['statut'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $livraison['statut'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($livraison['statut'] !== 'livrée'): ?>
                                <button
                                    class="btn btn-success btn-marquer-livree"
                                    data-id="<?= $livraison['id'] ?>"
                                    style="padding: 5px 10px;">
                                    Marquer comme livrée
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($livraisons)): ?>
                    <tr><td colspan="4">Aucune livraison assignée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <script>
        document.querySelectorAll('.btn-marquer-livree').forEach(btn => {
            btn.addEventListener('click', function() {
                const livraisonId = this.dataset.id;
                if (!livraisonId) return;

                fetch('../../actions/marquer_livraison_livree.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'livraison_id=' + encodeURIComponent(livraisonId)
                })
                .then(response => response.text())
                .then(result => {
                    // Optionally, show a flash message or reload the page
                    window.location.reload();
                })
                .catch(() => alert('Erreur lors de la mise à jour.'));
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sort-select');
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');

            sortSelect.addEventListener('change', function() {
                const sortType = this.value;
                const rows = Array.from(tbody.querySelectorAll('tr'));

                // Column indexes: 0: Numéro, 1: Fournisseur, 2: Date prévue, 3: Statut
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
                            return valA.localeCompare(valB, undefined, {numeric: true});
                        case 'fournisseur':
                            valA = getCellValue(a, colIndexes.fournisseur).toLowerCase();
                            valB = getCellValue(b, colIndexes.fournisseur).toLowerCase();
                            return valA.localeCompare(valB, undefined, {numeric: true});
                        case 'date_prevue':
                            valA = getCellValue(a, colIndexes.date_prevue);
                            valB = getCellValue(b, colIndexes.date_prevue);
                            return valA.localeCompare(valB);
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

        document.getElementById('search-livraison-input').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });
    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>