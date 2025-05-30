<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Connexion à la base
require_once 'db.php';

// Récupération des données
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Champs requis manquants."]);
    exit;
}

// Vérifie si l'email existe déjà
$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    echo json_encode(["success" => false, "message" => "Cet email est déjà utilisé."]);
    exit;
}

// Hash du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base
$stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (:email, :password)");
$success = $stmt->execute([
    'email' => $email,
    'password' => $hashedPassword
]);

if ($success) {
    echo json_encode(["success" => true, "message" => "Administrateur ajouté avec succès."]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout."]);
}
?>
