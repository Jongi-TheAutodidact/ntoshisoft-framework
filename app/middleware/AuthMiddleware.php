<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Authentication Middleware
 * 
 * Handles user authentication and authorization
 */
class AuthMiddleware extends Middleware
{
    /**
     * Handle authentication request
     *
     * @param array $request The current request data
     * @param callable $next The next middleware or controller
     * @return mixed
     */
    public function handle(array $request, callable $next): mixed
    {
        // Load user model for authentication checks
        $user = new User();
        
        // Check if user is authenticated
        if (!$user->logged_in()) {
            // Store intended URL for redirect after login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            // Redirect to login page
            if ($this->isApiRequest()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect' => ROOT . '/login'
                ]);
                exit;
            } else {
                redirect('login');
            }
        }
        
        // Additional security checks
        $this->validateSession();
        $this->checkCsrfToken();
        
        // Pass control to next middleware/controller
        return $next($request);
    }
    
    /**
     * Check if this is an API request
     */
    private function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) && 
               str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }
    
    /**
     * Validate session for security
     */
    private function validateSession(): void
    {
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > SESSION_LIFETIME * 60)) {
            
            // Session expired, destroy and redirect
            session_destroy();
            session_start();
            
            if ($this->isApiRequest()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Session expired',
                    'redirect' => ROOT . '/login'
                ]);
                exit;
            } else {
                redirect('login');
            }
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID periodically (session fixation protection)
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // Every 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    /**
     * Validate CSRF token for POST/PUT/DELETE requests
     */
    private function checkCsrfToken(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                if ($this->isApiRequest()) {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid CSRF token'
                    ]);
                    exit;
                } else {
                    // Show CSRF error page
                    http_response_code(403);
                    $this->showCsrfError();
                    exit;
                }
            }
        }
    }
    
    /**
     * Show CSRF error page
     */
    private function showCsrfError(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Security Error - <?= APP_NAME ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center p-5">
                                <div class="text-danger mb-4">
                                    <i class="bi bi-shield-exclamation" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="card-title text-danger mb-3">Security Error</h3>
                                <p class="card-text mb-4">
                                    Invalid security token detected. This may be due to session timeout or 
                                    attempting to submit a form twice.
                                </p>
                                <a href="<?= ROOT ?>/login" class="btn btn-primary">Return to Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}