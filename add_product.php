<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "myshop");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit();
}

// Récupération des données envoyées
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$stock = $_POST['stock'] ?? '';
$category = $_POST['category'] ?? '';

// Validation simple
if (empty($name) || empty($price) || empty($stock) || empty($category)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires."]);
    exit();
}

// Requête SQL pour insérer le produit
$stmt = $conn->prepare("INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdis", $name, $price, $stock, $category);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Produit ajouté avec succès."]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du produit."]);
}

$stmt->close();
$conn->close();
?>
