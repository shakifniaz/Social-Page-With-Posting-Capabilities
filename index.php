<?php
// Enable error reporting for debugging
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

// Start session
Session::start();

$router = new Router();

// Debug: log that we're setting up routes
error_log("Setting up routes...");

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Post routes
$router->get('/posts', [PostController::class, 'showPosts']);
$router->get('/posts/create', [PostController::class, 'showCreatePost']);
$router->post('/posts/create', [PostController::class, 'createPost']);
$router->post('/posts/delete', [PostController::class, 'deletePost']);
$router->post('/posts/update', [PostController::class, 'updatePost']); // ADDED THIS LINE
$router->post('/posts/like', [PostController::class, 'toggleLike']);

// Dashboard route
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/', [DashboardController::class, 'index']);

// Debug: log that routes are set up
error_log("Routes setup complete");

// Dispatch the request
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Debug: log the incoming request
error_log("Incoming request: $method $uri");

$router->dispatch($uri, $method);