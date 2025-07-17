<?php
session_start();
require_once('../../includes/config.php');

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/

// Query for lots with etat 'rouge'
$stmt = $pdo->query("SELECT * FROM lots WHERE etat = 'rouge'");
$lots_rouge = $stmt->fetchAll();
$count_rouge = count($lots_rouge);

// Query for lots with etat 'orange'
$stmt = $pdo->query("SELECT * FROM lots WHERE etat = 'orange'");
$lots_orange = $stmt->fetchAll();
$count_orange = count($lots_orange);

// Query for livraisons with statut 'probleme'
$stmt = $pdo->query("SELECT * FROM livraisons WHERE statut = 'probleme'");
$livraisons_probleme = $stmt->fetchAll();
$count_probleme = count($livraisons_probleme);

// Total "problème critique"
$total_critique = $count_rouge + $count_probleme;

$stmt = $pdo->query("SELECT DISTINCT role FROM users WHERE role IS NOT NULL AND role != ''");
$roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

$user_role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$alertes = [];
if ($user_role && $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM alertes WHERE (role = ? OR user_id = ?) ORDER BY created_at DESC");
    $stmt->execute([$user_role, $user_id]);
    $alertes = $stmt->fetchAll();
}

$all_alertes = [];
if ($user_role === 'admin') {
    $stmt = $pdo->query("SELECT * FROM alertes ORDER BY created_at DESC");
    $all_alertes = $stmt->fetchAll();
}

// For the form select (array of arrays)
$userList = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll();

