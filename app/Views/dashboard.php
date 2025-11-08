<?php
$title = 'Dashboard | MetroPost';
ob_start();
?>
<div class="container">
    <!-- Modern Dashboard Header -->
    <div class="modern-dashboard-header">
        <div class="header-main">
            <div class="user-welcome">
                <div class="user-avatar-large">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div class="welcome-content">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['name']) ?>! 👋</h1>
                    <p class="welcome-subtitle">Here's what's happening with your posts today</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9m0 0v12m0 0h6m-6 0h6" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= count($userPosts) ?></span>
                        <span class="stat-label">Total Posts</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number">
                            <?= array_sum(array_column($userPosts, 'likes')) ?>
                        </span>
                        <span class="stat-label">Total Likes</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="/posts/create" class="btn btn-primary create-post-btn-modern">
                <span class="btn-content">
                    <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Post
                </span>
                <span class="btn-hover-effect"></span>
            </a>
        </div>
    </div>

    <?php 
    use App\Core\Session;
    if (Session::get('success')): ?>
        <div class="alert alert-success">
            <div class="alert-content">
                <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= Session::get('success') ?>
            </div>
            <?php Session::remove('success'); ?>
        </div>
    <?php endif; ?>

    <?php if (Session::get('error')): ?>
        <div class="alert alert-error">
            <div class="alert-content">
                <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= Session::get('error') ?>
            </div>
            <?php Session::remove('error'); ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-section">
        <div class="section-header">
            <h3>Your Recent Posts</h3>
            <div class="section-actions">
                <span class="posts-count"><?= count($userPosts) ?> posts</span>
            </div>
        </div>
        
        <?php if (empty($userPosts)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4>No posts yet</h4>
                <p>Share your first thought with the community</p>
                <a href="/posts/create" class="btn btn-primary">Create Your First Post</a>
            </div>
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
                                <button class="btn-edit-icon" title="Edit post (Currently disabled)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="btn-edit-tooltip">Edit post (Disabled)</span>
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
                            
                            <!-- Centered image below text -->
                            <?php if ($post['image']): ?>
                                <div class="post-image">
                                    <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                                </div>
                            <?php endif; ?>
                            
                            <!-- Like section -->
                            <div class="post-interactions">
                                <button class="like-btn <?= $post['user_has_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>" title="Like (Currently disabled)">
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

<!-- Edit Modal (Removed since edit is disabled) -->

<script>
// Edit button does nothing
function openEditModal(postId, content) {
    // Do nothing
    console.log('Edit functionality is currently disabled');
    return false;
}

function closeEditModal() {
    // Do nothing
}

// Like functionality does nothing
document.addEventListener('DOMContentLoaded', function() {
    console.log('Like and edit functionality are currently disabled');
    
    // Prevent like button clicks
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('Like functionality is currently disabled');
            return false;
        });
    });

    // Prevent edit button clicks
    document.querySelectorAll('.btn-edit-icon').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('Edit functionality is currently disabled');
            return false;
        });
    });
});

// Make edit buttons non-functional by removing onclick attributes
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-icon');
    editButtons.forEach(button => {
        button.removeAttribute('onclick');
    });
});
</script>

<style>
/* Modern Dashboard Header Styles */
.modern-dashboard-header {
    background: linear-gradient(135deg, #ffffff 0%, #fef6ee 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    border: 1px solid #ffedd5;
    box-shadow: 0 8px 32px rgba(242, 104, 24, 0.08);
    position: relative;
    overflow: hidden;
}

.modern-dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(242, 104, 24, 0.05) 0%, rgba(255, 140, 66, 0.1) 100%);
    border-radius: 0 0 0 100px;
    z-index: 0;
}

.header-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.user-welcome {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.user-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f26818 0%, #ff8c42 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 32px;
    box-shadow: 0 8px 24px rgba(242, 104, 24, 0.3);
    border: 4px solid #fff;
}

.welcome-content h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 800;
    color: #1c1e21;
    line-height: 1.2;
}

.welcome-subtitle {
    margin: 0;
    color: #6b7280;
    font-size: 16px;
    font-weight: 500;
}

.header-stats {
    display: flex;
    gap: 16px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: white;
    border-radius: 16px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    min-width: 140px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fef6ee 0%, #ffedd5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f26818;
}

.stat-icon svg {
    width: 20px;
    height: 20px;
}

.stat-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-number {
    font-size: 24px;
    font-weight: 800;
    color: #1c1e21;
    line-height: 1;
}

.stat-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.header-actions {
    display: flex;
    justify-content: flex-end;
    position: relative;
    z-index: 1;
}

.create-post-btn-modern {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 28px;
    background: linear-gradient(135deg, #f26818 0%, #ff8c42 100%);
    color: white;
    text-decoration: none;
    border-radius: 16px;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    box-shadow: 0 8px 24px rgba(242, 104, 24, 0.3);
}

.create-post-btn-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(242, 104, 24, 0.4);
}

.create-post-btn-modern:active {
    transform: translateY(-1px);
}

/* Enhanced Alert Styles */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 500;
    border: 1px solid;
}

.alert-success {
    background: #f0fdf4;
    color: #166534;
    border-color: #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f1f5f9;
}

.section-header h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1c1e21;
}

.section-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.posts-count {
    background: #f3f4f6;
    color: #6b7280;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    border: 2px dashed #e5e7eb;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    color: #d1d5db;
}

.empty-icon svg {
    width: 100%;
    height: 100%;
}

.empty-state h4 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 600;
    color: #374151;
}

.empty-state p {
    margin: 0 0 24px 0;
    color: #6b7280;
    font-size: 16px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .modern-dashboard-header {
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
    }
    
    .header-main {
        flex-direction: column;
        gap: 24px;
    }
    
    .user-welcome {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .user-avatar-large {
        width: 60px;
        height: 60px;
        font-size: 24px;
        align-self: center;
    }
    
    .welcome-content h1 {
        font-size: 24px;
    }
    
    .header-stats {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .stat-card {
        min-width: 120px;
        padding: 12px 16px;
    }
    
    .stat-number {
        font-size: 20px;
    }
    
    .section-header {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .create-post-btn-modern {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
    }
}

@media (max-width: 480px) {
    .modern-dashboard-header {
        padding: 20px;
        border-radius: 12px;
    }
    
    .user-avatar-large {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .welcome-content h1 {
        font-size: 20px;
    }
    
    .welcome-subtitle {
        font-size: 14px;
    }
    
    .stat-card {
        min-width: 100px;
        padding: 10px 12px;
    }
    
    .stat-number {
        font-size: 18px;
    }
    
    .stat-icon {
        width: 32px;
        height: 32px;
    }
    
    .stat-icon svg {
        width: 16px;
        height: 16px;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';