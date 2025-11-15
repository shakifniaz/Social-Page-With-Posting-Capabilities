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
                                <button class="btn-edit-icon edit-post-btn" 
                                        data-post-id="<?= $post['id'] ?>" 
                                        data-content="<?= htmlspecialchars($post['content']) ?>"
                                        data-image="<?= $post['image'] ? htmlspecialchars($post['image']) : '' ?>"
                                        title="Edit post">
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
                            <p id="post-content-<?= $post['id'] ?>"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            
                            <?php if ($post['image']): ?>
                                <div class="post-image" id="post-image-<?= $post['id'] ?>">
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

<!-- Edit Post Modal -->
<div id="editPostModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Post</h3>
            <button type="button" class="close-modal" id="closeEditModal">&times;</button>
        </div>
        <form id="editPostForm" enctype="multipart/form-data">
            <input type="hidden" id="edit_post_id" name="post_id">
            
            <div class="form-group">
                <label for="edit_content" class="form-label">Content</label>
                <textarea 
                    id="edit_content" 
                    name="content" 
                    rows="4" 
                    required 
                    maxlength="255" 
                    placeholder="Edit your post content..."
                    class="content-textarea"
                ></textarea>
                <div class="input-footer">
                    <div class="char-count" id="edit_charCount">0/255</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Image</label>
                <div class="image-upload-container">
                    <input type="file" name="image" id="edit_image" accept="image/*" class="image-input" hidden>
                    <label for="edit_image" class="image-upload-area" id="edit_imageUploadArea">
                        <div class="upload-content">
                            <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="upload-text">
                                <strong>Click to upload or change image</strong>
                                <span>PNG, JPG, GIF up to 5MB</span>
                            </div>
                        </div>
                    </label>
                    <div class="image-preview" id="edit_imagePreview" style="display: none;">
                        <img id="edit_previewImage" src="" alt="Preview">
                        <button type="button" class="remove-image-btn" id="edit_removeImageBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="current-image" id="currentImageSection" style="display: none;">
                        <p class="current-image-label">Current Image:</p>
                        <div class="current-image-preview">
                            <img id="currentImage" src="" alt="Current image">
                            <button type="button" class="remove-current-image-btn" id="removeCurrentImageBtn">
                                Remove Image
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="remove_current_image" name="remove_current_image" value="0">
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="updatePostBtn">
                    <span class="btn-content">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Post
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="notification" class="notification" style="display: none;"></div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 16px;
    padding: 0;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 24px 0 24px;
    margin-bottom: 24px;
}

.modal-header h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1c1e21;
}

.close-modal {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-modal:hover {
    background: #f3f4f6;
    color: #374151;
}

#editPostForm {
    padding: 0 24px 24px 24px;
}

.current-image {
    margin-top: 16px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.current-image-label {
    margin: 0 0 12px 0;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.current-image-preview {
    display: flex;
    align-items: center;
    gap: 16px;
}

.current-image-preview img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
}

.remove-current-image-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-current-image-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 16px 24px;
    border-radius: 12px;
    font-weight: 500;
    z-index: 1001;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.notification.success {
    background: #10b981;
    color: white;
}

.notification.error {
    background: #ef4444;
    color: white;
}

.btn-edit-icon {
    background: none;
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.3s ease;
    position: relative;
}

.btn-edit-icon:hover {
    background: #f3f4f6;
    color: #374151;
}

.btn-edit-tooltip {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: #374151;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
}

.btn-edit-icon:hover .btn-edit-tooltip {
    opacity: 1;
    visibility: visible;
}

.image-upload-container {
    margin-top: 8px;
}

.image-upload-area {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
}

.image-upload-area:hover {
    border-color: #f26818;
    background: #fef6ee;
}

.image-upload-area.drag-over {
    border-color: #f26818;
    background: #fef6ee;
    transform: scale(1.02);
}

.upload-content {
    text-align: center;
}

.upload-icon {
    width: 48px;
    height: 48px;
    color: #9ca3af;
    margin-bottom: 12px;
}

.upload-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.upload-text strong {
    color: #374151;
    font-size: 16px;
}

.upload-text span {
    color: #6b7280;
    font-size: 14px;
}

.image-preview {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.image-preview img {
    width: 100%;
    height: auto;
    max-height: 200px;
    object-fit: cover;
    display: block;
}

.remove-image-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.7);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
}

