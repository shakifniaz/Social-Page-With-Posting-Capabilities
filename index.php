<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Session.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/PostController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\DashboardController;

Session::start();

$router = new Router();

error_log("Setting up routes...");

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

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/', [DashboardController::class, 'index']);

error_log("Routes setup complete");

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

error_log("Incoming request: $method $uri");

$router->dispatch($uri, $method);