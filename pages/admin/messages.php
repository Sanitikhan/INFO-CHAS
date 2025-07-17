<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/

try {
    $stmt = $pdo->query("SELECT * FROM fournisseurs");
    $fournisseurs = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $fournisseurs = [];
}

// Fetch all users for the select (if not already done)
$users = $pdo->query("SELECT id, username FROM users")->fetchAll();

// Fetch received messages for the logged-in user
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT m.*, u.username AS sender_name 
        FROM messages m 
        JOIN users u ON m.expediteur_id = u.id 
        WHERE m.receveur_id = ? 
        ORDER BY m.id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $received_messages = $stmt->fetchAll();
} else {
    $received_messages = [];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/mesmessages.css">
    <link rel="stylesheet" href="../../public/messages.css">
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
                            <a class="nav-link active dropdown-title">Messagerie</a>
                        </li>
                        <li class="nav-item">
                            <a href="messages.php" class="nav-link active dropdown-link">Mes messages</a>
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
            <h1>MES MESSAGES</h1>
        </header>

        <section class="btn-section">
            <input type="text" id="search-message-input" placeholder="Rechercher..." class="btn-search" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <button class="btn btn-add" id="add-message-btn">Nouveau message</button>
        </section>
        
        <div class="messages-container">
                <!-- Send Message Form -->
                <div class="form-section" id="add-message-form-section" style="display:none;">
                    <form action="../../actions/messagerie.php" method="POST">
                        <label for="receveur_id">Destinataire :</label>
                        <select name="receveur_id" required>
                            <?php foreach ($users as $user): ?>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <label for="objet">Sujet :</label>
                        <input type="text" name="objet" required>
                        <textarea name="corps" id="corps" rows="6" style="width:100%; resize:vertical; display:block; margin-bottom:1em;" required></textarea>
                        <button type="submit" name="send_message">Envoyer</button>
                    </form>
                </div>
        </div>
        <div class="received-messages">
            <!-- Display messages -->
            <h2>Messages reçus</h2>
            <ul>
                <?php foreach ($received_messages as $msg): ?>
                    <li>
                        <strong><?= htmlspecialchars($msg['objet']) ?></strong>
                        de <?= htmlspecialchars($msg['sender_name']) ?><br>
                        <?= nl2br(htmlspecialchars($msg['corps'])) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <script>
        /* Search 'message' */
        document.getElementById('search-message-input').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        const rows = document.querySelectorAll('#message-table tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
        });

        /* Display form */
        document.getElementById('add-message-btn').addEventListener('click', function() {
        const formSection = document.getElementById('add-message-form-section');
        formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
        });
    </script>
    <script src="../../actions/script.js"></script>
</body>
</html>