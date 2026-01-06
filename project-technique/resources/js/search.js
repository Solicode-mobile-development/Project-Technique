document.addEventListener('DOMContentLoaded', function() {
    // Search Functionality
    const searchForm = document.getElementById('search-form');
    const propertyGrid = document.getElementById('property-grid');
    const paginationContainer = document.getElementById('pagination');
    const loadingSpinner = document.getElementById('loading');
    let searchTimeout;

    if (searchForm) {
        const inputs = searchForm.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500);
            });
        });
    }

    function performSearch() {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);

        // Show loading, hide grid
        propertyGrid.classList.add('opacity-50');
        loadingSpinner.classList.remove('hidden');

        fetch(`/properties/search?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            propertyGrid.innerHTML = data.html;
            paginationContainer.innerHTML = data.pagination;
            
            // Re-initialize modal listeners for new content
            initModalListeners();
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            propertyGrid.classList.remove('opacity-50');
            loadingSpinner.classList.add('hidden');
        });
    }

    // Modal Details Loading
    function initModalListeners() {
        // Use event delegation for better performance with dynamic content
        const buttons = document.querySelectorAll('.view-property-btn');
        
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const propertyId = this.dataset.id;
                loadPropertyDetails(propertyId);
            });
        });
    }

    function loadPropertyDetails(id) {
        const modalBody = document.getElementById('modal-content-body');
        
        // Show loading state in modal
        modalBody.innerHTML = `
            <div class="text-center py-10">
                <div class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="loading">
                    <span class="sr-only">Chargement...</span>
                </div>
            </div>
        `;

        fetch(`/properties/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            modalBody.innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error loading property details:', error);
            modalBody.innerHTML = '<p class="text-center text-red-500 py-4">Erreur lors du chargement des détails.</p>';
        });
    }

    // Initial listeners
    initModalListeners();
});
