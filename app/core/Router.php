<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute(string $method, string $uri, array $action): void
    {
        $this->routes[$method][rtrim($uri, '/') ?: '/'] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';
        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            exit('Ruta no encontrada.');
        }

        [$controllerClass, $controllerMethod] = $action;
        $controller = new $controllerClass();
        $controller->{$controllerMethod}();
    }
}
