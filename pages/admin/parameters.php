<?php
session_start();
require_once '../../includes/config.php';

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../login.php');
    exit();
}*/

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$email = $user ? $user['email'] : null;

$all_users_stmt = $pdo->query("SELECT id, username, email, role FROM users");
$all_users = $all_users_stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // <-- Fix here

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $password, $role])) {
        $_SESSION['success'] = "Inscription réussie, vous pouvez vous connecter.";
        header("Location: parameters.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de l'inscription.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/parameters.css">
    <link rel="stylesheet" href="../../public/modal2.css">
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
                    <a href="parameters.php" class="nav-link active">
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
            <h1>PARAMETRES</h1>
        </header>

        <!-- Mes informations + Ajouter un nouvel utilisateur -->
        <section class="section-content">
            <div class="container" style="flex: 1;">
                <h2>Mes informations</h2>
                <div class="user-info">
                    <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p><strong>Email :</strong> <?= $email ? htmlspecialchars($email) : '<em>Non défini</em>' ?></p>
                    <p><strong>Mot de passe :</strong> ********</p>
                </div>
                <button class="btn btn-edit" id="openEditUtilisateur">Modifier</button>
            </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <button id="openAddUtilisateur" class="btn">Ajouter un nouvel utilisateur</button>
            <?php endif; ?>
            
            <div id="modal-utilisateur" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center;">
                <div class="modal-content" style="padding:20px; border-radius:8px; max-width:500px; width:90%; background: #fff;">

                    <button id="closeUtilisateur" style="float:right;">X</button>

                    <h2>Ajouter un nouvel utilisateur</h2>
                    <?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); } ?>
                    <button type="button" id="close-user-form-btn" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:1.5em; cursor:pointer; color:#000;">&times;</button>
                    <form method="post">
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur :</label>
                            <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
                        </div>
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Rôle :</label>
                            <select name="role" required>
                                <option value="admin">Administrateur</option>
                                <option value="commercial">Commercial</option>
                                <option value="gestionnaire de stock">Gestionnaire de stock</option>
                                <option value="magasinier">Magasinier</option>
                                <option value="gestionnaire de livraison">Gestionnaire de livraison</option>
                                <option value="livreur">Livreur</option>
                                <option value="fournisseur">Fournisseur</option>
                            </select>
                        <div class="form-group">
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

            <div id="modal-modifier" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center;">
                <div class="modal-content" style="padding:20px; border-radius:8px; max-width:500px; width:90%; background: #fff;">

                    <button id="closeModifier" style="float:right;">X</button>
                        <h2>Modifier mes informations</h2>
                        <form action="../actions/update_user.php" method="POST" style="background: none; box-shadow: none; padding: 0;">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur :</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email :</label>
                                <input type="email" id="email" name="email" value="<?= $email ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe :</label>
                                <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe :</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn">Mettre à jour</button>
                            </div>
                        </form>
                </div>
            </div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <section class="section-content">
            <div class="container">
                <h2>Liste des utilisateurs</h2>
                <table border="1"  cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                            <tr class="user-row"
                                data-id="<?= htmlspecialchars($user['id']) ?>"
                                data-username="<?= htmlspecialchars($user['username']) ?>"
                                data-email="<?= htmlspecialchars($user['email']) ?>"
                                data-role="<?= htmlspecialchars($user['role']) ?>">
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <!-- Edit User Modal/Form -->
<div id="edit-user-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:2em; border-radius:10px; min-width:300px; position:relative;">
        <button onclick="document.getElementById('edit-user-modal').style.display='none'" style="position:absolute;top:10px;right:10px;">&times;</button>
        <h3>Modifier l'utilisateur</h3>
        <form id="edit-user-form" method="post" action="../actions/update_user.php">
            <input type="hidden" name="user_id" id="edit-user-id">
            <div class="form-group">
                <label for="edit-username">Nom d'utilisateur :</label>
                <input type="text" name="username" id="edit-username" required>
            </div>
            <div class="form-group">
                <label for="edit-email">Email :</label>
                <input type="email" name="email" id="edit-email" required>
            </div>
            <div class="form-group">
                <label for="edit-role">Rôle :</label>
                <select name="role" id="edit-role" required>
                    <option value="commercial">Commercial</option>
                    <option value="admin">Administrateur</option>
                    <option value="gestionnaire de stock">Gestionnaire de stock</option>
                    <option value="magasinier">Magasinier</option>
                    <option value="gestionnaire de livraison">Gestionnaire de livraison</option>
                    <option value="livreur">Livreur</option>
                    <option value="fournisseur">Fournisseur</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showBtn = document.getElementById('show-user-form-btn');
            const formSection = document.getElementById('user-form-section');
            const closeBtn = document.getElementById('close-user-form-btn');

            showBtn.addEventListener('click', function() {
                formSection.style.display = 'block';
                showBtn.style.display = 'none';
            });

            closeBtn.addEventListener('click', function() {
                formSection.style.display = 'none';
                showBtn.style.display = 'inline-block'; // or 'block' depending on your layout
            });
        });

        document.getElementById('edit-infos-btn').addEventListener('click', function() {
        const formSection = document.getElementById('edit-infos-form-section');
        formSection.style.display = (formSection.style.display === 'none' || formSection.style.display === '') ? 'block' : 'none';
        });
    </script>
    <script src="../../actions/script.js"></script>
    <script>
        document.querySelectorAll('.user-row').forEach(function(row) {
            row.addEventListener('click', function() {
                document.getElementById('edit-user-id').value = row.dataset.id;
                document.getElementById('edit-username').value = row.dataset.username;
                document.getElementById('edit-email').value = row.dataset.email;
                document.getElementById('edit-role').value = row.dataset.role;
                document.getElementById('edit-user-modal').style.display = 'flex';
            });
        });

            const openBtn = document.getElementById('openAddUtilisateur');
            const modal = document.getElementById('modal-utilisateur');
            const closeBtn = document.getElementById('closeUtilisateur');
            const openUserBtn = document.getElementById('openEditUtilisateur');
            const modalModifier = document.getElementById('modal-modifier');
            const closeModifierBtn = document.getElementById('closeModifier');

            openBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
            openUserBtn.addEventListener('click', () => {
                modalModifier.style.display = 'flex';
            });

            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            closeModifierBtn.addEventListener('click', () => {
                modalModifier.style.display = 'none';
            });

            window.addEventListener('click', e => {
                if (e.target === modal) modal.style.display = 'none';
            });
            window.addEventListener('click', e => {
                if (e.target === modalModifier) modalModifier.style.display = 'none';
            });

            // Fermer si clic en dehors de la modal-content
            window.addEventListener('click', e => {
            const modal = document.getElementById('modal-utilisateur');
            const modalModifier = document.getElementById('modal-modifier');
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
</script>
</body>
</html>