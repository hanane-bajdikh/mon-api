<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Récupération des données envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérification des champs requis
if (!isset($data["product_id"], $data["new_stock"])) {
    echo json_encode(["success" => false, "message" => "Champs manquants"]);
    exit;
}

$product_id = intval($data["product_id"]);
$new_stock = intval($data["new_stock"]);

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "myshop");

if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion BDD"]);
    exit;
}

// Mise à jour du stock
$stmt = $mysqli->prepare("UPDATE products SET stock = ? WHERE id = ?");
$stmt->bind_param("ii", $new_stock, $product_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Stock mis à jour"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour"]);
}

$stmt->close();
$mysqli->close();
?>
