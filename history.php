<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des recherches - Instagram Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include 'includes/header.php'; ?>
    <?php require_once 'includes/db.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Historique des recherches</h1>
                <a href="index.php" class="text-blue-500 hover:underline">Nouvelle recherche</a>
            </div>
            
            <?php
            // Récupérer l'historique des recherches
            $query = "SELECT id, username, search_date FROM searches ORDER BY search_date DESC";
            $result = $pdo->query($query);
            
            if ($result->num_rows > 0) {
                echo '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
                echo '<ul class="divide-y divide-gray-200">';
                
                while ($row = $result->fetch_assoc()) {
                    $date = date('d/m/Y H:i', strtotime($row['search_date']));
                    echo '<li class="p-4 hover:bg-gray-50">';
                    echo '<a href="history.php?search_id=' . $row['id'] . '" class="block">';
                    echo '<div class="flex justify-between items-center">';
                    echo '<span class="font-medium">@' . htmlspecialchars($row['username']) . '</span>';
                    echo '<span class="text-sm text-gray-500">' . $date . '</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '</li>';
                }
                
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="bg-white rounded-lg shadow-md p-6 text-center">';
                echo '<p class="text-gray-500">Aucune recherche effectuée pour le moment.</p>';
                echo '</div>';
            }
            
            // Afficher les posts d'une recherche spécifique
            if (isset($_GET['search_id']) && is_numeric($_GET['search_id'])) {
                $search_id = (int)$_GET['search_id'];
                
                // Récupérer les informations de la recherche
                $search_query = "SELECT username FROM searches WHERE id = ?";
                $stmt = $pdo->prepare($search_query);
                $stmt->bind_param("i", $search_id);
                $stmt->execute();
                $search_result = $stmt->get_result();
                $search_info = $search_result->fetch_assoc();
                
                if ($search_info) {
                    echo '<div class="mt-8">';
                    echo '<h2 class="text-xl font-semibold mb-4">Posts de @' . htmlspecialchars($search_info['username']) . '</h2>';
                    
                    // Récupérer les posts
                    $posts_query = "SELECT post_date, caption FROM posts WHERE search_id = ? ORDER BY post_date DESC";
                    $stmt = $pdo->prepare($posts_query);
                    $stmt->bind_param("i", $search_id);
                    $stmt->execute();
                    $posts_result = $stmt->get_result();
                    
                    if ($posts_result->num_rows > 0) {
                        echo '<div class="space-y-4">';
                        
                        while ($post = $posts_result->fetch_assoc()) {
                            $post_date = date('d/m/Y', strtotime($post['post_date']));
                            
                            echo '<div class="bg-white p-4 rounded-lg shadow-md">';
                            echo '<div class="flex justify-between items-start">';
                            echo '<p class="text-sm text-gray-500">' . $post_date . '</p>';
                            echo '</div>';
                            echo '<p class="mt-2">' . (empty($post['caption']) ? 'Aucune description' : nl2br(htmlspecialchars($post['caption']))) . '</p>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    } else {
                        echo '<p class="text-center text-gray-500 bg-white p-4 rounded-lg shadow-md">Aucun post trouvé pour cet utilisateur.</p>';
                    }
                    
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
