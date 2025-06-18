<?php
require_once '../includes/config.php';
session_start();

if (
    isset($_POST['numero_livraison'], $_POST['fournisseur_id'], $_POST['date_prevue'], $_POST['statut'])
) {
    try {
        $livreur_id = $_POST['livreur_id'];

        $stmt = $pdo->prepare("
            INSERT INTO livraisons (
                numero_livraison,
                fournisseur_id,
                date_prevue,
                date_livraison,
                statut,
                transporteur,
                notes,
                created_by,
                livreur_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([
            $_POST['numero_livraison'],      // numero_livraison
            $_POST['fournisseur_id'],        // fournisseur_id
            $_POST['date_prevue'],           // date_prevue
            $_POST['date_livraison'],        // date_livraison
            $_POST['statut'],                // statut
            $livreur_id,                     // transporteur (store the ID)
            $_POST['notes'] ?? null,         // notes (optional)
            $_SESSION['user_id'] ?? null,    // created_by (from session)
            $livreur_id                       // livreur_id (associate with transporteur)
        ])) {
            $_SESSION['flash_message'] = "Livraison ajoutée avec succès !";
            header('Location: ../pages/admin/livraisons.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Champs obligatoires manquants.";
}
?>
