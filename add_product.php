<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

$designation = $_GET['designation'] ?? null;
$description = $_GET['description'] ?? null;
$prix = $_GET['prix'] ?? null;
$stock = $_GET['stock'] ?? null;
$categorie = $_GET['categorie'] ?? null;

if (!$designation || !$prix || !$stock || !$categorie) {
    echo json_encode(['error' => 'Désignation, prix, stock et catégorie sont requis']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO produit (designation, description, prix, stock, categorie) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $designation,
        $description ?: '',
        (float)$prix,
        (int)$stock,
        $categorie
    ]);
    
    if ($result) {
        $produitId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'Produit créé avec succès',
            'produit_id' => $produitId
        ]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la création du produit']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>