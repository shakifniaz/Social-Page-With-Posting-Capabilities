<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller {
    public function showPosts() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $posts = Post::getAllWithUserLikeStatus($user['id']);
        
        $this->view('posts/posts.php', ['user' => $user, 'posts' => $posts]);
    }

    public function showCreatePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }
        $this->view('posts/create.php', ['user' => $user]);
    }

    public function createPost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $image = $_FILES['image'] ?? null;

        if (empty($content)) {
            echo "Content cannot be empty.";
            return;
        }

        $imagePath = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $imagePath = 'uploads/' . $fileName;

            if (!move_uploaded_file($image['tmp_name'], $uploadDir . $fileName)) {
                echo "Failed to upload image.";
                return;
            }
        }

        Post::create($user['id'], $content, $imagePath);
        header('Location: /posts');
    }

    public function deletePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        
        if (!$postId) {
            echo "Post ID is required.";
            return;
        }

        $post = Post::findByIdAndUserId($postId, $user['id']);
        if (!$post) {
            echo "Post not found or you don't have permission to delete it.";
            return;
        }

        $deleted = Post::delete($postId, $user['id']);
        
        if ($deleted) {
            if ($post['image']) {
                $imagePath = __DIR__ . '/../../public/' . $post['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            header('Location: /dashboard');
        } else {
            echo "Failed to delete post.";
        }
    }

    public function updatePost() {
        header('Content-Type: application/json');
        
        $user = Session::get('user');
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        $removeCurrentImage = $_POST['remove_current_image'] ?? '0';
        $image = $_FILES['image'] ?? null;

        if (!$postId || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Post ID and content are required.']);
            exit;
        }

        // Verify post belongs to user
        $post = Post::findByIdAndUserId($postId, $user['id']);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found or you don\'t have permission to edit it.']);
            exit;
        }

        try {
            $imagePath = ''; // Empty string means keep current image
            $currentImagePath = $post['image'];
            
            // Handle image removal
            if ($removeCurrentImage === '1') {
                // Mark for removal
                $imagePath = null;
                
                // Delete old image file if exists
                if ($currentImagePath) {
                    $oldImagePath = __DIR__ . '/../../public/' . $currentImagePath;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
            
            // Handle new image upload
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                // Validate file size
                if ($image['size'] > 5 * 1024 * 1024) {
                    echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
                    exit;
                }
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($image['type'], $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
                    exit;
                }
                
                // Delete old image if exists
                if ($currentImagePath) {
                    $oldImagePath = __DIR__ . '/../../public/' . $currentImagePath;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Upload new image
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExtension;
                $imagePath = 'uploads/' . $fileName;

                if (!move_uploaded_file($image['tmp_name'], $uploadDir . $fileName)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
                    exit;
                }
            }
            
            // Update post in database
            $updated = Post::updatePostFull($postId, $user['id'], $content, $imagePath);
            
            if ($updated) {
                // Determine the final image URL for the response
                $finalImageUrl = null;
                if ($imagePath === null) {
                    // Image was removed
                    $finalImageUrl = null;
                } else if ($imagePath === '') {
                    // Image was kept (no change)
                    $finalImageUrl = $currentImagePath ? '/' . $currentImagePath : null;
                } else {
                    // New image was uploaded
                    $finalImageUrl = '/' . $imagePath;
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Post updated successfully.',
                    'image_url' => $finalImageUrl
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update post in database.']);
            }
            
        } catch (\Exception $e) {
            error_log('Post update error: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function toggleLike() {
        header('Content-Type: application/json');
        
        $user = Session::get('user');
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Post ID required']);
            exit;
        }

        try {
            $result = Post::toggleUserLike($postId, $user['id']);
            
            echo json_encode([
                'success' => true, 
                'liked' => $result['liked'], 
                'likes' => $result['likes']
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function getLikes() {
        header('Content-Type: application/json');
        
        $user = Session::get('user');
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $postIdsJson = $_POST['post_ids'] ?? '[]';
        $postIds = json_decode($postIdsJson, true) ?? [];
        
        if (empty($postIds)) {
            echo json_encode(['success' => true, 'likes' => []]);
            exit;
        }

        try {
            $likesData = Post::getLikesForPosts($postIds, $user['id']);
            
            echo json_encode([
                'success' => true,
                'likes' => $likesData
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
?>