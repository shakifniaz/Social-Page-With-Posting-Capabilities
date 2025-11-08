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

        $posts = Post::getAllWithUsers();
        
        foreach ($posts as &$post) {
            $post['user_has_liked'] = Post::getUserLikeStatus($post['id'], $user['id']);
        }
        
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
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['content'] ?? '');

        if (!$postId || empty($content)) {
            Session::set('error', "Post ID and content are required.");
            header('Location: /dashboard');
            exit;
        }

        $post = Post::findByIdAndUserId($postId, $user['id']);
        if (!$post) {
            Session::set('error', "Post not found or you don't have permission to edit it.");
            header('Location: /dashboard');
            exit;
        }

        $updated = Post::updateContent($postId, $user['id'], $content);
        
        if ($updated) {
            Session::set('success', "Post updated successfully.");
        } else {
            Session::set('error', "Failed to update post.");
        }
        
        header('Location: /dashboard');
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
            $result = Post::toggleLike($postId, $user['id']);
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
}