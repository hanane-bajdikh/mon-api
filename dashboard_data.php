<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Compter les commandes en attente
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM commande WHERE statut = 'en attente'");
$stmt->execute();
$pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Récupérer les produits en stock faible (stock < 10)
$stmt = $pdo->prepare("SELECT designation, stock FROM produit WHERE stock < 10");
$stmt->execute();
$lowStockResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir pour Flutter
$lowStock = [];
foreach($lowStockResults as $row) {
    $lowStock[] = [
        "nom" => $row['designation'],
        "quantite" => (int)$row['stock']
    ];
}

// Calculer le total des ventes (commandes livrées)
$stmt = $pdo->prepare("SELECT SUM(total) AS total_sales FROM commande WHERE statut = 'livré'");
$stmt->execute();
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

// Compter les utilisateurs
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM utilisateur");
$stmt->execute();
$userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo json_encode([
    "success" => true,
    "pending_orders" => (int)$pendingOrders,
    "low_stock" => $lowStock,
    "total_sales" => (float)$totalSales,
    "user_count" => (int)$userCount,
    "stats" => [5, 12, 18, 8, 15, 10]
]);
?>