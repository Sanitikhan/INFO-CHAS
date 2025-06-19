<?php
session_start();
require_once('../../includes/config.php');

$stmt = $pdo->query("SELECT id, numero_livraison, date_prevue FROM livraisons");
$livraisons = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, reference, date_livraison FROM commandes WHERE date_livraison IS NOT NULL");
$commandes = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, title, date FROM evenements");
$evenements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/calendrier.css">
    <link rel="stylesheet" href="../../public/calendar.css">
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
                    <a href="calendrier.php" class="nav-link active">
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
            <h1>CALENDRIER</h1>
        </header>
        <section class="btn-section">
            <button id="add-event-btn" class="btn" style="margin-bottom: 20px;">Ajouter un événement</button>
        </section>
            
            <div id="calendar"></div>

        <div id="add-event-modal" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:350px;">
                <span class="close" onclick="closeAddEventModal()" style="float:right;cursor:pointer;">&times;</span>
                <h2>Nouvel événement</h2>
                <form id="add-event-form">
                    <div style="margin-bottom:1em;">
                        <label for="event-title">Titre</label><br>
                        <input type="text" id="event-title" required>
                    </div>
                    <div style="margin-bottom:1em;">
                        <label for="event-date">Date</label><br>
                        <input type="date" id="event-date" required>
                    </div>
                    <button type="submit" class="btn">Ajouter</button>
                </form>
            </div>
        </div>
    </section>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="../../actions/script.js"></script>
    <script>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 600,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            <?php foreach ($livraisons as $livraison): ?>
            {
                title: "Livraison #<?= htmlspecialchars($livraison['numero_livraison']) ?>",
                start: "<?= $livraison['date_prevue'] ?>"
            },
            <?php endforeach; ?>
            <?php foreach ($evenements as $evt): ?>
            {
                title: "<?= htmlspecialchars($evt['title']) ?>",
                start: "<?= $evt['date'] ?>"
            },
            <?php endforeach; ?>
            <?php foreach ($commandes as $commande): ?>
            {
                title: "Commande #<?= htmlspecialchars($commande['reference']) ?>",
                start: "<?= $commande['date_livraison'] ?>"
            },
            <?php endforeach; ?>
        ]
    });
    calendar.render();

    // Modal logic
    document.getElementById('add-event-btn').onclick = function() {
        document.getElementById('add-event-modal').style.display = 'flex';
    };
    document.getElementById('add-event-form').onsubmit = function(e) {
        e.preventDefault();
        const title = document.getElementById('event-title').value.trim();
        const date = document.getElementById('event-date').value;
        if (title && date) {
            fetch('../../actions/ajouter_event.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'title=' + encodeURIComponent(title) + '&date=' + encodeURIComponent(date)
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'ok') {
                    calendar.addEvent({
                        title: title,
                        start: date
                    });
                    closeAddEventModal();
                    document.getElementById('add-event-form').reset();
                } else {
                    alert('Erreur lors de l\'ajout de l\'événement.');
                }
            });
        }
    };
});

function closeAddEventModal() {
    document.getElementById('add-event-modal').style.display = 'none';
}
</script>
</body>
</html>