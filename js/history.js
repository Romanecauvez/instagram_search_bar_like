document.addEventListener('DOMContentLoaded', function () {
    const resultsContainer = document.getElementById('results');
    const postsList = document.getElementById('postsList');
    const loadingIndicator = document.getElementById('loading');

    // Fonction pour charger l'historique des recherches
    function loadHistory() {
        // Afficher l'indicateur de chargement
        loadingIndicator.classList.remove('hidden');
        resultsContainer.classList.add('hidden');

        // Faire une requête à l'API pour récupérer l'historique
        fetch('api/history.php') // Remplacez par l'URL de votre API
            .then(response => response.json())
            .then(data => {
                // Masquer l'indicateur de chargement
                loadingIndicator.classList.add('hidden');

                if (data.success && data.history.length > 0) {
                    // Afficher les résultats
                    postsList.innerHTML = ''; // Vider la liste actuelle

                    data.history.forEach(search => {
                        const searchItem = document.createElement('div');
                        searchItem.className = 'bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow';
                        searchItem.innerHTML = `
                            <div class="flex justify-between items-center">
                                <span class="font-medium">@${search.username}</span>
                                <span class="text-sm text-gray-500">${search.search_date}</span>
                            </div>
                            <div class="mt-2">
                                <button onclick="loadPosts(${search.id})" class="text-blue-500 hover:underline">Voir les posts</button>
                            </div>
                        `;
                        postsList.appendChild(searchItem);
                    });

                    // Afficher le conteneur des résultats
                    resultsContainer.classList.remove('hidden');
                } else {
                    // Afficher un message si aucun historique n'est trouvé
                    postsList.innerHTML = '<p class="text-center text-gray-500">Aucune recherche trouvée dans l\'historique.</p>';
                    resultsContainer.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération de l\'historique :', error);
                loadingIndicator.classList.add('hidden');
                postsList.innerHTML = '<p class="text-center text-red-500">Une erreur s\'est produite lors du chargement de l\'historique.</p>';
                resultsContainer.classList.remove('hidden');
            });
    }

    // Fonction pour charger les posts d'une recherche spécifique
    window.loadPosts = function (searchId) {
        // Afficher l'indicateur de chargement
        loadingIndicator.classList.remove('hidden');
        resultsContainer.classList.add('hidden');

        // Faire une requête à l'API pour récupérer les posts
        fetch(`api/posts.php?search_id=${searchId}`)
            .then(response => response.json())
            .then(data => {
                // Masquer l'indicateur de chargement
                loadingIndicator.classList.add('hidden');

                if (data.success && data.posts.length > 0) {
                    // Afficher les posts
                    postsList.innerHTML = ''; // Vider la liste actuelle

                    data.posts.forEach(post => {
                        const postDate = new Date(post.post_date).toLocaleDateString('fr-FR');
                        const postItem = document.createElement('div');
                        postItem.className = 'bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow';
                        postItem.innerHTML = `
                            <div class="flex justify-between items-start">
                                <p class="text-sm text-gray-500">${postDate}</p>
                            </div>
                            <p class="mt-2">${post.caption || 'Aucune description'}</p>
                        `;
                        postsList.appendChild(postItem);
                    });

                    // Afficher le conteneur des résultats
                    resultsContainer.classList.remove('hidden');
                } else {
                    // Afficher un message si aucun post n'est trouvé
                    postsList.innerHTML = '<p class="text-center text-gray-500">Aucun post trouvé pour cette recherche.</p>';
                    resultsContainer.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des posts :', error);
                loadingIndicator.classList.add('hidden');
                postsList.innerHTML = '<p class="text-center text-red-500">Une erreur s\'est produite lors du chargement des posts.</p>';
                resultsContainer.classList.remove('hidden');
            });
    };

    // Charger l'historique au chargement de la page
    loadHistory();
});
