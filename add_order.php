<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["customer_name"], $data["customer_email"], $data["total"])) {
    echo json_encode(["success" => false, "message" => "Champs manquants"]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "myshop");

if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion BDD"]);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO orders (customer_name, customer_email, total) VALUES (?, ?, ?)");
$stmt->bind_param("ssd", $data["customer_name"], $data["customer_email"], $data["total"]);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Commande ajoutée"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout"]);
}

$stmt->close();
$mysqli->close();
?>
