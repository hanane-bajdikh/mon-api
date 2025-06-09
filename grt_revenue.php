<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Revenus par mois (derniers 12 mois)
$stmt = $pdo->prepare("
    SELECT 
        YEAR(date) as year,
        MONTH(date) as month,
        SUM(total) as revenue,
        COUNT(*) as orders_count
    FROM commande 
    WHERE statut = 'livré' 
    AND date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(date), MONTH(date)
    ORDER BY YEAR(date), MONTH(date)
");
$stmt->execute();
$monthlyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Revenus par jour (derniers 30 jours)
$stmt = $pdo->prepare("
    SELECT 
        DATE(date) as revenue_date,
        SUM(total) as daily_revenue,
        COUNT(*) as daily_orders
    FROM commande 
    WHERE statut = 'livré' 
    AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(date)
    ORDER BY DATE(date)
");
$stmt->execute();
$dailyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Revenus par catégorie
$stmt = $pdo->prepare("
    SELECT 
        p.categorie,
        SUM(co.quantite * p.prix) as category_revenue,
        SUM(co.quantite) as items_sold
    FROM composer co
    JOIN produit p ON co.idProduit = p.id
    JOIN commande c ON co.idCommande = c.id
    WHERE c.statut = 'livré'
    GROUP BY p.categorie
    ORDER BY category_revenue DESC
");
$stmt->execute();
$categoryRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats générales
$stmt = $pdo->prepare("SELECT SUM(total) as total_revenue FROM commande WHERE statut = 'livré'");
$stmt->execute();
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM commande WHERE statut = 'livré'");
$stmt->execute();
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

$stmt = $pdo->prepare("SELECT AVG(total) as avg_order FROM commande WHERE statut = 'livré'");
$stmt->execute();
$avgOrder = $stmt->fetch(PDO::FETCH_ASSOC)['avg_order'] ?? 0;

// Revenus du mois en cours
$stmt = $pdo->prepare("
    SELECT SUM(total) as current_month_revenue 
    FROM commande 
    WHERE statut = 'livré' 
    AND YEAR(date) = YEAR(NOW()) 
    AND MONTH(date) = MONTH(NOW())
");
$stmt->execute();
$currentMonthRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['current_month_revenue'] ?? 0;

echo json_encode([
    'monthly_revenue' => $monthlyRevenue,
    'daily_revenue' => $dailyRevenue,
    'category_revenue' => $categoryRevenue,
    'stats' => [
        'total_revenue' => (float)$totalRevenue,
        'total_orders' => (int)$totalOrders,
        'avg_order' => (float)$avgOrder,
        'current_month_revenue' => (float)$currentMonthRevenue
    ]
]);
?>