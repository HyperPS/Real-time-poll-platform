/**
 * Poll Voting JavaScript - Handles voting interface and real-time updates
 */

class PollVotingManager {
    constructor() {
        this.currentPollId = null;
        this.votingInProgress = false;
        this.resultsRefreshInterval = null;
    }

    /**
     * Initialize voting for a specific poll
     */
    async initiatePoll(pollId) {
        this.currentPollId = pollId;

        try {
            // Check if IP has already voted
            const statusResponse = await apiRequest('GET', `/api/vote/status?poll_id=${pollId}`);

            if (statusResponse.hasVoted) {
                this.showAlreadyVotedMessage(statusResponse.optionId, pollId);
            } else {
                this.showVotingInterface(pollId);
            }

            // Start real-time results update
            this.startResultsRefresh(pollId);

        } catch (error) {
            console.error('Error initiating poll:', error);
            this.showErrorMessage('Failed to load poll');
        }
    }

    /**
     * Submit a vote
     */
    async submitVote(pollId, optionId) {
        if (this.votingInProgress) return;

        this.votingInProgress = true;

        try {
            const response = await apiRequest('POST', '/api/vote/cast', {
                poll_id: pollId,
                option_id: optionId
            });

            if (response.success) {
                this.showVoteSuccessMessage();
                this.disableVotingInterface();
                this.updateResults(pollId);
            } else {
                this.showErrorMessage(response.message || 'Failed to cast vote');
            }

        } catch (error) {
            console.error('Voting error:', error);
            this.showErrorMessage(error.message || 'Failed to cast vote');
        } finally {
            this.votingInProgress = false;
        }
    }

    /**
     * Update poll results
     */
    async updateResults(pollId) {
        try {
            const response = await apiRequest('GET', `/api/results?poll_id=${pollId}`);

            if (response.success) {
                this.renderResults(response);
            }

        } catch (error) {
            console.error('Error fetching results:', error);
        }
    }

    /**
     * Start continuous results refresh
     */
    startResultsRefresh(pollId) {
        // Clear existing interval
        if (this.resultsRefreshInterval) {
            clearInterval(this.resultsRefreshInterval);
        }

        // Update every 1 second
        this.resultsRefreshInterval = setInterval(() => {
            this.updateResults(pollId);
        }, 1000);
    }

    /**
     * Stop results refresh
     */
    stopResultsRefresh() {
        if (this.resultsRefreshInterval) {
            clearInterval(this.resultsRefreshInterval);
            this.resultsRefreshInterval = null;
        }
    }

    /**
     * Render poll results
     */
    renderResults(data) {
        const resultsContainer = document.getElementById('pollResults');
        if (!resultsContainer) return;

        let html = '';

        data.options.forEach(option => {
            const percentage = option.percentage || 0;
            const barWidth = percentage.toFixed(1);

            html += `
                <div class="result-item mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-500">${escapeHtml(option.option_text)}</span>
                        <span class="badge bg-primary">${option.vote_count} vote${option.vote_count !== 1 ? 's' : ''}</span>
                    </div>
                    <div class="progress" style="height: 24px;">
                        <div class="progress-bar" style="width: ${barWidth}%;">
                            <small class="text-white fw-bold" style="font-size: 12px;">
                                ${barWidth}%
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
            <div class="text-muted small mt-3 p-2 bg-light rounded">
                <i class="fas fa-chart-pie"></i> Total Votes: <strong>${data.total_votes}</strong>
            </div>
        `;

        resultsContainer.innerHTML = html;
    }

    /**
     * Show voting interface
     */
    showVotingInterface(pollId) {
        const container = document.getElementById('votingInterface');
        if (!container) return;

        container.innerHTML = `
            <form id="votingForm">
                <div id="votingOptions"></div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-vote-yea"></i> Cast Your Vote
                    </button>
                </div>
            </form>
        `;

        // Populate options via AJAX
        this.loadPollOptions(pollId);

        // Handle form submission
        document.getElementById('votingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const selectedOption = document.querySelector('input[name="option"]:checked');
            if (selectedOption) {
                this.submitVote(pollId, selectedOption.value);
            } else {
                this.showErrorMessage('Please select an option');
            }
        });
    }

    /**
     * Load poll options
     */
    async loadPollOptions(pollId) {
        try {
            const response = await apiRequest('GET', `/api/polls/${pollId}`);

            if (response.success) {
                const options = response.poll.options;
                let html = '';

                options.forEach(option => {
                    html += `
                        <div class="form-check mb-3 p-3 bg-light rounded option-item" style="cursor: pointer; transition: all 0.3s ease;">
                            <input class="form-check-input" type="radio" name="option" value="${option.id}" id="option_${option.id}">
                            <label class="form-check-label w-100" for="option_${option.id}">
                                ${escapeHtml(option.option_text)}
                            </label>
                        </div>
                    `;

                    // Add hover effect
                    const optionElement = document.getElementById(`option_${option.id}`);
                    if (optionElement) {
                        optionElement.parentElement.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#ede9fe';
                            this.style.transform = 'translateX(4px)';
                        });
                        optionElement.parentElement.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '#f3f4f6';
                            this.style.transform = 'translateX(0)';
                        });
                    }
                });

                document.getElementById('votingOptions').innerHTML = html;
            }

        } catch (error) {
            console.error('Error loading options:', error);
        }
    }

    /**
     * Show already voted message
     */
    showAlreadyVotedMessage(optionId, pollId) {
        const container = document.getElementById('votingInterface');
        if (!container) return;

        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-check-circle"></i> You have already voted on this poll.
            </div>
            <div id="pollResults"></div>
        `;

        // Show results
        this.updateResults(pollId);
    }

    /**
     * Disable voting interface
     */
    disableVotingInterface() {
        const options = document.querySelectorAll('input[name="option"]');
        options.forEach(option => {
            option.disabled = true;
        });

        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Vote Submitted!';
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-success');
        }
    }

    /**
     * Show error message
     */
    showErrorMessage(message) {
        const container = document.getElementById('votingInterface');
        if (container) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            container.insertBefore(alert, container.firstChild);
        }
    }

    /**
     * Show vote success message
     */
    showVoteSuccessMessage() {
        const container = document.getElementById('votingInterface');
        if (container) {
            const message = document.createElement('div');
            message.className = 'alert alert-success alert-dismissible fade show';
            message.innerHTML = `
                <i class="fas fa-check-circle"></i> Your vote has been recorded!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            container.insertBefore(message, container.firstChild);
        }
    }
}

// Initialize manager globally
const pollVotingManager = new PollVotingManager();

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    pollVotingManager.stopResultsRefresh();
});
