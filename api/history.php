<?php
require_once '../includes/db.php'; // Inclure la connexion à la base de données

header('Content-Type: application/json');

try {
    // Récupérer l'historique des recherches
    $query = "SELECT id, username, search_date FROM searches ORDER BY search_date DESC";
    $stmt = $pdo->query($query);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Renvoyer les résultats au format JSON
    echo json_encode([
        'success' => true,
        'history' => $history,
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, renvoyer un message d'erreur
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération de l\'historique : ' . $e->getMessage(),
    ]);
}
