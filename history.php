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
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">Historique des recherches</h1>

            <!-- Indicateur de chargement -->
            <div id="loading" class="hidden mt-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                <p class="mt-2 text-gray-600">Chargement de l'historique...</p>
            </div>

            <!-- Conteneur des résultats -->
            <div id="results" class="mt-6 hidden">
                <div id="postsList" class="space-y-4"></div>
            </div>

            <!-- Lien pour revenir à la recherche -->
            <div class="mt-6 pt-4 border-t">
                <a href="index.php" class="text-blue-500 hover:underline">Retour à la recherche</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/history.js"></script>
</body>
</html>
