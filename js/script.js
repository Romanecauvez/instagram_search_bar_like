document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const loadingIndicator = document.getElementById('loading');
    const resultsContainer = document.getElementById('results');
    const postsListContainer = document.getElementById('postsList');

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value.trim();
        
        if (!username) return;
        
        // Afficher l'indicateur de chargement
        loadingIndicator.classList.remove('hidden');
        resultsContainer.classList.add('hidden');
        
        // Appeler le script PHP qui contactera l'API
        fetch(`api/fetch.php?username=${encodeURIComponent(username)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                displayResults(data, username);
                loadingIndicator.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                loadingIndicator.classList.add('hidden');
                alert('Une erreur est survenue lors de la récupération des données.');
            });
    });
    
    function displayResults(data, username) {
        postsListContainer.innerHTML = '';
        
        if (data.success && data.posts && data.posts.length > 0) {
            data.posts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.className = 'bg-gray-50 p-4 rounded border';
                
                const date = new Date(post.timestamp * 1000).toLocaleDateString();
                
                postElement.innerHTML = `
                    <div class="flex justify-between items-start">
                        <p class="text-sm text-gray-500">${date}</p>
                    </div>
                    <p class="mt-2">${post.caption || 'Aucune description'}</p>
                `;
                
                postsListContainer.appendChild(postElement);
            });
        } else {
            postsListContainer.innerHTML = '<p class="text-center text-gray-500">Aucun post trouvé pour cet utilisateur.</p>';
        }
    }
});
