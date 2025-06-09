<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

$produitId = $_GET['produit_id'] ?? null;

if (!$produitId) {
    echo json_encode(['error' => 'ID du produit requis']);
    exit;
}

try {
    // Vérifier si le produit existe
    $stmt = $pdo->prepare("SELECT designation FROM produit WHERE id = ?");
    $stmt->execute([$produitId]);
    $produit = $stmt->fetch();
    
    if (!$produit) {
        echo json_encode(['error' => 'Produit non trouvé']);
        exit;
    }
    
    // Supprimer d'abord les relations dans composer (si elles existent)
    $stmt = $pdo->prepare("DELETE FROM composer WHERE idProduit = ?");
    $stmt->execute([$produitId]);
    
    // Supprimer le produit
    $stmt = $pdo->prepare("DELETE FROM produit WHERE id = ?");
    $result = $stmt->execute([$produitId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Produit supprimé avec succès',
            'nom_produit' => $produit['designation']
        ]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la suppression']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>