<?php
require_once '../includes/config.php';

try {
    $stmt = $pdo->query("SELECT * FROM fournisseurs");
    $fournisseurs = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $fournisseurs = [];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/modal.css">
    <title>Paramètres</title>
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
                            <a href="#" class="nav-link dropdown-link">Mes messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Mes alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link dropdown-link">Fournisseurs</a>
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
                    <a href="#" class="nav-link active">
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
        <h1>Bienvenue dans les paramètres</h1>
        <form class="form-fournisseur" method="POST" action="../actions/ajouter_fournisseur.php">
            <input type="text" name="nom" placeholder="Nom du fournisseur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="telephone" placeholder="Téléphone" required>
            <button type="submit">Ajouter le fournisseur</button>
        </form>

        <!-- Affichage des fournisseurs existants -->
        <h2>Fournisseurs enregistrés</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
            <?php foreach ($fournisseurs as $fournisseur): ?>
                <tr>
                    <td><?= htmlspecialchars($fournisseur['id']) ?></td>
                    <td><?= htmlspecialchars($fournisseur['nom']) ?></td>
                    <td><?= htmlspecialchars($fournisseur['email']) ?></td>
                    <td><?= htmlspecialchars($fournisseur['telephone']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="wrapper">
            <!-- Modal de succès -->
            <div id="modal-success" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('modal-success')">&times;</span>
                    <p>Fournisseur ajouté avec succès !</p>
                </div>
            </div>

            <!-- Modal d'erreur -->
            <div id="modal-error" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('modal-error')">&times;</span>
                    <p>Erreur lors de l'ajout du fournisseur.</p>
                </div>
            </div>
        </div>
        
    </section>

    

    



    <script src="../actions/script.js"></script>
    <!--<script src="../actions/modal.js"></script>-->
</body>
</html>