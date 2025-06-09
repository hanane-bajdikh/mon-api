<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Récupérer les données
$nom = $_GET['nom'] ?? null;
$prenom = $_GET['prenom'] ?? null;
$email = $_GET['email'] ?? null;
$password = $_GET['password'] ?? null;

if (!$nom || !$prenom || !$email || !$password) {
    echo json_encode(['error' => 'Tous les champs sont requis']);
    exit;
}

// Vérifier si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'Cet email est déjà utilisé']);
    exit;
}

// Hasher le mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insérer le nouvel utilisateur
$stmt = $pdo->prepare("
    INSERT INTO utilisateur (nom, prenom, email, mdp, idRole, valider) 
    VALUES (?, ?, ?, ?, 2, 1)
");

try {
    $result = $stmt->execute([$nom, $prenom, $email, $hashedPassword]);
    
    if ($result) {
        $userId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'user_id' => $userId
        ]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la création']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>