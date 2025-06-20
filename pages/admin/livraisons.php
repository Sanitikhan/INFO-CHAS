<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}

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
    SELECT l.*, 
           f.nom AS fournisseur_nom, 
           u.username AS livreur_nom
    FROM livraisons l
    LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
    LEFT JOIN users u ON l.livreur_id = u.id
");
$livraisons = $stmt->fetchAll();

// Fetch fournisseurs from the database
$stmt = $pdo->query("SELECT id, nom FROM fournisseurs");
$fournisseurs = $stmt->fetchAll();

// Fetch all users with role 'transporteur'
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'transporteur'");
$transporteurs = $stmt->fetchAll();

// Fetch all livreurs
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'livreur'");
$livreurs = $stmt->fetchAll();

$detailsHtml = '';
if (isset($_GET['details'])) {
    $livraison_id = intval($_GET['details']);

    // Fetch livraison info
    $stmt = $pdo->prepare("
        SELECT l.*, 
            u.username AS livreur_nom, 
            f.nom AS fournisseur_nom,
            uc.username AS createur_username
        FROM livraisons l
        LEFT JOIN users u ON l.livreur_id = u.id
        LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
        LEFT JOIN users uc ON l.created_by = uc.id
        WHERE l.id = ?
    ");
    $stmt->execute([$livraison_id]);
    $livraison = $stmt->fetch();

    // Fetch livraison details
    /*$stmt = $pdo->prepare("
        SELECT ld.*, lots.reference, lots.type
        FROM livraisons_details ld
        LEFT JOIN lots ON ld.lot_id = lots.id
        WHERE ld.livraison_id = ?
    ");
    $stmt->execute([$livraison_id]);
    $details = $stmt->fetchAll();*/

    ob_start();
    if ($livraison) {
        ?>
        <h2>Livraison #<?= htmlspecialchars($livraison['numero_livraison']) ?></h2>
        <p><strong>Fournisseur:</strong> <?= htmlspecialchars($livraison['fournisseur_nom']) ?></p>
        <p><strong>Date prévue:</strong> <?= htmlspecialchars($livraison['date_prevue']) ?></p>
        <p><strong>Statut:</strong>
            <span class="statut-badge statut-<?= htmlspecialchars($livraison['statut']) ?>">
                <?= ucfirst(str_replace('_', ' ', $livraison['statut'])) ?>
            </span>
        </p>
        <p><strong>Livreur:</strong> <?= htmlspecialchars($livraison['livreur_nom']) ?></p>
        <p><strong>Notes:</strong> <?= htmlspecialchars($livraison['notes'] ?? '-') ?></p>
        <p><strong>Créé par:</strong> <?= htmlspecialchars($livraison['createur_username']) ?></p>
        <p><strong>Date de création:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($livraison['created_at']))) ?></p>
        <!--<h3>Détails des lots</h3>
        <table>
            <thead>
                <tr>
                    <th>Référence lot</th>
                    <th>Type</th>
                    <th>Quantité attendue</th>
                    <th>Quantité reçue</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['reference']) ?></td>
                    <td><?= htmlspecialchars($d['type']) ?></td>
                    <td><?= htmlspecialchars($d['quantite_attendue']) ?></td>
                    <td><?= htmlspecialchars($d['quantite_recue']) ?></td>
                    <td><?= htmlspecialchars($d['commentaire']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>-->
        <?php
    } else {
        echo "<p>Aucune information trouvée pour cette livraison.</p>";
    }
    $detailsHtml = ob_get_clean();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraisons</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/form.css">
    <link rel="stylesheet" href="../../public/modal2.css">
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
                        <a href="livraisons.php" class="nav-link active">
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
            <h1>LIVRAISONS</h1>
        </header>
        
        <section class="btn-section">
            <input type="text" id="search-livraison-input" placeholder="Rechercher..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <div class="btn-section-right">
                <button class="btn btn-add" id="add-livraisons-btn">Ajouter une livraison</button>
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

        <div class="form-section" id="add-livraisons-form-section" style="display:none;">
            <form action="../../actions/ajouter_livraisons.php" method="POST">
                <input name="numero_livraison" placeholder="Numéro de livraison" required>
                <select name="fournisseur_id" id="fournisseur_id" required>
                    <option value="">Sélectionner un fournisseur</option>
                    <?php foreach ($fournisseurs as $fournisseur): ?>
                        <option value="<?= $fournisseur['id'] ?>">
                            <?= htmlspecialchars($fournisseur['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input name="date_prevue" type="date" placeholder="Date prévue" required>
                <select name="statut" required>
                    <option value="en_attente">En attente</option>
                    <option value="en_cours">En cours</option>
                    <option value="livree">Livrée</option>
                    <option value="probleme">Problème</option>
                </select>
                <select name="livreur_id" id="livreur_id" required>
                    <option value="">Sélectionner un livreur</option>
                    <?php foreach ($livreurs as $livreur): ?>
                        <option value="<?= $livreur['id'] ?>">
                            <?= htmlspecialchars($livreur['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="texte" name="notes" placeholder="Notes">
                <button type="submit">Ajouter la livraison</button>
            </form>
        </div>

        <!-- Affichage des livraisons existantes -->
        <table id="livraisons-table" border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>N° Livraison</th>
                    <th>Fournisseur</th>
                    <th>Date prévue</th>
                    <th>Statut</th>
                    <th>Livreur assigné</th>
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

                            <td><?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?></td>
                            
                            
                            <td class="actions">
                                
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <button class="btn btn-voir"
                                        data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                        data-fournisseur="<?= htmlspecialchars($livraison['fournisseur_nom']) ?>"
                                        data-date_prevue="<?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>"
                                        data-date_livraison="<?= $livraison['date_livraison'] ? date('d/m/Y H:i', strtotime($livraison['date_livraison'])) : '-' ?>"
                                        data-statut="<?= htmlspecialchars($livraison['statut']) ?>"
                                        data-livreur="<?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?>"
                                        data-notes="<?= htmlspecialchars($livraison['notes'] ?? '-') ?>"
                                        style="padding: 5px 10px;">
                                        Voir
                                    </button>
                                    <a href="#" 
                                    class="btn btn-edit" 
                                    style="padding: 5px 10px;"
                                    data-id="<?= $livraison['id'] ?>"
                                    data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                    data-fournisseur="<?= $livraison['fournisseur_id'] ?>"
                                    data-date="<?= $livraison['date_prevue'] ?>"
                                    data-statut="<?= $livraison['statut'] ?>"
                                    data-livreur="<?= $livraison['livreur_id'] ?>"
                                    data-notes="<?= htmlspecialchars($livraison['notes'] ?? '') ?>"
                                    >Modifier</a>
                                    <button class="btn btn-delete" style="padding: 5px 10px;" data-id="<?= $livraison['id'] ?>">Supprimer</button>
                                <?php elseif ($_SESSION['role'] === 'livreur'): ?>
                                    <button class="btn btn-voir"
                                        data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                        data-fournisseur="<?= htmlspecialchars($livraison['fournisseur_nom']) ?>"
                                        data-date_prevue="<?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>"
                                        data-date_livraison="<?= $livraison['date_livraison'] ? date('d/m/Y H:i', strtotime($livraison['date_livraison'])) : '-' ?>"
                                        data-statut="<?= htmlspecialchars($livraison['statut']) ?>"
                                        data-livreur="<?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?>"
                                        data-notes="<?= htmlspecialchars($livraison['notes'] ?? '-') ?>"
                                        style="padding: 5px 10px;">
                                        Voir
                                    </button>
                                    <button class="btn" style="padding: 5px 10px;">Confirmer Livraison</button>
                                <?php elseif ($_SESSION['role'] === 'gestionnaire de livraison'): ?>
                                    <button class="btn btn-voir"
                                        data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                        data-fournisseur="<?= htmlspecialchars($livraison['fournisseur_nom']) ?>"
                                        data-date_prevue="<?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>"
                                        data-date_livraison="<?= $livraison['date_livraison'] ? date('d/m/Y H:i', strtotime($livraison['date_livraison'])) : '-' ?>"
                                        data-statut="<?= htmlspecialchars($livraison['statut']) ?>"
                                        data-livreur="<?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?>"
                                        data-notes="<?= htmlspecialchars($livraison['notes'] ?? '-') ?>"
                                        style="padding: 5px 10px;">
                                        Voir
                                    </button>
                                    <button class="btn btn-attribuer" style="padding: 5px 10px;" data-id="<?= $livraison['id'] ?>">Attribuer</button>
                                    <?php if ($livraison['statut'] !== 'livrée'): ?>
                                        <button class="btn btn-confirmer-livraison" style="padding: 5px 10px;" data-id="<?= $livraison['id'] ?>">
                                            Confirmer Livraison
                                        </button>
                                <?php endif; ?>
                                <?php elseif ($_SESSION['role'] === 'gestionnaire de stock'): ?>
                                    <button class="btn btn-voir"
                                            data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                            data-fournisseur="<?= htmlspecialchars($livraison['fournisseur_nom']) ?>"
                                            data-date_prevue="<?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>"
                                            data-date_livraison="<?= $livraison['date_livraison'] ? date('d/m/Y H:i', strtotime($livraison['date_livraison'])) : '-' ?>"
                                            data-statut="<?= htmlspecialchars($livraison['statut']) ?>"
                                            data-livreur="<?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?>"
                                            data-notes="<?= htmlspecialchars($livraison['notes'] ?? '-') ?>"
                                            style="padding: 5px 10px;">
                                            Voir
                                        </button>
                                    <button class="btn" style="padding: 5px 10px;">Mettre à jour Stock</button>
                                <?php else: ?>
                                    <button class="btn btn-voir"
                                        data-numero="<?= htmlspecialchars($livraison['numero_livraison']) ?>"
                                        data-fournisseur="<?= htmlspecialchars($livraison['fournisseur_nom']) ?>"
                                        data-date_prevue="<?= date('d/m/Y', strtotime($livraison['date_prevue'])) ?>"
                                        data-date_livraison="<?= $livraison['date_livraison'] ? date('d/m/Y H:i', strtotime($livraison['date_livraison'])) : '-' ?>"
                                        data-statut="<?= htmlspecialchars($livraison['statut']) ?>"
                                        data-livreur="<?= htmlspecialchars($livraison['livreur_nom'] ?? '-') ?>"
                                        data-notes="<?= htmlspecialchars($livraison['notes'] ?? '-') ?>"
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
                <h2 style="margin-top:0;margin-bottom:1em;font-size:1.3em;">Détails de la livraison</h2>
                <table id="details-content" style="width:100%; background:#f9f9f9; border-radius:8px; overflow:hidden;">
                    <!-- Details will be filled by JS -->
                </table>
            </div>
        </div>

        <div id="modal-edit" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerEditModal()">&times;</span>
                <h2>Modifier la livraison</h2>
                <form id="edit-livraison-form" method="POST" action="../../actions/modifier_livraison.php" style="color: #fff;">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-numero">Numéro de livraison</label>
                        <input name="numero_livraison" id="edit-numero" required>
                    </div>
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-fournisseur">Fournisseur</label>
                        <select name="fournisseur_id" id="edit-fournisseur" required>
                            <?php foreach ($fournisseurs as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-date">Date prévue</label>
                        <input type="date" name="date_prevue" id="edit-date" required>
                    </div>
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-statut">Statut</label>
                        <select name="statut" id="edit-statut" required>
                            <option value="en_attente">En attente</option>
                            <option value="en_cours">En cours</option>
                            <option value="livree">Livrée</option>
                            <option value="probleme">Problème</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-livreur">Livreur</label>
                        <select name="livreur_id" id="edit-livreur" required>
                            <?php foreach ($livreurs as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1em;">
                        <label for="edit-notes">Notes</label>
                        <input name="notes" id="edit-notes">
                    </div>
                    
                    <button type="submit">Enregistrer</button>
                </form>
            </div>
        </div>

        <div id="modal-supprimer" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerSupprimerModal()">&times;</span>
                <h2>Confirmer la suppression</h2>
                <p>Voulez-vous vraiment supprimer cette livraison ?</p>
                <button id="btn-confirm-supprimer" class="btn btn-confirm-delete">Oui, supprimer</button>
                <button type="button" class="btn" onclick="fermerSupprimerModal()">Annuler</button>
            </div>
        </div>

        <div id="modal-attribuer" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="fermerAttribuerModal()">&times;</span>
                <h2>Attribuer un livreur</h2>
                <form id="attribuer-form" method="POST" style="color: #fff;">
                    <input type="hidden" name="livraison_id" id="attribuer-livraison-id">
                    <label for="attribuer-livreur-id">Sélectionner un livreur :</label>
                    <select name="livreur_id" id="attribuer-livreur-id" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($livreurs as $livreur): ?>
                            <option value="<?= $livreur['id'] ?>"><?= htmlspecialchars($livreur['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-success" style="margin-top:10px;">Attribuer</button>
                </form>
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

    document.addEventListener('DOMContentLoaded', function() {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500); // Remove after fade out
            }, 5000); // 5 seconds
        }
    });

    /* Search 'livraison' */
    document.getElementById('search-livraison-input').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#livraisons-table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
    });

    // Voir button logic for livraisons
    document.querySelectorAll('.btn-voir').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const row = btn.closest('tr');
            const statut = row.dataset.statut;
            const statutLabel = btn.dataset.statut.charAt(0).toUpperCase() + btn.dataset.statut.slice(1);

            const details = [
                ['Numéro', btn.dataset.numero],
                ['Fournisseur', btn.dataset.fournisseur],
                ['Date prévue', btn.dataset.date_prevue],
                ['Date livraison', btn.dataset.date_livraison],
                [
                    'Statut',
                    `<span class="statut-badge ${statut}"></span> ${statutLabel}`
                ],
                ['Livreur', btn.dataset.livreur],
                ['Notes', btn.dataset.notes]
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

    // Modal edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('modal-edit').style.display = 'block';
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-numero').value = this.dataset.numero;
            document.getElementById('edit-fournisseur').value = this.dataset.fournisseur;
            document.getElementById('edit-date').value = this.dataset.date;
            document.getElementById('edit-statut').value = this.dataset.statut;
            document.getElementById('edit-livreur').value = this.dataset.livreur;
            document.getElementById('edit-notes').value = this.dataset.notes;
        });
    });

    // Modal delete
    let livraisonToDelete = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Only attach to table delete buttons
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                livraisonToDelete = this.dataset.id;
                document.getElementById('modal-supprimer').style.display = 'block';
            });
        });

        // Attach event to the confirm button in the modal
        const confirmBtn = document.getElementById('btn-confirm-supprimer');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (!livraisonToDelete) return;
                fetch('../../actions/supprimer_livraison.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(livraisonToDelete)
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

    // Open modal on "Attribuer" button click
    document.querySelectorAll('.btn-attribuer').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('attribuer-livraison-id').value = this.dataset.id;
            document.getElementById('modal-attribuer').style.display = 'block';
        });
    });

    function fermerAttribuerModal() {
        document.getElementById('modal-attribuer').style.display = 'none';
    }

    // Handle form submit via AJAX
    document.getElementById('attribuer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const livraisonId = document.getElementById('attribuer-livraison-id').value;
        const livreurId = document.getElementById('attribuer-livreur-id').value;
        fetch('../../actions/attribuer_livreur.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'livraison_id=' + encodeURIComponent(livraisonId) + '&livreur_id=' + encodeURIComponent(livreurId)
        })
        .then(response => response.text())
        .then(result => {
            window.location.reload();
        });
    });

    // Confirm Livraison for gestionnaire de livraison
    document.querySelectorAll('.btn-confirmer-livraison').forEach(btn => {
        btn.addEventListener('click', function() {
            const livraisonId = this.dataset.id;
            if (!livraisonId) return;
            if (!confirm('Confirmer que cette livraison est livrée ?')) return;

            fetch('../../actions/confirmer_livraison.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'livraison_id=' + encodeURIComponent(livraisonId)
            })
            .then(response => response.text())
            .then(result => {
                window.location.reload();
            })
            .catch(() => alert('Erreur lors de la confirmation.'));
        });
    });

    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>