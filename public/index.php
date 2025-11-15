<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../app/' . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\DashboardController;

Session::start();

$router = new Router();

$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/posts', [PostController::class, 'showPosts']);
$router->get('/posts/create', [PostController::class, 'showCreatePost']);
$router->post('/posts/create', [PostController::class, 'createPost']);
$router->post('/posts/delete', [PostController::class, 'deletePost']);
$router->post('/posts/update', [PostController::class, 'updatePost']);
$router->post('/posts/like', [PostController::class, 'toggleLike']);
$router->post('/posts/get-likes', [PostController::class, 'getLikes']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/', [DashboardController::class, 'index']);

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);