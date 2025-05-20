<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];

    $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom, email, telephone) VALUES (:nom, :email, :telephone)");

    if ($stmt->execute([
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone
        ])) {
            echo "Fournisseur ajouté avec succès.";
            http_response_code(200);
            error_log("Fournisseur ajouté : " . $nom); // Log pour vérification
        } else {
            echo "Erreur lors de l'ajout du fournisseur.";
            http_response_code(500);
            error_log("Erreur lors de l'ajout du fournisseur: " . print_r($stmt->errorInfo(), true)); // Log de l'erreur SQL
        }
    exit(); // Important: Arrêter l'exécution du script PHP ici pour ne rien renvoyer d'autre
} else {
    http_response_code(400); // Mauvaise requête si on accède directement au fichier
    echo "Requête invalide.";
    exit();
}
?>