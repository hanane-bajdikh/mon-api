<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

$pendingResult = $mysqli->query("SELECT COUNT(*) AS total FROM commandes WHERE statut = 'en attente'");
$pendingOrders = $pendingResult->fetch_assoc()['total'] ?? 0;

$lowStockResult = $mysqli->query("SELECT nom, quantite FROM produits WHERE quantite < 10");
$lowStock = [];
while ($row = $lowStockResult->fetch_assoc()) {
    $lowStock[] = $row;
}

// Statistiques de vente fictives (à remplacer plus tard par des vraies données)
$stats = [5, 12, 18, 8, 15, 10];

echo json_encode([
    "success" => true,
    "pendingOrders" => $pendingOrders,
    "lowStock" => $lowStock,
    "stats" => $stats
]);
