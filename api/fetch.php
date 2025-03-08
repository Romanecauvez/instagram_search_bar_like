<?php
require_once '../includes/db.php';

$api_key = $env["RAPID_API_KEY"];
$api_host = $env["RAPID_API_HOST"];

header('Content-Type: application/json');

// Vérifier que le nom d'utilisateur est fourni
$username = htmlspecialchars($_GET['username']);

// Configuration de l'API
$api_url = "https://" . $api_host . "/v1/posts?username_or_id_or_url=" . urlencode($username);

// Initialiser cURL
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
    echo json_encode(['success' => false, 'message' => 'Erreur cURL: ' . $err]);
    exit;
}

// Décoder la réponse JSON
$data = json_decode($response, true);

// Vérifier si la réponse est valide
if (!$data || !isset($data['data'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides reçues de l\'API']);
    exit;
}

// Extraction des posts
$posts = [];
foreach ($data['data']['items'] as $item) {
    // Vérifier si le caption et created_at sont disponibles
    if (isset($item['caption']['created_at']) && isset($item['caption']['text'])) {
        $posts[] = [
            'timestamp' => $item['caption']['created_at'],  // Timestamp Unix
            'caption' => $item['caption']['text']           // Texte de la légende
        ];
    }
}

// Enregistrer la recherche dans la base de données
$search_id = saveSearch($pdo, $username);

// Enregistrer les posts dans la base de données
if ($search_id) {
    savePosts($pdo, $search_id, $posts);
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
    $stmt->bindValue(1, $username, PDO::PARAM_STR); // Utilisation de bindValue pour PDO
    
    if ($stmt->execute()) {
        return $pdo->lastInsertId();  // Utilisation de lastInsertId() avec PDO pour récupérer l'ID
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
