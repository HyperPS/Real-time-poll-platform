/**
 * Main AJAX and UI functionality
 */

// Poll voting AJAX handler
function castVote(pollId, optionId) {
    return $.ajax({
        url: '/api/vote/cast',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            poll_id: pollId,
            option_id: optionId
        }),
        dataType: 'json',
        timeout: 5000
    });
}

// Get poll results
function getPollResults(pollId) {
    return $.ajax({
        url: `/api/results?poll_id=${pollId}`,
        method: 'GET',
        dataType: 'json',
        timeout: 5000
    });
}

// Check vote status for current IP
function checkVoteStatus(pollId) {
    return $.ajax({
        url: `/api/vote/status?poll_id=${pollId}`,
        method: 'GET',
        dataType: 'json',
        timeout: 5000
    });
}

// Fetch all polls
function fetchPolls() {
    return $.ajax({
        url: '/api/polls',
        method: 'GET',
        dataType: 'json',
        timeout: 5000
    });
}

// Initialize 3D hover effects with Three.js
function init3DHoverEffects() {
    // Get all cards
    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        // Add 3D perspective on hover
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) rotateX(3deg) rotateZ(1deg)';
            this.style.boxShadow = '0 16px 48px rgba(79, 70, 229, 0.3)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) rotateX(0) rotateZ(0)';
            this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.1)';
        });

        // Parallax effect on mouse move
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            const y = (e.clientY - rect.top) / rect.height;

            const rotateX = (y - 0.5) * 10;
            const rotateY = (x - 0.5) * 10;

            this.style.transform = `
                perspective(1000px)
                rotateX(${rotateX}deg)
                rotateY(${rotateY}deg)
                translateZ(10px)
            `;
        });
    });
}

// Initialize real-time poll updates
function initializeRealTimeUpdates() {
    // This function can be called to set up polling intervals
    console.log('Real-time updates initialized');
}

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize on document ready
$(document).ready(function() {
    // Initialize 3D effects
    init3DHoverEffects();

    // Setup AJAX error handlers
    $(document).ajaxError(function(event, xhr, settings, error) {
        console.error('AJAX Error:', error);
        if (xhr.status === 401) {
            window.location.href = '/';
        }
    });

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        beforeSend: function(xhr) {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token.content);
            }
        }
    });
});

// Format date to readable string
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Show loading spinner
function showSpinner(container) {
    $(container).html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
}

// Show error message
function showError(container, message) {
    $(container).html(`
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle"></i> ${escapeHtml(message)}
        </div>
    `);
}

// API request with error handling
function apiRequest(method, url, data = null) {
    return new Promise((resolve, reject) => {
        const options = {
            type: method,
            url: url,
            dataType: 'json',
            timeout: 10000,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (data) {
            options.contentType = 'application/json';
            options.data = JSON.stringify(data);
        }

        $.ajax(options)
            .done(function(response) {
                resolve(response);
            })
            .fail(function(xhr, status, error) {
                reject({
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    message: xhr.responseJSON?.message || 'An error occurred'
                });
            });
    });
}