.remove-image-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.1);
}

.remove-image-btn svg {
    width: 16px;
    height: 16px;
}

.content-textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    resize: vertical;
    font-family: inherit;
    font-size: 16px;
    line-height: 1.5;
    color: #374151;
    background: #fafafa;
    transition: all 0.3s ease;
    min-height: 120px;
}

.content-textarea:focus {
    outline: none;
    border-color: #f26818;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(242, 104, 24, 0.1);
}

.input-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.char-count {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
}

.btn {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #f26818 0%, #ff8c42 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(242, 104, 24, 0.2);
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(242, 104, 24, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.like-btn {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: transparent !important;
    border: none !important;
    padding: 6px 12px !important;
    border-radius: 20px !important;
    cursor: pointer !important;
    color: #6b7280 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    font-size: 14px !important;
    font-weight: 500 !important;
}

.like-btn:hover {
    background: rgba(242, 104, 24, 0.1) !important;
    color: #f26818 !important;
    transform: translateY(-1px) !important;
}

.like-btn.liked {
    color: #f26818 !important;
}

.like-btn.liked:hover {
    background: rgba(242, 104, 24, 0.15) !important;
}

.like-btn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    transform: none !important;
}

.like-icon {
    width: 18px !important;
    height: 18px !important;
    transition: all 0.3s ease !important;
}

.like-btn:hover .like-icon {
    transform: scale(1.2) !important;
}

.like-count {
    font-size: 13px !important;
    font-weight: 600 !important;
    min-width: 20px !important;
    text-align: center !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Like system initialized for Dashboard page');
    
    let isProcessing = false;
    
    function setupLikeButtons() {
        const likeButtons = document.querySelectorAll('.like-btn');
        console.log(`Found ${likeButtons.length} like buttons in dashboard`);
        
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

    const editPostModal = document.getElementById('editPostModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editPostForm = document.getElementById('editPostForm');
    const editContent = document.getElementById('edit_content');
    const editCharCount = document.getElementById('edit_charCount');
    const editImageInput = document.getElementById('edit_image');
    const editImageUploadArea = document.getElementById('edit_imageUploadArea');
    const editImagePreview = document.getElementById('edit_imagePreview');
    const editPreviewImage = document.getElementById('edit_previewImage');
    const editRemoveImageBtn = document.getElementById('edit_removeImageBtn');
    const currentImageSection = document.getElementById('currentImageSection');
    const currentImage = document.getElementById('currentImage');
    const removeCurrentImageBtn = document.getElementById('removeCurrentImageBtn');
    const removeCurrentImageInput = document.getElementById('remove_current_image');
    const updatePostBtn = document.getElementById('updatePostBtn');
    const notification = document.getElementById('notification');

    let currentPostId = null;
    let hasExistingImage = false;

    if (editContent) {
        editContent.addEventListener('input', function() {
            const length = this.value.length;
            editCharCount.textContent = `${length}/255`;
            
            if (length > 200) {
                editCharCount.style.color = '#f26818';
            } else if (length > 150) {
                editCharCount.style.color = '#f59e0b';
            } else {
                editCharCount.style.color = '#6b7280';
            }
        });
    }

    document.querySelectorAll('.edit-post-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const content = this.getAttribute('data-content');
            const image = this.getAttribute('data-image');
            
            openEditModal(postId, content, image);
        });
    });

    function openEditModal(postId, content, image) {
        currentPostId = postId;
        
        document.getElementById('edit_post_id').value = postId;
        editContent.value = content;
        
        editContent.dispatchEvent(new Event('input'));
        
        hasExistingImage = image !== '';
        removeCurrentImageInput.value = '0';
        
        if (hasExistingImage) {
            currentImage.src = '/' + image;
            currentImageSection.style.display = 'block';
            editImageUploadArea.style.display = 'flex';
            editImagePreview.style.display = 'none';
        } else {
            currentImageSection.style.display = 'none';
            editImageUploadArea.style.display = 'flex';
            editImagePreview.style.display = 'none';
        }
        
        editImageInput.value = '';
        
        editPostModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        editPostModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        currentPostId = null;
        hasExistingImage = false;
    }

    if (closeEditModal) closeEditModal.addEventListener('click', closeModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeModal);

    if (editPostModal) {
        editPostModal.addEventListener('click', function(e) {
            if (e.target === editPostModal) {
                closeModal();
            }
        });
    }

    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('File size must be less than 5MB', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    editPreviewImage.src = e.target.result;
                    editImagePreview.style.display = 'block';
                    editImageUploadArea.style.display = 'none';
                    
                    if (hasExistingImage) {
                        currentImageSection.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (editRemoveImageBtn) {
        editRemoveImageBtn.addEventListener('click', function() {
            editImageInput.value = '';
            editImagePreview.style.display = 'none';
            editImageUploadArea.style.display = 'flex';
            
            if (hasExistingImage) {
                currentImageSection.style.display = 'block';
            }
        });
    }

    if (removeCurrentImageBtn) {
        removeCurrentImageBtn.addEventListener('click', function() {
            removeCurrentImageInput.value = '1';
            currentImageSection.style.display = 'none';
            hasExistingImage = false;
        });
    }

    if (editImageUploadArea) {
        editImageUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        editImageUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });

        editImageUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                editImageInput.files = files;
                editImageInput.dispatchEvent(new Event('change'));
            }
        });
    }

    if (editPostForm) {
        editPostForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            updatePostBtn.disabled = true;
            updatePostBtn.innerHTML = `
                <span class="btn-content">
                    <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Updating Post...
                </span>
            `;

            try {
                const response = await fetch('/posts/update', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showNotification('Post updated successfully!', 'success');
                    
                    const postContentElement = document.getElementById(`post-content-${currentPostId}`);
                    if (postContentElement) {
                        postContentElement.textContent = formData.get('content');
                    }
                    
                    const postImageElement = document.getElementById(`post-image-${currentPostId}`);
                    if (result.image_url) {
                        if (!postImageElement) {
                            const postElement = document.getElementById(`post-${currentPostId}`);
                            const postContentDiv = postElement.querySelector('.post-content');
                            const imageHtml = `<div class="post-image" id="post-image-${currentPostId}"><img src="${result.image_url}" alt="Post image"></div>`;
                            postContentDiv.insertBefore(createElementFromHTML(imageHtml), postContentDiv.querySelector('.post-interactions'));
                        } else {
                            postImageElement.querySelector('img').src = result.image_url;
                        }
                    } else {
                        if (postImageElement) {
                            postImageElement.remove();
                        }
                    }
                    
                    const postDateElement = document.querySelector(`#post-${currentPostId} .post-date`);
                    if (postDateElement && !postDateElement.querySelector('.edited-badge')) {
                        const editedBadge = document.createElement('span');
                        editedBadge.className = 'edited-badge';
                        editedBadge.textContent = '(Edited)';
                        postDateElement.appendChild(editedBadge);
                    }
                    
                    setTimeout(() => {
                        closeModal();
                    }, 1500);
                    
                } else {
                    showNotification(result.message || 'Failed to update post', 'error');
                }
            } catch (error) {
                console.error('Error updating post:', error);
                showNotification('Network error: ' + error.message, 'error');
            } finally {
                updatePostBtn.disabled = false;
                updatePostBtn.innerHTML = `
                    <span class="btn-content">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Post
                    </span>
                `;
            }
        });
    }

    function createElementFromHTML(htmlString) {
        const div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div.firstChild;
    }

    function showNotification(message, type) {
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>