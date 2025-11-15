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
        return $stmt->fetchAll();
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
        return $stmt->fetchAll();
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

    public static function updatePostWithImage(int $postId, int $userId, string $content, ?string $image = null): bool {
        $pdo = self::connect();
        
        try {
            $pdo->beginTransaction();
            
            if ($image === null) {
                $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = NULL, edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$content, $postId, $userId]);
            } else {
                $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = ?, edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$content, $image, $postId, $userId]);
            }
            
            $pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Post update error: " . $e->getMessage());
            return false;
        }
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
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function userHasLiked(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool)$stmt->fetch();
    }

    public static function getLikeCount(int $postId): int {
        $stmt = self::connect()->prepare('SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        $result = $stmt->fetch();
        return (int)($result['like_count'] ?? 0);
    }

    public static function addLike(int $postId, int $userId): bool {
        try {
            $stmt = self::connect()->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)');
            return $stmt->execute([$postId, $userId]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public static function removeLike(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }

    public static function getAllWithUserLikeStatus(int $userId): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name, 
                   COUNT(pl.id) as likes,
                   EXISTS(SELECT 1 FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_has_liked
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN post_likes pl ON p.id = pl.post_id
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll();
        
        foreach ($posts as &$post) {
            $post['user_has_liked'] = (bool)$post['user_has_liked'];
            $post['likes'] = (int)$post['likes'];
        }
        
        return $posts;
    }

    public static function findUserPostsWithLikeStatus(int $userId): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name, 
                   COUNT(pl.id) as likes,
                   EXISTS(SELECT 1 FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_has_liked
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN post_likes pl ON p.id = pl.post_id
            WHERE p.user_id = ?
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$userId, $userId]);
        $posts = $stmt->fetchAll();
        
        foreach ($posts as &$post) {
            $post['user_has_liked'] = (bool)$post['user_has_liked'];
            $post['likes'] = (int)$post['likes'];
        }
        
        return $posts;
    }

    public static function getLikesForPosts(array $postIds, int $userId): array {
        if (empty($postIds)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($postIds) - 1) . '?';
        $stmt = self::connect()->prepare("
            SELECT p.id as post_id, 
                   COUNT(pl.id) as likes,
                   EXISTS(SELECT 1 FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_has_liked
            FROM posts p 
            LEFT JOIN post_likes pl ON p.id = pl.post_id
            WHERE p.id IN ($placeholders)
            GROUP BY p.id
        ");
        
        $params = array_merge([$userId], $postIds);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        $likesData = [];
        
        foreach ($results as $result) {
            $likesData[] = [
                'post_id' => (int)$result['post_id'],
                'likes' => (int)$result['likes'],
                'user_has_liked' => (bool)$result['user_has_liked']
            ];
        }
        
        return $likesData;
    }

    public static function toggleUserLike(int $postId, int $userId): array {
        $pdo = self::connect();
        
        try {
            $pdo->beginTransaction();
            
            $checkStmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
            $checkStmt->execute([$postId, $userId]);
            $existingLike = $checkStmt->fetch();
            
            if ($existingLike) {
                $deleteStmt = $pdo->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
                $deleteStmt->execute([$postId, $userId]);
                $liked = false;
            } else {
                $insertStmt = $pdo->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)');
                $insertStmt->execute([$postId, $userId]);
                $liked = true;
            }
            
            $countStmt = $pdo->prepare('SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?');
            $countStmt->execute([$postId]);
            $result = $countStmt->fetch();
            $likes = (int)($result['like_count'] ?? 0);
            
            $pdo->commit();
            
            return [
                'liked' => $liked,
                'likes' => $likes,
                'success' => true
            ];
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Like toggle error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getUserLikeStatus(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool)$stmt->fetch();
    }

    public static function updatePostFull(int $postId, int $userId, string $content, ?string $imagePath = null): bool {
        $pdo = self::connect();
        
        try {
            $pdo->beginTransaction();
            
            if ($imagePath === null) {
                $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = NULL, edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$content, $postId, $userId]);
            } else if ($imagePath === '') {
                $stmt = $pdo->prepare('UPDATE posts SET content = ?, edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$content, $postId, $userId]);
            } else {
                $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = ?, edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$content, $imagePath, $postId, $userId]);
            }
            
            $pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Post update error: " . $e->getMessage());
            return false;
        }
    }
}
?>