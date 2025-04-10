<?php
include '../includes/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'commercial'; // Par défaut

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $password, $role])) {
        $_SESSION['success'] = "Inscription réussie, vous pouvez vous connecter.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de l'inscription.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); } ?>
    <form method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>
    <a href="login.php">Déjà inscrit ? Connectez-vous</a>
</body>
</html>
