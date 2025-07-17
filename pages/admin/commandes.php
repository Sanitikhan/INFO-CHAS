<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/


    // Lots à préparer
    $sqlApreparer = "
        SELECT 
            c.id AS commande_id,
            c.date_prevue_envoi,
            c.etat_preparation,
            cl.quantite AS quantite_lot_commande,
            l.id AS lot_id,
            GROUP_CONCAT(
                CONCAT(
                    a.nom_article, ' - ', 
                    a.reference, ' - ',
                    al.couleur, ' - ',
                    al.taille, ' x', al.quantite
                )
                SEPARATOR '<br>'
            ) AS articles
        FROM commandes c
        JOIN commande_lot cl ON c.id = cl.commande_id
        JOIN lots l ON cl.lot_id = l.id
        LEFT JOIN article_lot al ON l.id = al.lot_id
        LEFT JOIN articles a ON al.article_id = a.id
        WHERE c.etat_preparation != 'prêt'
        GROUP BY c.id, l.id
        ORDER BY c.date_prevue_envoi ASC
    ";
    $lotsAPreparer = $pdo->query($sqlApreparer)->fetchAll(PDO::FETCH_ASSOC);


    foreach ($lotsAPreparer as &$lot) {
    $articlesRaw = explode('<br>', $lot['articles']);
    $articlesParTaille = [];

    foreach ($articlesRaw as $articleStr) {
        if (preg_match('/ - (.+?) - (.+?) - (.+?) x(\d+)/', $articleStr, $matches)) {
            $couleur = $matches[2];
            $taille = $matches[3];
            $quantite = $matches[4];

            if (!isset($articlesParTaille[$taille])) {
                $articlesParTaille[$taille] = [];
            }
            $articlesParTaille[$taille][] = "$couleur x$quantite";
        }
    }

    $articlesAffichage = [];
    foreach ($articlesParTaille as $taille => $couleurs) {
        $articlesAffichage[] = "$taille (" . implode(', ', $couleurs) . ")";
    }

    $lot['articles'] = implode('<br>', $articlesAffichage);
}


    // Lots prêts
    $sqlPrets = "
        SELECT 
            c.id AS commande_id,
            c.date_prevue_envoi,
            c.etat_preparation,
            l.id AS lot_id,
            GROUP_CONCAT(
                CONCAT(
                    a.nom_article, ' - ', 
                    a.reference, ' - ',
                    al.couleur, ' - ',
                    al.taille, ' x', al.quantite
                )
                SEPARATOR '<br>'
            ) AS articles
        FROM commandes c
        JOIN commande_lot cl ON c.id = cl.commande_id
        JOIN lots l ON cl.lot_id = l.id
        LEFT JOIN article_lot al ON l.id = al.lot_id
        LEFT JOIN articles a ON al.article_id = a.id
        WHERE c.etat_preparation = 'prêt'
        GROUP BY c.id, l.id
        ORDER BY c.date_prevue_envoi ASC
        ";

    $lotsPrets = $pdo->query($sqlPrets)->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les lots
    $lots = $pdo->query("
        SELECT l.id, 
            GROUP_CONCAT(a.nom_article, ' (', al.couleur, '-', al.taille, ') x', al.quantite SEPARATOR ', ') AS contenu
        FROM lots l
        LEFT JOIN article_lot al ON l.id = al.lot_id
        LEFT JOIN articles a ON al.article_id = a.id
        GROUP BY l.id
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer toutes les commandes
    $sql = "
        SELECT 
            c.*, 
            GROUP_CONCAT(
                CONCAT(
                    a.nom_article, ' - ', 
                    a.reference, ' - ',
                    al.couleur, ' - ',
                    al.taille, ' x', al.quantite
                ) SEPARATOR '<br>'
            ) AS articles
        FROM commandes c
        LEFT JOIN commande_lot cl ON c.id = cl.commande_id
        LEFT JOIN lots l ON cl.lot_id = l.id
        LEFT JOIN article_lot al ON l.id = al.lot_id
        LEFT JOIN articles a ON al.article_id = a.id
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM commandes ORDER BY date_commande DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statutBadgeClass($etat) {
    switch (strtolower($etat)) {
        case 'à préparer': return 'statut-attendue';
        case 'en cours': return 'statut-en_cours';
        case 'prêt': return 'statut-livree';
        case 'problème': return 'statut-probleme';
        default: return 'statut-attendue';
    }
}
?>
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/form.css">
    <link rel="stylesheet" href="../../public/stock.css">
    <link rel="stylesheet" href="../../public/badges.css">
    <link rel="stylesheet" href="../../public/livraisons.css">
    <link rel="stylesheet" href="../../public/flashmessage.css">
    <link rel="stylesheet" href="../../public/modal2.css">
    <link rel="stylesheet" href="../../public/modaledit.css">
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
                        <a href="#" class="nav-link dropdown-toggle">
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
                                <a href="articles.php" class="nav-link dropdown-link">Articles</a>
                            </li>
                        </ul>
                    </li>
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                <li class="nav-item">
                    <a href="commandes.php" class="nav-link active">
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
                <label for="date_commande">Date de commande :</label>
                <input type="date" id="date_commande" name="date_commande" required>

                <label for="date_prevue_envoi">Date prévue d'envoi :</label>
                <input type="date" id="date_prevue_envoi" name="date_prevue_envoi" required>

                <label for="etat_preparation">État de préparation :</label>
                <select id="etat_preparation" name="etat_preparation" required>
                    <option value="">Sélectionner un état</option>
                    <option value="à préparer">À préparer</option>
                    <option value="en cours">En cours</option>
                    <option value="prêt">Prêt</option>
                </select>

                <h3>Lots à inclure :</h3>
                <?php
                // Récupérer les lots prêts (ou tous selon ta logique)
                $lots = $pdo->query("
                    SELECT l.id, 
                        GROUP_CONCAT(CONCAT(a.nom_article, ' (', al.couleur, '-', al.taille, ') x', al.quantite) SEPARATOR ', ') AS contenu
                    FROM lots l
                    LEFT JOIN article_lot al ON l.id = al.lot_id
                    LEFT JOIN articles a ON al.article_id = a.id
                    GROUP BY l.id
                ")->fetchAll(PDO::FETCH_ASSOC);

                 foreach ($lots as $lot): ?>
                    <div style="margin-bottom: 8px;">
                        <input type="checkbox" name="lots[<?= $lot['id'] ?>]" id="lot<?= $lot['id'] ?>" value="1">
                        <label for="lot<?= $lot['id'] ?>" style="cursor:pointer; font-weight:bold;">
                            Lot #<?= $lot['id'] ?>
                        </label>
                        <button type="button" onclick="toggleDetails(<?= $lot['id'] ?>)" style="margin-left:10px;">+ détail</button>
                        <input type="number" name="quantite[<?= $lot['id'] ?>]" min="1" placeholder="Quantité" style="width:80px; margin-left:10px;">
                        
                        <div id="details-<?= $lot['id'] ?>" style="display:none; margin-left:20px; margin-top:5px; font-size:0.9em; color:#ccc;">
                            <?= htmlspecialchars($lot['contenu']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit">Ajouter la commande</button>
            </form>
        </div>

        <h2>Lots à préparer</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Commande n°</th>
                    <th>Lot n°</th>
                    <th>Quantité de lot</th>
                    <th>Articles du lot</th>
                    <th>Date prévue d'envoi</th>
                    <th>État</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lotsAPreparer as $lot): ?>
                <?php
                    // Retrouver la commande liée à ce lot
                    $commandeId = $lot['commande_id'];
                    $commandeAssociee = null;
                    foreach ($commandes as $commande) {
                        if ($commande['id'] == $commandeId) {
                            $commandeAssociee = $commande;
                            break;
                        }
                    }
                ?>
                    <tr>
                        <td>#<?= $lot['commande_id'] ?></td>
                        <td><?= $lot['lot_id'] ?></td>
                        <td><?= $lot['quantite_lot_commande'] ?></td>
                        <td><?= $lot['articles'] ?></td>
                        <td><?= date('d/m/Y', strtotime($lot['date_prevue_envoi'])) ?></td>
                        <td><span class="statut-badge <?= statutBadgeClass($commande['etat_preparation']) ?>">
                            <?= htmlspecialchars($commande['etat_preparation']) ?>
                        </span></td>
                        <td>
                            <?php if ($commandeAssociee): ?>
                                <button class="btn btn-voir"
                                    data-id="<?= $commandeAssociee['id'] ?>" 
                                    data-date_commande="<?= htmlspecialchars($commandeAssociee['date_commande']) ?>"
                                    data-date_prevue_envoi="<?= htmlspecialchars($commandeAssociee['date_prevue_envoi']) ?>"
                                    data-etat_preparation="<?= htmlspecialchars($commandeAssociee['etat_preparation']) ?>"
                                    data-articles="<?= htmlspecialchars($lot['articles']) ?>"
                                    style="padding: 5px 10px;">
                                    Voir
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-edit"
                                style="padding: 5px 10px;"
                                data-id="<?= $commandeAssociee['id'] ?>" 
                                data-date_commande="<?= htmlspecialchars($commandeAssociee['date_commande']) ?>"
                                data-date_prevue_envoi="<?= htmlspecialchars($commandeAssociee['date_prevue_envoi']) ?>"
                                data-etat_preparation="<?= htmlspecialchars($commandeAssociee['etat_preparation']) ?>"
                            >
                                Modifier
                            </button>
                            <form method="post" action="../../actions/marquer_pret.php" style="background: none; border: none; padding: 0; box-shadow: none;">
                                <input type="hidden" name="commande_id" value="<?= $lot['commande_id'] ?>">
                                <button class="btn" type="submit" style="padding: 5px 10px; font-weight: 200;">Marquer comme prêt</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Lots prêts à expédier</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Commande n°</th>
                    <th>Lot n°</th>
                    <th>Articles du lot</th>
                    <th>Date prévue d'envoi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lotsPrets as $lot): ?>
                    <tr>
                        <td>#<?= $lot['commande_id'] ?></td>
                        <td><?= $lot['lot_id'] ?></td>
                        <td><?= $lot['articles'] ?></td>
                        <td><?= date('d/m/Y', strtotime($lot['date_prevue_envoi'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>





        <!-- Modal Voir -->
        <div id="voir-lot-modal" class="modal" style="display:none;">
            <div class="modal-content" style="background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <button onclick="document.getElementById('voir-lot-modal').style.display='none'" class="close" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</button>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Détails du lot</h2>
                <table id="voir-lot-details" style="width:100%; background:#f9f9f9; border-radius:8px; overflow:hidden;">
                    <!-- Details will be filled by JS -->
                </table>
            </div>
        </div>

        <!-- Modal Modifier Commande -->
        <div id="modal-edit" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); padding:2em; position:relative;">
                <span class="close" onclick="fermerEditCommandeModal()" style="position:absolute;top:10px;right:10px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</span>
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Modifier la commande</h2>
                <form id="edit-commande-form" method="POST" action="../../actions/modifier_commande.php" style="color: #fff;">
                    <input type="hidden" name="id" id="edit-id">

                    <div style="margin-bottom: 10px">
                        <label>Date de commande :</label>
                        <input type="date" name="date_commande" id="edit-date_commande" required>
                    <div style="margin-bottom: 10px">
                        <label>Date prévue d'envoi :</label>
                        <input type="date" name="date_prevue_envoi" id="edit-date_prevue_envoi" required>

                    </div>
                    <div style="margin-bottom: 10px">
                        <label>État préparation :</label>
                        <select name="etat_preparation" id="edit-etat_preparation" required>
                            <option value="">Sélectionner un état</option>
                            <option value="à préparer">À préparer</option>
                            <option value="en cours">En cours</option>
                            <option value="prêt">Prêt</option>
                        </select>
                    </div>
                    <button type="submit">Enregistrer</button>
                </form>
            </div>
        </div>

        <div id="modal-supprimer-commande" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerSupprimerCommandeModal()">&times;</span>
                <h2>Confirmer la suppression</h2>
                <p>Voulez-vous vraiment supprimer cette commande ?</p>
                <button id="btn-confirm-supprimer-commande" class="btn btn-confirm-delete">Oui, supprimer</button>
                <button type="button" class="btn" onclick="fermerSupprimerCommandeModal()">Annuler</button>
            </div>
        </div>

        

    </section>

    <script>

    document.addEventListener('DOMContentLoaded', function () {
    // Bouton "Ajouter commande"
    document.getElementById('add-commande-btn')?.addEventListener('click', function() {
        const formSection = document.getElementById('add-commande-form-section');
        formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
    });

    // Tri commandes
    const sortSelect = document.getElementById('sort-select');
    const table = document.getElementById('commandes-table');
    const tbody = table?.querySelector('tbody');

    sortSelect?.addEventListener('change', function() {
        const sortType = this.value;
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            let valA, valB;
            switch (sortType) {
                case 'reference': valA = a.children[1].textContent.trim().toLowerCase(); valB = b.children[1].textContent.trim().toLowerCase(); return valA.localeCompare(valB, undefined, {numeric: true});
                case 'date_commande': valA = a.children[4].textContent.trim(); valB = b.children[4].textContent.trim(); return valA.localeCompare(valB);
                case 'date_livraison': valA = a.children[5].textContent.trim(); valB = b.children[5].textContent.trim(); return valA.localeCompare(valB);
                case 'preparateur': valA = a.children[2].textContent.trim().toLowerCase(); valB = b.children[2].textContent.trim().toLowerCase(); return valA.localeCompare(valB);
                case 'etat': valA = a.children[6].textContent.trim().toLowerCase(); valB = b.children[6].textContent.trim().toLowerCase(); return valA.localeCompare(valB);
                default: return 0;
            }
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    });

    // Voir commande
    document.querySelectorAll('.btn-voir').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const details = [
                ['Date de la commande', btn.dataset.date_commande],
                ["Date prévue à l'envoi", btn.dataset.date_prevue_envoi],
                ['Etat', btn.dataset.etat_preparation],
                ['Articles du lot', btn.dataset.articles || '—']
            ];

            let html = '<tbody>';
            details.forEach(([label, value]) => {
                html += `<tr><th>${label}</th><td>${value}</td></tr>`;
            });
            html += '</tbody>';

            document.getElementById('voir-lot-details').innerHTML = html;
            document.getElementById('voir-lot-modal').style.display = 'flex';
        });
    });

    // Modifier commande
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('modal-edit').style.display = 'block';
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-date_commande').value = this.dataset.date_commande;
            document.getElementById('edit-date_prevue_envoi').value = this.dataset.date_prevue_envoi;
            document.getElementById('edit-etat_preparation').value = this.dataset.etat_preparation;
        });
    });

    // Recherche commande
    const searchInput = document.getElementById('search-commande-input');
    const rows = table?.querySelectorAll('tbody tr');
    searchInput?.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(filter) ? '' : 'none';
        });
    });

    // Suppression commande
    let commandeToDelete = null;
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            commandeToDelete = this.dataset.id;
            document.getElementById('modal-supprimer-commande').style.display = 'block';
        });
    });

    document.getElementById('btn-confirm-supprimer-commande')?.addEventListener('click', function() {
        if (!commandeToDelete) return;
        fetch('../../actions/supprimer_commande.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(commandeToDelete)
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Erreur lors de la suppression.');
            }
        });
    });

    // Flash message
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 4000);
    }
});

function fermerEditCommandeModal() {
    document.getElementById('modal-edit').style.display = 'none';
}

function fermerSupprimerCommandeModal() {
    document.getElementById('modal-supprimer-commande').style.display = 'none';
}

    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>