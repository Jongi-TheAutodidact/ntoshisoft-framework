<?php
declare(strict_types=1);

class Router {
    private array $routes;

    public function __construct(array $routes) {
        $this->routes = $routes;
    }

    public function dispatch(string $url): void {
        $this->processRoute($url, $this->routes);
    }

    private function processRoute(string $url, array $routes, array $parentMiddleware = []): void {
        foreach ($routes as $route => $config) {
            // Handle route groups
            if (isset($config['group'])) {
                $groupMiddleware = $config['middleware'] ?? [];
                $this->processRoute($url, $config['group'], array_merge($parentMiddleware, $groupMiddleware));
                continue;
            }

            // Convert the route to a regex to match parameters
            $routeRegex = preg_replace('/{([^}]+)}/', '([^/]+)', $route);
            $routeRegex = '#^' . $routeRegex . '$#';

            if (preg_match($routeRegex, $url, $matches)) {
                // Extract parameters
                array_shift($matches); // Remove the full match
                
                if (is_array($config) && isset($config['controller'])) {
                    [$controller, $method] = $config['controller'];
                    $routeMiddleware = $config['middleware'] ?? [];
                } else {
                    [$controller, $method] = $config;
                    $routeMiddleware = [];
                }

                // Combine parent (group) middleware with route middleware
                $middlewareStack = array_merge($parentMiddleware, $routeMiddleware);

                $next = function () use ($controller, $method, $matches) {
                    $controllerFile = "../app/controllers/{$controller}.php";
                    if (!file_exists($controllerFile)) {
                        require '../app/views/404.ntoshi.php';
                        return;
                    }

                    require_once $controllerFile;
                    $controllerInstance = new $controller();

                    if (method_exists($controllerInstance, $method)) {
                        call_user_func_array([$controllerInstance, $method], $matches);
                    } else {
                        require '../app/views/404.ntoshi.php';
                    }
                };

                // Execute middleware stack
                $this->executeMiddleware($middlewareStack, ['url' => $url], $next);
                return;
            }
        }

        // Default 404 handler
        require '../app/views/404.ntoshi.php';
    }

    private function loadMiddleware(array $middleware): array {
        $middlewareStack = [];
        foreach ($middleware as $mw) {
            $middlewareFile = "../app/middleware/{$mw}.php";
            if (file_exists($middlewareFile)) {
                require_once $middlewareFile;
                if (class_exists($mw)) {
                    $middlewareStack[] = new $mw();
                }
            }
        }
        return $middlewareStack;
    }

    private function executeMiddleware(array $middlewareStack, array $request, callable $next): void {
        $middlewareStack = $this->loadMiddleware($middlewareStack);
        
        $pipeline = array_reduce(array_reverse($middlewareStack), function ($nextLayer, $middleware) {
            return function ($request) use ($nextLayer, $middleware) {
                return $middleware->handle($request, $nextLayer);
            };
        }, $next);

        $pipeline($request);
    }
}