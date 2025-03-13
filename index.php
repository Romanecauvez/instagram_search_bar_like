<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">Recherche de Posts Instagram</h1>
            
            <form id="searchForm" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur Instagram</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: mrbeast" required>
                </div>
                
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">Rechercher</button>
            </form>
            
            <div id="loading" class="hidden mt-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                <p class="mt-2 text-gray-600">Chargement des données...</p>
            </div>
            
            <div id="results" class="mt-6 hidden">
                <h2 class="text-xl font-semibold mb-3">Résultats</h2>
                <div id="postsList" class="space-y-4"></div>
            </div>
            
            <div class="mt-6 pt-4 border-t">
                <a href="history.php" class="text-blue-500 hover:underline">Voir l'historique des recherches</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/index.js"></script>
</body>
</html>
