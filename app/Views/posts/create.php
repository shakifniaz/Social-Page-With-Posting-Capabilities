<?php
$title = 'Create Post | MetroPost';
ob_start();
?>
<div class="container">
    <div class="create-post-header">
        <div class="header-content">
            <h2>Create New Post</h2>
        </div>
        <a href="/posts" class="btn btn-secondary back-btn">
            <span class="btn-content">
                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Posts
            </span>
            <span class="btn-hover-effect"></span>
        </a>
    </div>

    <div class="create-post-card">
        <form method="POST" action="/posts/create" class="create-post-form" enctype="multipart/form-data" id="postForm">
            <div class="user-preview">
                <div class="user-avatar-preview">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div class="user-info-preview">
                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                </div>
            </div>

            <div class="form-group">
                <div class="input-header">
                    <label for="content" class="form-label">What's on your mind?</label>
                    <span class="char-count" id="charCount">0/255</span>
                </div>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="4" 
                    required 
                    maxlength="255" 
                    placeholder="Share your thoughts, ideas, or experiences..."
                    class="content-textarea"
                ></textarea>
                
            </div>

            <div class="form-group">
                <label class="form-label">Add Image (Optional)</label>
                <div class="image-upload-container">
                    <input type="file" name="image" id="image" accept="image/*" class="image-input" hidden>
                    <label for="image" class="image-upload-area" id="imageUploadArea">
                        <div class="upload-content">
                            <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="upload-text">
                                <strong>Click to upload image</strong>
                                <span>PNG, JPG, GIF up to 5MB</span>
                            </div>
                        </div>
                    </label>
                    <div class="image-preview" id="imagePreview" style="display: none;">
                        <img id="previewImage" src="" alt="Preview">
                        <button type="button" class="remove-image-btn" id="removeImageBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">
                    <span class="btn-content">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Create Post
                    </span>
                    <span class="btn-hover-effect"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    const imageInput = document.getElementById('image');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const submitBtn = document.getElementById('submitBtn');
    const postForm = document.getElementById('postForm');

    contentTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = `${length}/255`;
        
        if (length > 200) {
            charCount.style.color = '#f26818';
        } else if (length > 150) {
            charCount.style.color = '#f59e0b';
        } else {
            charCount.style.color = '#6b7280';
        }
    });

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
                imageUploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.style.display = 'none';
        imageUploadArea.style.display = 'flex';
    });

    imageUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });

    imageUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
    });

    imageUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });

    postForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="btn-content">
                <svg class="btn-icon animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating Post...
            </span>
        `;
    });
});
</script>

<style>
.create-post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding: 16px 0;
}

.header-content h2 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: 700;
    color: #1c1e21;
}

.subtitle {
    margin: 0;
    color: #6b7280;
    font-size: 16px;
    font-weight: 400;
}

.back-btn {
    background: #6b7280;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
}

.back-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.create-post-card {
    background: #fff;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.user-preview {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #f1f5f9;
}

.user-avatar-preview {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f26818 0%, #ff8c42 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 2px 8px rgba(242, 104, 24, 0.2);
}

.user-info-preview {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.user-info-preview strong {
    color: #1c1e21;
    font-size: 16px;
    font-weight: 600;
}

.post-preview-text {
    color: #6b7280;
    font-size: 14px;
}

.form-group {
    margin-bottom: 24px;
}

.input-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.form-label {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.char-count {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
    transition: color 0.3s ease;
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

.content-textarea::placeholder {
    color: #9ca3af;
}

.input-footer {
    margin-top: 8px;
}

.input-hint {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6b7280;
    font-size: 14px;
}

.hint-icon {
    width: 16px;
    height: 16px;
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
    max-height: 300px;
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

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
}

.submit-btn {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #f26818 0%, #ff8c42 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(242, 104, 24, 0.2);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(242, 104, 24, 0.3);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .create-post-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    .header-content h2 {
        font-size: 24px;
    }
    
    .create-post-card {
        padding: 24px;
        border-radius: 12px;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .back-btn,
    .submit-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .create-post-card {
        padding: 20px;
        border-radius: 10px;
    }
    
    .user-preview {
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    
    .user-avatar-preview {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .content-textarea {
        padding: 12px;
        font-size: 14px;
    }
    
    .image-upload-area {
        padding: 30px 20px;
    }
    
    .upload-icon {
        width: 40px;
        height: 40px;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';