<?php
$host = "localhost"; // changer en 192.168.1.30
$dbname = "stock_management";
$username = "root"; // infochasuser
$password = ""; // 123456

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
