<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Récupérer tous les produits avec leur stock
$stmt = $pdo->prepare("
    SELECT 
        id,
        designation,
        prix,
        stock,
        categorie,
        CASE 
            WHEN stock = 0 THEN 'rupture'
            WHEN stock <= 5 THEN 'critique'
            WHEN stock <= 10 THEN 'faible'
            ELSE 'normal'
        END as stock_status
    FROM produit 
    ORDER BY stock ASC, designation ASC
");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques des stocks
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produit");
$stmt->execute();
$totalProduits = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as rupture FROM produit WHERE stock = 0");
$stmt->execute();
$enRupture = $stmt->fetch(PDO::FETCH_ASSOC)['rupture'];

$stmt = $pdo->prepare("SELECT COUNT(*) as critique FROM produit WHERE stock <= 5 AND stock > 0");
$stmt->execute();
$stockCritique = $stmt->fetch(PDO::FETCH_ASSOC)['critique'];

$stmt = $pdo->prepare("SELECT COUNT(*) as faible FROM produit WHERE stock <= 10 AND stock > 5");
$stmt->execute();
$stockFaible = $stmt->fetch(PDO::FETCH_ASSOC)['faible'];

$stmt = $pdo->prepare("SELECT SUM(stock * prix) as valeur_stock FROM produit");
$stmt->execute();
$valeurStock = $stmt->fetch(PDO::FETCH_ASSOC)['valeur_stock'] ?? 0;

echo json_encode([
    'produits' => $produits,
    'stats' => [
        'total_produits' => (int)$totalProduits,
        'en_rupture' => (int)$enRupture,
        'stock_critique' => (int)$stockCritique,
        'stock_faible' => (int)$stockFaible,
        'valeur_stock' => (float)$valeurStock
    ]
]);
?>