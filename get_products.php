<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Tous les produits avec leurs statistiques de vente
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.designation,
        p.description,
        p.prix,
        p.stock,
        p.categorie,
        p.image,
        COALESCE(SUM(co.quantite), 0) as total_sold,
        COALESCE(SUM(co.quantite * p.prix), 0) as total_revenue,
        COUNT(DISTINCT c.id) as orders_count
    FROM produit p
    LEFT JOIN composer co ON p.id = co.idProduit
    LEFT JOIN commande c ON co.idCommande = c.id AND c.statut = 'livré'
    GROUP BY p.id, p.designation, p.description, p.prix, p.stock, p.categorie, p.image
    ORDER BY p.designation ASC
");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Top 5 produits les mieux vendus
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.designation,
        p.prix,
        p.categorie,
        SUM(co.quantite) as total_sold,
        SUM(co.quantite * p.prix) as revenue
    FROM produit p
    JOIN composer co ON p.id = co.idProduit
    JOIN commande c ON co.idCommande = c.id
    WHERE c.statut = 'livré'
    GROUP BY p.id, p.designation, p.prix, p.categorie
    ORDER BY total_sold DESC
    LIMIT 5
");
$stmt->execute();
$meilleursVentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Produits les moins vendus (ou jamais vendus)
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.designation,
        p.prix,
        p.categorie,
        p.stock,
        COALESCE(SUM(co.quantite), 0) as total_sold
    FROM produit p
    LEFT JOIN composer co ON p.id = co.idProduit
    LEFT JOIN commande c ON co.idCommande = c.id AND c.statut = 'livré'
    GROUP BY p.id, p.designation, p.prix, p.categorie, p.stock
    ORDER BY total_sold ASC, p.stock DESC
    LIMIT 5
");
$stmt->execute();
$moinsVendus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques générales
$stmt = $pdo->prepare("SELECT COUNT(*) as total_produits FROM produit");
$stmt->execute();
$totalProduits = $stmt->fetch(PDO::FETCH_ASSOC)['total_produits'];

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT categorie) as total_categories FROM produit");
$stmt->execute();
$totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['total_categories'];

$stmt = $pdo->prepare("SELECT SUM(stock) as total_stock FROM produit");
$stmt->execute();
$totalStock = $stmt->fetch(PDO::FETCH_ASSOC)['total_stock'];

$stmt = $pdo->prepare("SELECT AVG(prix) as prix_moyen FROM produit");
$stmt->execute();
$prixMoyen = $stmt->fetch(PDO::FETCH_ASSOC)['prix_moyen'];

echo json_encode([
    'produits' => $produits,
    'meilleures_ventes' => $meilleursVentes,
    'moins_vendus' => $moinsVendus,
    'stats' => [
        'total_produits' => (int)$totalProduits,
        'total_categories' => (int)$totalCategories,
        'total_stock' => (int)$totalStock,
        'prix_moyen' => (float)$prixMoyen
    ]
]);
?>