<?php
// Paramètres de connexion à ta base de données MySQL
$host = "localhost";
$db_name = "myshop";            // Remplace par le nom exact de ta base
$username = "root";             // L'utilisateur par défaut de WAMP/XAMPP
$password = "";                 // Mot de passe vide pour root en local (WAMP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}
?>
