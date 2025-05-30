<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "myshop");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion."]);
    exit();
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont requis."]);
    exit();
}

// Hash du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Utilisateur ajouté."]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout."]);
}

$stmt->close();
$conn->close();
?>
 