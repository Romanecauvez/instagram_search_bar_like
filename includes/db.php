<?php
// Charger les variables d'environnement depuis le fichier .env
$env = parse_ini_file(__DIR__ . '/../.env');

// Paramètres de connexion à la base de données
$db_host = $env["DB_HOST"];
$db_user = $env["DB_USER"];
$db_pass = $env["DB_PASS"];
$db_name = $env["DB_NAME"];

try {
    // Créer une instance PDO pour la connexion
    $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_pass);
    
    // Définir le mode d'erreur PDO pour les exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour créer la table 'searches'
    $sql_searches = "
    CREATE TABLE IF NOT EXISTS searches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        search_date DATETIME NOT NULL,
        INDEX (username)
    );
    ";

    // Exécuter la requête pour créer la table 'searches'
    $pdo->exec($sql_searches);

    // Requête SQL pour créer la table 'posts'
    $sql_posts = "
    CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        search_id INT NOT NULL,
        post_date DATETIME NOT NULL,
        caption TEXT,
        FOREIGN KEY (search_id) REFERENCES searches(id) ON DELETE CASCADE,
        INDEX (search_id)
    );
    ";

    // Exécuter la requête pour créer la table 'posts'
    $pdo->exec($sql_posts);

} catch (PDOException $e) {
    // Si la connexion échoue ou qu'il y a une erreur, afficher l'erreur
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
