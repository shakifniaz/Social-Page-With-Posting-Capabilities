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
                        <button class="like-btn <?= $post['user_has_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
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
    console.log('Setting up like buttons with animations...');
    
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const likeIcon = this.querySelector('.like-icon');
            const likeCount = this.querySelector('.like-count');
            
            console.log('Like button clicked for post:', this.getAttribute('data-post-id'));
            
            const isLiked = this.classList.contains('liked');
            
            if (isLiked) {
                this.classList.remove('liked');
                likeIcon.setAttribute('fill', 'none');
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            } else {
                this.classList.add('liked');
                likeIcon.setAttribute('fill', 'currentColor');
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            }
            
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
            
            likeIcon.style.transform = 'scale(1.3)';
            setTimeout(() => {
                likeIcon.style.transform = 'scale(1)';
            }, 200);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-btn');
    console.log('Found like buttons in posts page:', likeButtons.length);
    likeButtons.forEach(btn => {
        console.log('Like button for post:', btn.getAttribute('data-post-id'));
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';