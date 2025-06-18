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
            // After successful authentication
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect all users to the admin dashboard
            header('Location: admin/dashboard.php');
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
    <link rel="stylesheet" href="../public/loginpage.css">
    <link rel="icon" type="image/png" href="../img/logo_w.png" />
    <!-- Boxicons CSS -->
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
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
</body>
</html>