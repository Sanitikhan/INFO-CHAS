<?php
include '../includes/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            header('Location: admin/dashboard.php');
            exit();
        } else {
            $_SESSION['flash_message'] = "Mot de passe incorrect.";
            $_SESSION['flash_type'] = "error";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['flash_message'] = "Utilisateur non trouvé.";
        $_SESSION['flash_type'] = "error";
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../public/loginpage.css">
    <link rel="stylesheet" href="../public/flashmessage.css">
    <link rel="icon" type="image/png" href="../img/logo_w.png" />
    <!-- Boxicons CSS -->
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash-message <?= isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'error' ? 'flash-error' : '' ?>" id="flash-message">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <section class="container forms">
        <div class="form login">
            <div class="form-content">
                <section class="logo-section">
                    <img src="../img/logo_b.png" alt="Logo" class="logo">
                </section>
                <header>Connexion</header>

                <form method="post">
                    <div class="field input-field">
                        <input type="email" name="email" placeholder="Email" class="input" required>
                    </div>

                    <div class="field input-field">
                        <input type="password" name="password" placeholder="Mot de passe" class="password" required>
                        <i class='bx bx-hide eye-icon'></i>
                    </div>

                    <div class="form-link">
                        <a href="#" class="forgot-pass">Mot de passe oublié?</a>
                    </div>

                    <div class="field button-field">
                        <button type="submit">Connexion</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <script src="../actions/login.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flash = document.getElementById('flash-message');
            if (flash) {
                setTimeout(() => {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>