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
        GROUP_CONCAT(DISTINCT CONCAT('Lot n°', l.id) SEPARATOR ', ') AS lots
    FROM articles a
    LEFT JOIN article_lot al ON a.id = al.article_id
    LEFT JOIN lots l ON al.lot_id = l.id
    GROUP BY a.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
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
                                <a href="lots.php" class="nav-link dropdown-link">Lots</a>
                            </li>
                            <li class="nav-item">
                                <a href="articles.php" class="nav-link dropdown-link active">Articles</a>
                            </li>
                            <li class="nav-item">
                                <a href="rangement.php" class="nav-link dropdown-link">Rangement</a>
                            </li>
                        </ul>
                    </li>
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <li class="nav-item">
                        <a href="commandes.php" class="nav-link">
                            <span class="material-symbols-rounded">shopping_cart</span>
                            <span class="nav-label">Commandes</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Commandes</a>
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
            <h1>ARTICLES</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-article-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <button class="btn btn-add" id="add-articles-btn">Ajouter un article</button>
                <div class="sort-dropdown" style="display:inline-block;">
                    <label for="sort-select" style="margin-right:8px;">Trier par :</label>
                    <select id="sort-select" style="padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="nom_article">Nom</option>
                        <option value="reference">Référence</option>
                        <option value="categorie">Catégorie</option>
                        <option value="etat">État</option>
                        <option value="quantite_stock">Quantité en stock</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="form-section" id="add-articles-form-section" style="display:none;">
            <form action="../../actions/ajouter_articles.php" method="POST">
                <input name="nom_article" placeholder="Nom de l'article" required>
                <input name="reference" placeholder="Référence" required>
                <select name="categorie" required>
                    <option value="Top">Top</option>
                    <option value="Bas">Bas</option>
                    <option value="Dessus">Dessus</option>
                    <option value="Ensemble">Ensemble</option>
                </select>
                <input name="quantite_stock" placeholder="Quantité en stock" required>
                <select name="etat" required>
                    <option value="vert">Vert</option>
                    <option value="orange">Orange</option>
                    <option value="rouge">Rouge</option>
                </select>
                <button type="submit">Ajouter un article</button>
            </form>
        </div>

        <table id="articles-table" border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Référence</th>
                    <th>Catégorie</th>
                    <th>Quantité en stock</th>
                    <th>Lots liés</th>
                    <th>Etat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr data-etat="<?= htmlspecialchars($article['etat']) ?>">
                    <td><?= htmlspecialchars($article['nom_article']) ?></td>
                    <td><?= htmlspecialchars($article['reference']) ?></td>
                    <td><?= htmlspecialchars($article['categorie']) ?></td>
                    <td><?= $article['quantite_stock'] ?></td>
                    <td><?= htmlspecialchars($article['lots']) ?></td>
                    <td>
                        <span class="etat-square <?= htmlspecialchars($article['etat']) ?>"></span>
                    </td>
                    <td class="actions">
                            
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <button class="btn btn-voir"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                    data-date_creation="<?= htmlspecialchars($article['date_creation'] ?? '-') ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                                <a href="#"
                                    class="btn btn-edit"
                                    style="padding: 5px 10px;"
                                    data-id="<?= $article['id'] ?>"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                >Modifier</a>
                                <button class="btn btn-delete" style="padding: 5px 10px;" data-id="<?= $article['id'] ?>">Supprimer</button>
                            <?php elseif ($_SESSION['role'] === 'livreur'): ?>
                                <button class="btn btn-voir"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                    data-date_creation="<?= htmlspecialchars($article['date_creation'] ?? '-') ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                                <button class="btn" style="padding: 5px 10px;">Confirmer article</button>
                            <?php elseif ($_SESSION['role'] === 'gestionnaire de article'): ?>
                                <button class="btn btn-voir"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                    data-date_creation="<?= htmlspecialchars($article['date_creation'] ?? '-') ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                                <button class="btn btn-attribuer" style="padding: 5px 10px;" data-id="<?= $article['id'] ?>">Attribuer</button>
                                <?php if ($article['statut'] !== 'livrée'): ?>
                                    <button class="btn btn-confirmer-article" style="padding: 5px 10px;" data-id="<?= $article['id'] ?>">
                                        Confirmer article
                                    </button>
                            <?php endif; ?>
                            <?php elseif ($_SESSION['role'] === 'gestionnaire de stock'): ?>
                                <button class="btn btn-voir"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                    data-date_creation="<?= htmlspecialchars($article['date_creation'] ?? '-') ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                                <button class="btn" style="padding: 5px 10px;">Mettre à jour Stock</button>
                            <?php else: ?>
                                <button class="btn btn-voir"
                                    data-nom_article="<?= htmlspecialchars($article['nom_article']) ?>"
                                    data-reference="<?= htmlspecialchars($article['reference']) ?>"
                                    data-categorie="<?= htmlspecialchars($article['categorie']) ?>"
                                    data-quantite_stock="<?= $article['quantite_stock'] ?>"
                                    data-lots="<?= htmlspecialchars($article['lots']) ?>"
                                    data-etat="<?= htmlspecialchars($article['etat']) ?>"
                                    data-date_creation="<?= htmlspecialchars($article['date_creation'] ?? '-') ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                            <?php endif; ?>
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal détails -->
        <div id="modal-details" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <button onclick="fermerModal()" class="close" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</button>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Détails de l'article</h2>
                <table id="details-content" style="width:100%; background:#f9f9f9; border-radius:8px; overflow:hidden;">
                    <!-- Details will be filled by JS -->
                </table>
            </div>
        </div>

        <div id="modal-edit" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <span class="close" onclick="fermerEditModal()" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</span>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Modifier l'article</h2>
                <form id="edit-article-form" method="POST" action="../../actions/modifier_article.php">
                    <input type="hidden" name="id" id="edit-id">
                    <div style="margin-bottom: 10px">
                        <label for="edit-nom_article">Nom de l'article</label>
                        <input name="nom_article" id="edit-nom_article" required>
                    </div>
                    <div style="margin-bottom: 10px">
                        <label for="edit-reference">Référence</label>
                        <input name="reference" id="edit-reference" required>
                    </div>
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

            const details = [
                ['Nom', btn.dataset.nom_article],
                ['Référence', btn.dataset.reference],
                ['Catégorie', btn.dataset.categorie],
                ['Quantité en stock', btn.dataset.quantite_stock],
                ['Lots liés', btn.dataset.lots],
                [
                    'État',
                    `<span class="etat-square ${etat}"></span> ${etatLabel}`
                ],
                ['Date de création', btn.dataset.date_creation],

                
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

    // Modal edit for articles
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('modal-edit').style.display = 'block';
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nom_article').value = this.dataset.nom_article;
            document.getElementById('edit-reference').value = this.dataset.reference;
            document.getElementById('edit-categorie').value = this.dataset.categorie;
            document.getElementById('edit-quantite_stock').value = this.dataset.quantite_stock;
            document.getElementById('edit-etat').value = this.dataset.etat;
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
                if (!articleToDelete) return;
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

    document.addEventListener('DOMContentLoaded', function() {
    const referenceInput = document.getElementById('edit-reference') || document.querySelector('input[name="reference"]');
    const categorieSelect = document.getElementById('edit-categorie') || document.querySelector('select[name="categorie"]');

    if (referenceInput && categorieSelect) {
        referenceInput.addEventListener('input', function() {
            const val = referenceInput.value.trim().toLowerCase();
            if (val.startsWith('top')) {
                categorieSelect.value = 'Top';
            } else if (val.startsWith('bas')) {
                categorieSelect.value = 'Bas';
            } else if (val.startsWith('dss')) {
                categorieSelect.value = 'Dessus';
            } else if (val.startsWith('ens')) {
                categorieSelect.value = 'Ensemble';
            }
        });
    }
});

    document.addEventListener('DOMContentLoaded', function() {
        // For add form
        const quantiteInput = document.querySelector('input[name="quantite_stock"]');
        const etatSelect = document.querySelector('select[name="etat"]');
        if (quantiteInput && etatSelect) {
            quantiteInput.addEventListener('input', function() {
                const qty = parseInt(quantiteInput.value, 10);
                if (qty < 50) {
                    etatSelect.value = 'rouge';
                } else if (qty >= 51 && qty < 150) {
                    etatSelect.value = 'orange';
                } else if (qty >= 151) {
                    etatSelect.value = 'vert';
                } else {
                    etatSelect.value = '';
                }
            });
        }

        // For edit modal
        const editQuantiteInput = document.getElementById('edit-quantite_stock');
        const editEtatSelect = document.getElementById('edit-etat');
        if (editQuantiteInput && editEtatSelect) {
            editQuantiteInput.addEventListener('input', function() {
                const qty = parseInt(editQuantiteInput.value, 10);
                if (qty < 51) {
                    editEtatSelect.value = 'rouge';
                } else if (qty >= 51 && qty < 151) {
                    editEtatSelect.value = 'orange';
                } else if (qty >= 151) {
                    editEtatSelect.value = 'vert';
                } else {
                    editEtatSelect.value = '';
                }
            });
        }
        
    });
    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>