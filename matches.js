// Matches page functionality

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}`).classList.add('active');
        });
    });
    
    // Load matches from the server
    loadMatches();
    
    // Load favorites
    loadFavorites();
});

// Function to load matches from the server
function loadMatches() {
    fetch('backend/get_matches.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayMatches(data.matches);
                document.getElementById('matches-count').textContent = 
                    `We found ${data.matches.length} potential roommates that match your preferences!`;
            } else {
                document.getElementById('all-matches').innerHTML = 
                    `<div class="loading">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error fetching matches:', error);
            document.getElementById('all-matches').innerHTML = 
                '<div class="loading">Error loading matches. Please try again later.</div>';
        });
}

// Function to load favorites
function loadFavorites() {
    fetch('backend/get_favorites.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.favorites.length > 0) {
                displayFavorites(data.favorites);
            } else {
                document.getElementById('favorites').innerHTML = 
                    `<div class="no-favorites">
                        <p>You haven't added any favorites yet.</p>
                        <p>Browse your matches and click "Add to Favorites" to save them here.</p>
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Error fetching favorites:', error);
            document.getElementById('favorites').innerHTML = 
                '<div class="loading">Error loading favorites. Please try again later.</div>';
        });
}

// Function to display matches
function displayMatches(matches) {
    const matchesContainer = document.getElementById('all-matches');
    
    if (matches.length === 0) {
        matchesContainer.innerHTML = 
            `<div class="no-favorites">
                <p>No matches found.</p>
                <p>Try adjusting your preferences to find more potential roommates.</p>
            </div>`;
        return;
    }
    
    let matchesHTML = '';
    
    matches.forEach(match => {
        matchesHTML += `
            <div class="match-card">
                <div class="match-header">
                    <div class="match-avatar">
                        <img src="https://via.placeholder.com/100" alt="${match.name}">
                    </div>
                    <div class="match-info">
                        <h3>${match.name}</h3>
                        <p>${match.age} years old</p>
                        <span class="match-compatibility">${match.compatibility}% Match</span>
                    </div>
                </div>
                <div class="match-details">
                    <div class="match-detail">
                        <i class="fas fa-envelope"></i>
                        <span>${match.email}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-phone"></i>
                        <span>${match.phone}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${match.location}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-rupee-sign"></i>
                        <span>Budget: ${match.budget}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-users"></i>
                        <span>Prefers ${match.roommates} roommate(s)</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-heart"></i>
                        <span>${formatLifestyle(match.lifestyle)}</span>
                    </div>
                </div>
                <div class="match-actions">
                    <button class="btn btn-secondary add-favorite" data-id="${match.id}">
                        Add to Favorites
                    </button>
                    <button class="btn btn-primary contact" data-email="${match.email}">
                        Contact
                    </button>
                </div>
            </div>
        `;
    });
    
    matchesContainer.innerHTML = matchesHTML;
    
    // Add event listeners to the buttons
    document.querySelectorAll('.add-favorite').forEach(button => {
        button.addEventListener('click', function() {
            addToFavorites(this.getAttribute('data-id'));
        });
    });
    
    document.querySelectorAll('.contact').forEach(button => {
        button.addEventListener('click', function() {
            window.location.href = `mailto:${this.getAttribute('data-email')}`;
        });
    });
}

// Function to display favorites
function displayFavorites(favorites) {
    const favoritesContainer = document.getElementById('favorites');
    let favoritesHTML = '';
    
    favorites.forEach(favorite => {
        favoritesHTML += `
            <div class="match-card">
                <div class="match-header">
                    <div class="match-avatar">
                        <img src="https://via.placeholder.com/100" alt="${favorite.name}">
                    </div>
                    <div class="match-info">
                        <h3>${favorite.name}</h3>
                        <p>${favorite.age} years old</p>
                    </div>
                </div>
                <div class="match-details">
                    <div class="match-detail">
                        <i class="fas fa-envelope"></i>
                        <span>${favorite.email}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-phone"></i>
                        <span>${favorite.phone}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${favorite.location}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-rupee-sign"></i>
                        <span>Budget: ${favorite.budget}</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-users"></i>
                        <span>Prefers ${favorite.roommates} roommate(s)</span>
                    </div>
                    <div class="match-detail">
                        <i class="fas fa-heart"></i>
                        <span>${formatLifestyle(favorite.lifestyle)}</span>
                    </div>
                </div>
                <div class="match-actions">
                    <button class="btn btn-primary contact" data-email="${favorite.email}">
                        Contact
                    </button>
                </div>
            </div>
        `;
    });
    
    favoritesContainer.innerHTML = favoritesHTML;
    
    // Add event listeners to the contact buttons
    document.querySelectorAll('.contact').forEach(button => {
        button.addEventListener('click', function() {
            window.location.href = `mailto:${this.getAttribute('data-email')}`;
        });
    });
}

// Function to add a match to favorites
function addToFavorites(matchId) {
    fetch('backend/add_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ match_id: matchId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Added to favorites!');
            // Reload favorites
            loadFavorites();
        } else {
            alert(data.message || 'Failed to add to favorites');
        }
    })
    .catch(error => {
        console.error('Error adding to favorites:', error);
        alert('An error occurred. Please try again.');
    });
}

// Helper function to format lifestyle text
function formatLifestyle(lifestyle) {
    switch (lifestyle) {
        case 'non-smoker-non-drinker':
            return 'Non-smoker / Non-drinker';
        case 'smoker-drinker':
            return 'Smoker / Drinker';
        case 'no-preference':
            return 'No preference';
        default:
            return lifestyle;
    }
}