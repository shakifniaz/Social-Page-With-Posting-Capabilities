<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, $callback): void {
        $this->routes['GET'][$path] = $callback;
        error_log("Registered GET route: $path");
    }

    public function post(string $path, $callback): void {
        $this->routes['POST'][$path] = $callback;
        error_log("Registered POST route: $path");
    }

    public function dispatch(string $uri, string $method): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        
        error_log("Dispatching: $method $path");
        
        $callback = $this->routes[$method][$path] ?? null;
        
        if (!$callback) {
            error_log("Available POST routes: " . implode(', ', array_keys($this->routes['POST'] ?? [])));
            error_log("Available GET routes: " . implode(', ', array_keys($this->routes['GET'] ?? [])));
            
            http_response_code(404);
            echo "<h1>404 Not Found</h1>";
            echo "<p>Route not found: $method $path</p>";
            return;
        }

        if (is_array($callback) && count($callback) === 2) {
            [$class, $method] = $callback;
            if (class_exists($class)) {
                $controller = new $class();
                if (method_exists($controller, $method)) {
                    echo call_user_func([$controller, $method]);
                    return;
                } else {
                    error_log("Method $method not found in $class");
                    http_response_code(500);
                    echo "<h1>500 Internal Server Error</h1>";
                    echo "<p>Method $method not found in controller</p>";
                    return;
                }
            } else {
                error_log("Class $class not found");
                http_response_code(500);
                echo "<h1>500 Internal Server Error</h1>";
                echo "<p>Controller class $class not found</p>";
                return;
            }
        }

        echo call_user_func($callback);
    }

    public function redirect(string $path): void {
        header("Location: $path");
        exit;
    }
}