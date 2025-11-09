<?php
namespace App\Models;

use PDO;
use PDOException;

class Post {
    private static function connect(): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db = getenv('DB_NAME') ?: 'metro_web_class';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw $e;
        }
    }

    public static function create(int $userId, string $content, ?string $image = null): int {
        $pdo = self::connect();
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $content, $image]);
        return (int)$pdo->lastInsertId();
    }

    public static function getAllWithUsers(): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ');
        $stmt->execute();
        $posts = $stmt->fetchAll();
        
        foreach ($posts as &$post) {
            $post['user_has_liked'] = self::getUserLikeStatus($post['id'], $post['user_id']);
        }
        
        return $posts;
    }

    public static function findByUserId(int $userId): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.user_id = ? 
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll();
        
        foreach ($posts as &$post) {
            $post['user_has_liked'] = self::getUserLikeStatus($post['id'], $userId);
        }
        
        return $posts;
    }

    public static function delete(int $postId, int $userId): bool {
        $pdo = self::connect();
        
        $deleteLikesStmt = $pdo->prepare('DELETE FROM post_likes WHERE post_id = ?');
        $deleteLikesStmt->execute([$postId]);
        
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }

    public static function findByIdAndUserId(int $postId, int $userId): ?array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ? AND p.user_id = ?
        ');
        $stmt->execute([$postId, $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateContent(int $postId, int $userId, string $content): bool {
        $stmt = self::connect()->prepare('UPDATE posts SET content = ?, edited = 1 WHERE id = ? AND user_id = ?');
        return $stmt->execute([$content, $postId, $userId]);
    }

    public static function toggleLike(int $postId, int $userId): array {
        $pdo = self::connect();
        
        $checkStmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $checkStmt->execute([$postId, $userId]);
        $existingLike = $checkStmt->fetch();
        
        if ($existingLike) {
            $deleteStmt = $pdo->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
            $deleteStmt->execute([$postId, $userId]);
            
            $updateStmt = $pdo->prepare('UPDATE posts SET likes = GREATEST(0, likes - 1) WHERE id = ?');
            $updateStmt->execute([$postId]);
            
            $liked = false;
        } else {
            $insertStmt = $pdo->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)');
            $insertStmt->execute([$postId, $userId]);
            
            $updateStmt = $pdo->prepare('UPDATE posts SET likes = likes + 1 WHERE id = ?');
            $updateStmt->execute([$postId]);
            
            $liked = true;
        }
        
        $countStmt = $pdo->prepare('SELECT likes FROM posts WHERE id = ?');
        $countStmt->execute([$postId]);
        $likes = (int)$countStmt->fetchColumn();
        
        return ['liked' => $liked, 'likes' => $likes];
    }

    public static function getUserLikeStatus(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool)$stmt->fetch();
    }

    public static function getPaginatedWithUsers(int $page = 1, int $perPage = 10): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ');
        // Use integers explicitly
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        foreach ($posts as &$post) {
            $post['user_has_liked'] = self::getUserLikeStatus($post['id'], $post['user_id']);
        }

        return $posts;
    }
}