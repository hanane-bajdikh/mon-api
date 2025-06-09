<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Ventes par mois (derniers 12 mois) - TES VRAIES DONNÉES
$stmt = $pdo->prepare("
    SELECT 
        YEAR(date) as year,
        MONTH(date) as month,
        SUM(total) as monthly_sales,
        COUNT(*) as monthly_orders
    FROM commande 
    WHERE statut = 'livré' 
    AND date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(date), MONTH(date)
    ORDER BY YEAR(date), MONTH(date)
");
$stmt->execute();
$monthlySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ventes par semaine (dernières 8 semaines) - TES VRAIES DONNÉES
$stmt = $pdo->prepare("
    SELECT 
        YEAR(date) as year,
        WEEK(date) as week,
        SUM(total) as weekly_sales,
        COUNT(*) as weekly_orders
    FROM commande 
    WHERE statut = 'livré' 
    AND date >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
    GROUP BY YEAR(date), WEEK(date)
    ORDER BY YEAR(date), WEEK(date)
");
$stmt->execute();
$weeklySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ventes par jour (derniers 30 jours) - TES VRAIES DONNÉES
$stmt = $pdo->prepare("
    SELECT 
        DATE(date) as sale_date,
        SUM(total) as daily_sales,
        COUNT(*) as daily_orders
    FROM commande 
    WHERE statut = 'livré' 
    AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(date)
    ORDER BY DATE(date)
");
$stmt->execute();
$dailySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats générales - TES VRAIES DONNÉES
$stmt = $pdo->prepare("SELECT SUM(total) as total_sales FROM commande WHERE statut = 'livré'");
$stmt->execute();
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM commande WHERE statut = 'livré'");
$stmt->execute();
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

$averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

echo json_encode([
    'monthly_sales' => $monthlySales,
    'weekly_sales' => $weeklySales,
    'daily_sales' => $dailySales,
    'total_sales' => (float)$totalSales,
    'total_orders' => (int)$totalOrders,
    'average_order' => (float)$averageOrder
]);
?>