<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Tous les utilisateurs avec leurs statistiques
$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.nom,
        u.prenom,
        u.email,
        u.idRole,
        u.valider,
        r.libelle as role_name,
        COUNT(c.id) as total_orders,
        SUM(CASE WHEN c.statut = 'livré' THEN c.total ELSE 0 END) as total_spent,
        MAX(c.date) as last_order_date,
        AVG(CASE WHEN c.statut = 'livré' THEN c.total ELSE NULL END) as avg_order_value
    FROM utilisateur u
    LEFT JOIN role r ON u.idRole = r.id
    LEFT JOIN commande c ON u.id = c.idUtilisateur
    GROUP BY u.id, u.nom, u.prenom, u.email, u.idRole, u.valider, r.libelle
    ORDER BY total_spent DESC, total_orders DESC
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques générales
$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM utilisateur WHERE idRole = 2"); // Clients seulement
$stmt->execute();
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt = $pdo->prepare("SELECT COUNT(*) as new_users FROM utilisateur WHERE idRole = 2 AND DATE(NOW()) - INTERVAL 30 DAY <= NOW()");
$stmt->execute();
$newUsers = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];

$stmt = $pdo->prepare("SELECT COUNT(*) as active_users FROM utilisateur u JOIN commande c ON u.id = c.idUtilisateur WHERE u.idRole = 2 AND c.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stmt->execute();
$activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];

// Top 5 utilisateurs les plus fidèles (par montant dépensé)
$topUsers = array_slice(array_filter($users, function($user) {
    return $user['idRole'] == 2 && $user['total_spent'] > 0; // Clients avec achats
}), 0, 5);

// Nouveaux utilisateurs (derniers 30 jours)
$stmt = $pdo->prepare("
    SELECT id, nom, prenom, email, DATE(NOW()) as registration_date
    FROM utilisateur 
    WHERE idRole = 2 
    ORDER BY id DESC 
    LIMIT 10
");
$stmt->execute();
$recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'users' => $users,
    'top_users' => $topUsers,
    'recent_users' => $recentUsers,
    'stats' => [
        'total_users' => (int)$totalUsers,
        'new_users' => (int)$newUsers,
        'active_users' => (int)$activeUsers
    ]
]);
?>