// For mapping user_id to username (associative array)
$users = [];
$stmt = $pdo->query("SELECT id, username FROM users");
foreach ($stmt->fetchAll() as $user) {
    $users[$user['id']] = $user['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertes</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/alertes.css">
    <link rel="icon" href="../../img/logo_w.png" type="image/png">
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
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
                    <a href="#" class="nav-link active dropdown-toggle">
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
                            <a href="alertes.php" class="nav-link active dropdown-link">Alertes</a>
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
            <h1>Alertes</h1>
        </header>

        <div class="alert-summary" style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="background: #393E46; color: #fff; padding: 10px 20px; border-radius: 5px;">
                <strong>Total alertes :</strong> <?= $total_critique ?>
            </div>
            <div id="show-rouge" style="cursor:pointer; background: #f44336; color: #fff; padding: 10px 20px; border-radius: 5px;">
                <span class="material-symbols-rounded">inventory_2</span>
                <strong>Lots rouge :</strong> <?= $count_rouge ?>
            </div>
            <div id="show-orange" style="cursor:pointer; background: #ff9800; color: #fff; padding: 10px 20px; border-radius: 5px;">
                <span class="material-symbols-rounded">local_shipping</span>
                <strong>Lots orange :</strong> <?= $count_orange ?>
            </div>
            <div id="show-probleme" style="cursor:pointer; background:rgb(255, 213, 0); color: #fff; padding: 10px 20px; border-radius: 5px;">
                <span class="material-symbols-rounded">local_shipping</span>
                <strong>Problèmes livraisons :</strong> <?= $count_probleme ?>
            </div>
            <div class="btn-section-right">
                <button id="open-alert-modal" style="border:none; cursor:pointer; font-weight:800; background:#393E46; color: #fff; padding: 10px 20px; border-radius: 5px;">Envoyer une alerte</button>
            </div>
        </div>

            <div id="alert-modal" class="modal" style="display:none;">
                <div class="modal-content" style="max-width:400px;">
                    <span class="close" onclick="closeAlertModal()" style="float:right;cursor:pointer;">&times;</span>
                    <h2>Envoyer une alerte</h2>
                    <form id="alert-form" method="POST" action="../../actions/envoyer_alerte.php">
                        <div style="margin-bottom:1em;">
                            <select name="role">
                                <option value="">Sélectionner un rôle (optionnel)</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role) ?>">
                                        <?= ucfirst(htmlspecialchars($role)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="user_id">
                                <option value="">Ou sélectionner un utilisateur</option>
                                <?php foreach ($userList as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="alert-message">Message</label><br>
                            <textarea id="alert-message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn">Envoyer</button>
                    </form>
                </div>
            </div>

        <div class="alertes-grid">


            <div class="grid" id="list-rouge" style="display:none; margin-bottom:20px;">
                <h3>Lots rouge</h3>
                <?php foreach ($lots_rouge as $lot): ?>
                    <div><?= htmlspecialchars($lot['reference']) ?> (<?= htmlspecialchars($lot['type']) ?>)</div>
                <?php endforeach; ?>
            </div>

            <div class="grid" id="list-orange" style="display:none; margin-bottom:20px;">
                <h3>Lots orange</h3>
                <?php foreach ($lots_orange as $lot): ?>
                    <div><?= htmlspecialchars($lot['reference']) ?> (<?= htmlspecialchars($lot['type']) ?>)</div>
                <?php endforeach; ?>
            </div>

            <div class="grid" id="list-probleme" style="display:none; margin-bottom:20px;">
                <h3>Problèmes livraisons</h3>
                <?php foreach ($livraisons_probleme as $livraison): ?>
                    <div>
                        Livraison n°<?= htmlspecialchars($livraison['numero_livraison']) ?> :
                        <?= !empty($livraison['notes']) ? htmlspecialchars($livraison['notes']) : "Problème signalé." ?>
                    </div>
                <?php endforeach; ?>
            </div>

        
            <div class="grid grid-critiques">
                <h3>Problèmes critiques</h3>
                <?php if ($total_critique > 0): ?>
                    <?php foreach ($lots_rouge as $lot): ?>
                        <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-symbols-rounded" style="color: #fff;">warning</span>
                            <div>
                                <strong>Lot critique :</strong>
                                <?= htmlspecialchars($lot['reference']) ?> (<?= htmlspecialchars($lot['type']) ?>)
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($livraisons_probleme as $livraison): ?>
                        <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-symbols-rounded" style="color: #fff;">warning</span>
                            <div>
                                <strong>Livraison n°<?= htmlspecialchars($livraison['numero_livraison']) ?> :</strong>
                                <?= !empty($livraison['notes']) ? htmlspecialchars($livraison['notes']) : "Problème signalé." ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-success">
                        Aucun problème critique détecté.
                    </div>
                <?php endif; ?>
            </div>
            <div class="grid grid-alertes">
                <h3>Alertes reçues</h3>
                <?php if (!empty($alertes)): ?>
                    <?php foreach ($alertes as $alerte): ?>
                        <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-symbols-rounded" style="color: #fff;">notification_important</span>
                            <div>
                                <?= nl2br(htmlspecialchars($alerte['message'])) ?>
                                <div style="font-size:0.9em; color:#ccc;">
                                    Envoyé par : <?= htmlspecialchars($alerte['sender'] ?? 'Inconnu') ?><br>
                                    <?= htmlspecialchars($alerte['created_at']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-success">
                        Aucune alerte reçue.
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($user_role === 'admin'): ?>
                <div class="grid grid-all-alertes">
                    <h3>Toutes les alertes</h3>
                    <?php if (!empty($all_alertes)): ?>
                        <?php foreach ($all_alertes as $alerte): ?>
                            <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span class="material-symbols-rounded" style="color: #fff;">notification_important</span>
                                <div>
                                    <?= nl2br(htmlspecialchars($alerte['message'])) ?>
                                    <div style="font-size:0.9em; color:#ccc;">
                                        Envoyé par : <?= htmlspecialchars($alerte['sender'] ?? 'Inconnu') ?><br>
                                        <?php if (!empty($alerte['user_id']) && isset($users[$alerte['user_id']])): ?>
                                            Pour : <?= htmlspecialchars($users[$alerte['user_id']]) ?><br>
                                        <?php elseif (!empty($alerte['role'])): ?>
                                            Pour : <?= htmlspecialchars($alerte['role']) ?><br>
                                        <?php else: ?>
                                            Pour : Tous<br>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($alerte['created_at']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-success">
                            Aucune alerte enregistrée.
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
        </div>
    </section>


    <script src="../../actions/script.js"></script>
    <script>
document.getElementById('show-rouge').onclick = function() {
    document.getElementById('list-rouge').style.display =
        document.getElementById('list-rouge').style.display === 'none' ? 'block' : 'none';
};
document.getElementById('show-orange').onclick = function() {
    document.getElementById('list-orange').style.display =
        document.getElementById('list-orange').style.display === 'none' ? 'block' : 'none';
};
document.getElementById('show-probleme').onclick = function() {
    document.getElementById('list-probleme').style.display =
        document.getElementById('list-probleme').style.display === 'none' ? 'block' : 'none';
};

document.getElementById('open-alert-modal').onclick = function() {
    document.getElementById('alert-modal').style.display = 'flex';
};
function closeAlertModal() {
    document.getElementById('alert-modal').style.display = 'none';
}
</script>
</body>
</html>