<?php
require_once '../includes/db.php';

$api_key = $env["RAPID_API_KEY"];
$api_host = $env["RAPID_API_HOST"];

header('Content-Type: application/json');

// Vérifier que le nom d'utilisateur est fourni
$username = htmlspecialchars($_GET['username']);

// Fonction pour rechercher les posts dans la base de données
function getPostsFromDB($pdo, $username) {
    $query = "SELECT p.post_date, p.caption 
              FROM posts p 
              JOIN searches s ON p.search_id = s.id 
              WHERE s.username = ? 
              ORDER BY p.post_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour faire la requête à l'API
function getPostsFromAPI($username, $api_host, $api_key) {
    $api_url = "https://" . $api_host . "/v1/posts?username_or_id_or_url=" . urlencode($username);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: " . $api_host,
            "x-rapidapi-key: " . $api_key,
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['success' => false, 'message' => 'Erreur cURL: ' . $err];
    }

    $data = json_decode($response, true);

    if (!$data || !isset($data['data'])) {
        return ['success' => false, 'message' => 'Données invalides reçues de l\'API'];
    }

    $posts = [];
    foreach ($data['data']['items'] as $item) {
        if (isset($item['caption']['created_at']) && isset($item['caption']['text'])) {
            $posts[] = [
                'timestamp' => $item['caption']['created_at'],
                'caption' => $item['caption']['text']
            ];
        }
    }

    return $posts;
}

// Vérifier si les posts existent déjà dans la base de données
$posts = getPostsFromDB($pdo, $username);

if (empty($posts)) {
    // Si les posts ne sont pas dans la base de données, faire la requête à l'API
    $apiResponse = getPostsFromAPI($username, $api_host, $api_key);

    if (isset($apiResponse['success']) && !$apiResponse['success']) {
        echo json_encode($apiResponse);
        exit;
    }

    $posts = $apiResponse;

    // Enregistrer la recherche dans la base de données
    $search_id = saveSearch($pdo, $username);

    // Enregistrer les posts dans la base de données
    if ($search_id) {
        savePosts($pdo, $search_id, $posts);
    }
}

// Retourner les résultats
echo json_encode([
    'success' => true,
    'username' => $username,
    'posts' => $posts
]);

// Fonction pour enregistrer la recherche
function saveSearch($pdo, $username) {
    $query = "INSERT INTO searches (username, search_date) VALUES (?, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(1, $username, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        return $pdo->lastInsertId();
    }
    
    return false;
}

// Fonction pour enregistrer les posts
function savePosts($pdo, $search_id, $posts) {
    $query = "INSERT INTO posts (search_id, post_date, caption) VALUES (?, FROM_UNIXTIME(?), ?)";
    $stmt = $pdo->prepare($query);
    
    foreach ($posts as $post) {
        $stmt->bindValue(1, $search_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $post['timestamp'], PDO::PARAM_INT);
        $stmt->bindValue(3, $post['caption'], PDO::PARAM_STR);
        $stmt->execute();
    }
}
?>
