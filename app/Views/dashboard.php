<?php
$title = 'Dashboard | MetroPost';
ob_start();
?>
<div class="container">
    <div class="dashboard-header">
        <div class="welcome-text">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']) ?></h2>
            <p>Your email: <?= htmlspecialchars($user['email']) ?></p>
        </div>
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

    <?php 
    use App\Core\Session;
    if (Session::get('success')): ?>
        <div class="alert alert-success">
            <?= Session::get('success') ?>
            <?php Session::remove('success'); ?>
        </div>
    <?php endif; ?>

    <?php if (Session::get('error')): ?>
        <div class="alert alert-error">
            <?= Session::get('error') ?>
            <?php Session::remove('error'); ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-section">
        <h3>Your Recent Posts</h3>
        
        <?php if (empty($userPosts)): ?>
            <p>You haven't created any posts yet.</p>
        <?php else: ?>
            <div class="posts-container">
                <?php foreach ($userPosts as $post): ?>
                    <div class="post" id="post-<?= $post['id'] ?>">
                        <div class="post-header">
                            <div class="post-user-info">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($post['user_name'], 0, 1)) ?>
                                </div>
                                <div class="user-details">
                                    <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                                    <span class="post-date">
                                        <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                                        <?php if ($post['edited']): ?>
                                            <span class="edited-badge">(Edited)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="post-actions">
                                <button class="btn-edit-icon" title="Edit post">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="btn-edit-tooltip">Edit post</span>
                                </button>
                                <form method="POST" action="/posts/delete" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn-delete-icon" title="Delete post">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="btn-delete-tooltip">Delete post</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <!-- Text content aligned to container start -->
                            <p id="post-content-<?= $post['id'] ?>"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            
                            <?php if ($post['image']): ?>
                                <div class="post-image">
                                    <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-interactions">
                                <button class="like-btn <?= $post['user_has_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>" title="Like">
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
        <?php endif; ?>
    </div>
</div>


<script>
function openEditModal(postId, content) {
    console.log('Edit functionality is currently disabled');
    return false;
}

function closeEditModal() {
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Like and edit functionality are currently disabled');
    
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('Like functionality is currently disabled');
            return false;
        });
    });

    document.querySelectorAll('.btn-edit-icon').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('Edit functionality is currently disabled');
            return false;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-icon');
    editButtons.forEach(button => {
        button.removeAttribute('onclick');
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';