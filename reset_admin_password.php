<?php
include 'includes/config.php'; // Assure-toi que le chemin est correct

// Nouveau mot de passe haché
$new_password = password_hash('admin123', PASSWORD_DEFAULT);

// Mise à jour du mot de passe de l'admin
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@example.com'");
$stmt->execute([$new_password]);

echo "Mot de passe de l'admin mis à jour avec succès !";
?>
