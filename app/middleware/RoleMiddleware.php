<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Role-based Authorization Middleware
 * 
 * Restricts access based on user roles
 */
class RoleMiddleware extends Middleware
{
    private $allowedRoles;
    
    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }
    
    /**
     * Handle role-based authorization
     *
     * @param array $request The current request data
     * @param callable $next The next middleware or controller
     * @return mixed
     */
    public function handle(array $request, callable $next): mixed
    {
        // First check if user is logged in
        $user = new User();
        if (!$user->logged_in()) {
            if ($this->isApiRequest()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required'
                ]);
                exit;
            } else {
                redirect('auth/login');
            }
        }
        
        // Now check roles
        $userRole = $_SESSION['userRole'] ?? '';
        
        if (!in_array($userRole, $this->allowedRoles)) {
            $this->denyAccess();
        }
        
        // Validate session
        $this->validateSession();
        
        return $next($request);
    }
    
    /**
     * Validate session for security
     */
    private function validateSession(): void
    {
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > SESSION_LIFETIME * 60)) {
            
            session_destroy();
            session_start();
            redirect('auth/login');
        }
        
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Deny access to unauthorized users
     */
    private function denyAccess(): void
    {
        if ($this->isApiRequest()) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied. Insufficient permissions.'
            ]);
            exit;
        } else {
            $this->showAccessDeniedPage();
            exit;
        }
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
     * Show access denied page
     */
    private function showAccessDeniedPage(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied - <?= APP_NAME ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center p-5">
                                <div class="text-warning mb-4">
                                    <i class="bi bi-shield-x" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="card-title text-warning mb-3">Access Denied</h3>
                                <p class="card-text mb-4">
                                    You don't have permission to access this resource.
                                    <br>
                                    <small class="text-muted">Required role: <?= implode(', ', $this->allowedRoles) ?></small>
                                </p>
                                <a href="javascript:history.back()" class="btn btn-secondary me-2">Go Back</a>
                                <a href="<?= ROOT ?>/admin" class="btn btn-primary">Dashboard</a>
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