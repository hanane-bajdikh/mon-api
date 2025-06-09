<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

// Récupérer toutes les commandes avec les infos utilisateur
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.total,
        c.montant,
        c.statut,
        c.date,
        c.idUtilisateur,
        u.nom,
        u.prenom,
        u.email
    FROM commande c
    LEFT JOIN utilisateur u ON c.idUtilisateur = u.id
    ORDER BY c.id DESC
");

$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formater pour Flutter
$result = [];
foreach ($commandes as $commande) {
    $clientName = 'Client invité';
    if ($commande['nom'] && $commande['prenom']) {
        $clientName = $commande['prenom'] . ' ' . $commande['nom'];
    } elseif ($commande['email']) {
        $clientName = $commande['email'];
    }
    
    $totalAmount = $commande['total'] ?? $commande['montant'] ?? 0;
    
    $result[] = [
        'id' => (int)$commande['id'],
        'total' => (float)$totalAmount,
        'status' => $commande['statut'] ?? 'en attente',
        'date' => $commande['date'] ?? date('Y-m-d H:i:s'),
        'client_name' => $clientName,
        'client_email' => $commande['email'] ?? ''
    ];
}

echo json_encode($result);
?>