class LikeSystem {
    constructor() {
        this.isProcessing = false;
        this.updateInterval = null;
        this.init();
    }

    init() {
        console.log('Enhanced LikeSystem initialized');
        this.setupLikeButtons();
        this.startRealTimeUpdates();
    }

    setupLikeButtons() {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Setting up like buttons...');
            this.attachLikeHandlers();
        });
    }

    attachLikeHandlers() {
        const likeButtons = document.querySelectorAll('.like-btn');
        console.log(`Found ${likeButtons.length} like buttons`);
        
        likeButtons.forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });

        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleLikeClick(e, button);
            });
        });
    }

    async handleLikeClick(e, button) {
        e.preventDefault();
        e.stopPropagation();
        
        if (this.isProcessing) {
            console.log('Already processing a like, skipping...');
            return;
        }

        const postId = button.getAttribute('data-post-id');
        const likeIcon = button.querySelector('.like-icon');
        const likeCount = button.querySelector('.like-count');
        
        console.log(`Processing like for post ${postId}`, {
            currentLiked: button.classList.contains('liked'),
            currentCount: likeCount.textContent
        });

        this.isProcessing = true;
        button.disabled = true;
        const originalHTML = button.innerHTML;
        
        button.innerHTML = `
            <svg class="animate-spin like-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        try {
            const formData = new FormData();
            formData.append('post_id', postId);
            
            console.log('Sending like request to server...');
            
            const response = await fetch('/posts/like', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Server response:', result);
            
            if (result.success) {
                this.updateButtonState(button, result.liked, result.likes);
                console.log(`Successfully ${result.liked ? 'liked' : 'unliked'} post ${postId}`);
            } else {
                throw new Error(result.message || 'Unknown server error');
            }
            
        } catch (error) {
            console.error('Error updating like:', error);
            this.showError('Failed to update like: ' + error.message);
            button.innerHTML = originalHTML;
            button.disabled = false;
        } finally {
            this.isProcessing = false;
        }
    }

    updateButtonState(button, isLiked, likeCount) {
        const likeIcon = button.querySelector('.like-icon');
        const countElement = button.querySelector('.like-count');
        
        if (isLiked) {
            button.classList.add('liked');
            likeIcon.setAttribute('fill', 'currentColor');
        } else {
            button.classList.remove('liked');
            likeIcon.setAttribute('fill', 'none');
        }
        
        countElement.textContent = likeCount;
        
        this.animateLike(button);
        
        button.disabled = false;
        
        console.log('Button state updated:', {
            liked: isLiked,
            count: likeCount
        });
    }

    animateLike(button) {
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);
    }

    startRealTimeUpdates() {
        this.updateInterval = setInterval(() => {
            this.updateLikeCounts();
        }, 3000);
    }

    async updateLikeCounts() {
        if (this.isProcessing) return;
        
        const postIds = Array.from(document.querySelectorAll('.like-btn'))
            .map(btn => btn.getAttribute('data-post-id'))
            .filter(id => id);
        
        if (postIds.length === 0) return;
        
        try {
            const formData = new FormData();
            formData.append('post_ids', JSON.stringify(postIds));
            
            const response = await fetch('/posts/get-likes', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                
                if (result.success && result.likes) {
                    this.applyLikeUpdates(result.likes);
                }
            }
        } catch (error) {
            console.log('Error updating like counts:', error);
        }
    }

    applyLikeUpdates(likesData) {
        let hasUpdates = false;
        
        likesData.forEach(likeData => {
            const buttons = document.querySelectorAll(`.like-btn[data-post-id="${likeData.post_id}"]`);
            
            buttons.forEach(button => {
                const likeCount = button.querySelector('.like-count');
                const currentCount = likeCount.textContent;
                
                if (currentCount !== likeData.likes.toString()) {
                    likeCount.textContent = likeData.likes;
                    hasUpdates = true;
                }
            });
        });
        
        if (hasUpdates) {
            console.log('Applied real-time like updates');
        }
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }
}

console.log('Loading Enhanced LikeSystem...');
const likeSystem = new LikeSystem();