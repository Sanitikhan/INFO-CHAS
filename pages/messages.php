<?php
require_once '../includes/config.php';

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
        JOIN users u ON m.sender_id = u.id 
        WHERE m.receiver_id = ? 
        ORDER BY m.sent_at DESC");
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
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/messages.css">
    <link rel="icon" href="../img/logo_fc.png" type="image/png">
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
                <img src="../img/logo_fc.png" alt="FASHION CHIC">
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
                    <a href="index.html" class="nav-link">
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
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="nav-label">Réapprovisionnement</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Réapprovisionnement</a>
                        </li>
                    </ul>
                </li>
                <!-- Dropdown -->
                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">local_shipping</span>
                        <span class="nav-label">Livraisons</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Livraisons</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Toutes les livraisons</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Mes livraisons</a>
                        </li>
                    </ul>
                </li>
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
                            <a href="#" class="nav-link dropdown-link">Mes alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="fournisseurs.php" class="nav-link dropdown-link">Fournisseurs</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
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
            <h1>MES MESSAGES</h1>
        </header>
        
        <div class="messages-container">
                <!-- Send Message Form -->
                <form action="../actions/messagerie.php" method="POST">
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
                    <label for="corps">Message :</label>
                    <textarea name="corps" required></textarea>
                    <button type="submit" name="send_message">Envoyer</button>
                </form>
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

    <script src="../actions/script.js"></script>
</body>
</html>