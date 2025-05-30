<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'myshop'; // 👉 change selon le nom réel de ta base
$username = 'root';
$password = ''; // 👉 change si tu as mis un mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données"]);
    exit();
}

// Requête pour récupérer les produits
$sql = "SELECT * FROM produit"; // 👉 adapte si ta table a un autre nom
$stmt = $pdo->prepare($sql);
$stmt->execute();

$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Réponse JSON
header('Content-Type: application/json');
echo json_encode($produits);
