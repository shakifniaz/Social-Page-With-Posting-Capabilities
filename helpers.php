<?php
function url(string $path = ''): string {
    $basePath = rtrim(getenv('BASE_PATH') ?: '', '/');
    $path = '/' . ltrim($path, '/');
    return $basePath . $path;
}
