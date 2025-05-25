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
        // Affiche l'utilisateur pour déboguer
        echo "<pre>"; print_r($user); echo "</pre>"; 

        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.html");
            exit();
        } else {
            echo "<p style='color:red'>Mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color:red'>Utilisateur non trouvé.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../public/login.css">
    <link rel="icon" type="image/png" href="../img/logo_fc.png" />
    <!-- Boxicons CSS -->
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <section class="container forms">
        <div class="form login">
            <div class="form-content">
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

        <!-- Sign Form -->

        <div class="form signup">
            <div class="form-content">
                <header>Signup</header>

                <form action="post">
                    <div class="field input-field">
                        <input type="email" placeholder="Email" class="input" required>
                    </div>

                    <div class="field input-field">
                        <input type="password" placeholder="Create password" class="password" required>
                    </div>

                    <div class="field input-field">
                        <input type="password" placeholder="Confirm password" class="password" required>
                        <i class='bx bx-hide eye-icon'></i>
                    </div>

                    <div class="field button-field">
                        <button>Signup</button>
                    </div>

                    <div class="form-link">
                        <span>Already have an account? <a href="#" class="link login-link">Login</a></span>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <script src="../actions/login.js"></script>
</body>
</html>