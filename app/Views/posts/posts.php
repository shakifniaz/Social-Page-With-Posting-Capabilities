<?php
$title = 'Posts | MetroPost';
ob_start();
?>
<div class="container">
    <div class="posts-header">
        <h2>All Posts</h2>
        <a href="/posts/create" class="btn btn-primary create-post-btn">
            <span class="btn-content">
                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Post
            </span>
            <span class="btn-hover-effect"></span>
        </a>
    </div>

    <div class="posts-container">
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <div class="post-user-info">
                        <div class="user-avatar">
                            <?= strtoupper(substr($post['user_name'], 0, 1)) ?>
                        </div>
                        <div class="user-details">
                            <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                            <span class="post-date"><?= date('M j, Y g:i A', strtotime($post['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="post-content">
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    
                    <?php if ($post['image']): ?>
                        <div class="post-image">
                            <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-interactions">
                        <button class="like-btn <?= $post['user_has_liked'] ? 'liked' : '' ?>" 
                                data-post-id="<?= $post['id'] ?>" 
                                title="Like">
                            <svg class="like-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="<?= $post['user_has_liked'] ? 'currentColor' : 'none' ?>" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="like-count"><?= $post['likes'] ?></span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Like system initialized for Posts page');
    
    let isProcessing = false;
    
    function setupLikeButtons() {
        const likeButtons = document.querySelectorAll('.like-btn');
        console.log(`Found ${likeButtons.length} like buttons`);
        
        likeButtons.forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', handleLikeClick);
        });
    }
    
    async function handleLikeClick(event) {
        event.preventDefault();
        event.stopPropagation();
        
        if (isProcessing) {
            console.log('Already processing a like, skipping...');
            return;
        }
        
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        
        console.log(`Processing like for post ${postId}`);
        
        isProcessing = true;
        button.disabled = true;
        
        const isCurrentlyLiked = button.classList.contains('liked');
        const likeCountElement = button.querySelector('.like-count');
        const currentCount = parseInt(likeCountElement.textContent) || 0;
        
        if (isCurrentlyLiked) {
            button.classList.remove('liked');
            likeCountElement.textContent = currentCount - 1;
        } else {
            button.classList.add('liked');
            likeCountElement.textContent = currentCount + 1;
        }
        
        button.style.transform = 'scale(1.1)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);

        try {
            const formData = new FormData();
            formData.append('post_id', postId);
            
            console.log('Sending like request to /posts/like...');
            
            const response = await fetch('/posts/like', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Server response:', result);
            
            if (result.success) {
                console.log(`Successfully ${result.liked ? 'liked' : 'unliked'} post ${postId}. New count: ${result.likes}`);
                
            } else {
                throw new Error(result.message || 'Unknown server error');
            }
            
        } catch (error) {
            console.error('Error updating like:', error);
            
            if (isCurrentlyLiked) {
                button.classList.add('liked');
                likeCountElement.textContent = currentCount;
            } else {
                button.classList.remove('liked');
                likeCountElement.textContent = currentCount;
            }
            
            alert('Failed to update like: ' + error.message);
        } finally {
            isProcessing = false;
            button.disabled = false;
            
            const isLiked = button.classList.contains('liked');
            const currentCount = likeCountElement.textContent;
            
            button.innerHTML = `
                <svg class="like-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="like-count">${currentCount}</span>
            `;
        }
    }
    
    function startRealTimeUpdates() {
        setInterval(async () => {
            if (isProcessing) return;
            
            const postIds = Array.from(document.querySelectorAll('.like-btn'))
                .map(btn => btn.getAttribute('data-post-id'))
                .filter(id => id);
            
            if (postIds.length === 0) return;
            
            try {
                const formData = new FormData();
                formData.append('post_ids', JSON.stringify(postIds));
                
                const response = await fetch('/posts/get-likes', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    if (result.success && result.likes) {
                        result.likes.forEach(likeData => {
                            const buttons = document.querySelectorAll(`.like-btn[data-post-id="${likeData.post_id}"]`);
                            
                            buttons.forEach(button => {
                                const likeCount = button.querySelector('.like-count');
                                if (likeCount && likeCount.textContent !== likeData.likes.toString()) {
                                    likeCount.textContent = likeData.likes;
                                }
                            });
                        });
                    }
                }
            } catch (error) {
                console.log('Error updating like counts:', error);
            }
        }, 5000);
    }
    
    setupLikeButtons();
    startRealTimeUpdates();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>