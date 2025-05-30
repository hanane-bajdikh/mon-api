<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "", "myshop");

if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion BDD"]);
    exit;
}

$result = $mysqli->query("SELECT * FROM orders ORDER BY id DESC");

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
$mysqli->close();
?>
