<?php
session_start();
require_once('../../includes/config.php');

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/

// Total lots in stock
$total_lots = $pdo->query("SELECT COUNT(*) FROM lots")->fetchColumn();

// Lots with état 'rouge'
$lots_rouge = $pdo->query("SELECT COUNT(*) FROM lots WHERE etat = 'rouge'")->fetchColumn();
$lots_vert = $pdo->query("SELECT COUNT(*) FROM lots WHERE etat = 'vert'")->fetchColumn();
$lots_orange = $pdo->query("SELECT COUNT(*) FROM lots WHERE etat = 'orange'")->fetchColumn();

// Example: Log when a user adds a new lot
$reference = $_POST['reference'] ?? null;
$type = $_POST['type'] ?? null;

// Count lots in etat 'rouge' or 'orange'
$stmt = $pdo->query("SELECT COUNT(*) FROM lots WHERE etat IN ('rouge', 'orange')");
$total_lots_alertes = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/tableaudebord.css">
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
                    <a href="dashboard.php" class="nav-link active">
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
            <h1>TABLEAU DE BORD</h1>
        </header>
        <p>Bienvenue sur votre application de gestion de stock <strong><?php echo $_SESSION['username']; ?></strong> ! </p>
        <p>Vous êtes connecté en tant que <strong><?php echo $_SESSION['role']; ?></strong>.</p>

        <div class="dashboard-grid">
            <div class="grid performance" id="performance">
                <h3>Performances</h3>
                <div class="perf-grid">
                    <div class="perf-stats">
                        <div class="perf-item">
                            <span class="material-symbols-rounded">inventory_2</span>
                            <div>
                                <div class="perf-label">Lots total en stock</div>
                                <div class="perf-value"><?= $total_lots ?></div>
                            </div>
                        </div>
                        <div class="perf-item">
                            <span class="material-symbols-rounded">warning</span>
                            <div>
                                <div class="perf-label">Lots sous le seuil</div>
                                <div class="perf-value"><?= $lots_rouge ?></div>
                            </div>
                        </div>
                        <div class="perf-item">
                            <span class="material-symbols-rounded">local_shipping</span>
                            <div>
                                <div class="perf-label">Livraisons en attente</div>
                                <div class="perf-value"><p>x</p></div>
                            </div>
                        </div>
                        <div class="perf-item">
                            <span class="material-symbols-rounded">package</span>
                            <div>
                                <div class="perf-label">Livraisons Livrée</div>
                                <div class="perf-value"><p>x</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="perf-graph">
                        <div class="perf-label">Etat du stock</div>
                        <div class="graph-content">
                            <canvas id="perfChart" class="graph"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid recents" id="recents">
                <h3>Activités récentes</h3>
                <ul>
                    <?php
                    // Fetch the 10 most recent activities
                    $stmt = $pdo->query("
                        SELECT a.*, u.username 
                        FROM activity_log a
                        LEFT JOIN users u ON a.user_id = u.id
                        ORDER BY a.created_at DESC
                        LIMIT 10
                    ");
                    $activities = $stmt->fetchAll();
                    foreach ($activities as $activity): ?>
                        <li>
                            <?= htmlspecialchars($activity['action']) ?>
                            <?= !empty($activity['details']) ? ' - ' . htmlspecialchars($activity['details']) : '' ?>
                            ajouté par <strong><?= htmlspecialchars($activity['username']) ?></strong>
                            <span style="color:#888; font-size:0.9em;">(<?= $activity['created_at'] ?>)</span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($activities)): ?>
                        <li>Aucune activité récente.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="grid alertes" id="alertes">
                <h3>Alertes</h3>
                <div class="alert-summary" style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="background: #f44336; color: #fff; padding: 10px 20px; border-radius: 5px;">
                    <strong>Total alertes :</strong> 
                    </div>
                    <a href="alertes.php" class="btn" style="text-decoration: none;">Voir les alertes</a>
                </div>
            </div>
        </div>
    </section>

    <script src="../../actions/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('perfChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Vert', 'Orange', 'Rouge'],
        datasets: [{
            data: [<?= $lots_vert ?>, <?= $lots_orange ?>, <?= $lots_rouge ?>],
            backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
        }]
    },
    options: {
        plugins: {
            legend: { display: true, position: 'bottom' }
        },
        cutout: '70%',
    }
});
</script>
</body>
</html>