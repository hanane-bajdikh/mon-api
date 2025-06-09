<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");

include 'db.php';

// Gérer preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Récupérer les données
$productId = null;
$newStock = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);
    
    if ($data) {
        $productId = $data['product_id'] ?? null;
        $newStock = $data['new_stock'] ?? null;
    }
} else {
    // GET pour test
    $productId = $_GET['product_id'] ?? null;
    $newStock = $_GET['new_stock'] ?? null;
}

// Debug
error_log("Product ID: " . $productId);
error_log("New Stock: " . $newStock);

if ($productId === null || $newStock === null) {
    echo json_encode([
        'error' => 'ID produit et nouveau stock requis',
        'received_product_id' => $productId,
        'received_new_stock' => $newStock
    ]);
    exit;
}

$productId = (int)$productId;
$newStock = (int)$newStock;

if ($newStock < 0) {
    echo json_encode(['error' => 'Le stock ne peut pas être négatif']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE produit SET stock = ? WHERE id = ?");
    $result = $stmt->execute([$newStock, $productId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Stock mis à jour avec succès',
            'product_id' => $productId,
            'new_stock' => $newStock
        ]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la mise à jour en base']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>