<?php
require_once '../includes/db.php'; // Inclure la connexion à la base de données

header('Content-Type: application/json');

// Vérifier que l'ID de la recherche est fourni
if (!isset($_GET['search_id']) || !is_numeric($_GET['search_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de recherche invalide.',
    ]);
    exit;
}

$search_id = (int)$_GET['search_id'];

try {
    // Récupérer les posts de la recherche spécifique
    $query = "SELECT post_date, caption FROM posts WHERE search_id = ? ORDER BY post_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$search_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Renvoyer les résultats au format JSON
    echo json_encode([
        'success' => true,
        'posts' => $posts,
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, renvoyer un message d'erreur
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des posts : ' . $e->getMessage(),
    ]);
}