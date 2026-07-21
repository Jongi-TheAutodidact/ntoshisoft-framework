<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Rate Limiting Middleware
 * 
 * Prevents brute force attacks by limiting request frequency
 */
class RateLimitMiddleware extends Middleware
{
    private $maxRequests;
    private $windowMinutes;
    private $identifier;
    
    public function __construct(int $maxRequests = 60, int $windowMinutes = 1, string $identifier = null)
    {
        $this->maxRequests = $maxRequests;
        $this->windowMinutes = $windowMinutes;
        $this->identifier = $identifier ?? $this->getDefaultIdentifier();
    }
    
    /**
     * Handle rate limiting
     *
     * @param array $request The current request data
     * @param callable $next The next middleware or controller
     * @return mixed
     */
    public function handle(array $request, callable $next): mixed
    {
        if (!$this->checkRateLimit()) {
            $this->denyAccess();
        }
        
        return $next($request);
    }
    
    /**
     * Check if rate limit is exceeded
     */
    private function checkRateLimit(): bool
    {
        $key = 'rate_limit_' . md5($this->identifier);
        $now = time();
        $window = $this->windowMinutes * 60;
        
        // Get existing requests from session
        $requests = $_SESSION[$key] ?? [];
        
        // Clean old requests outside the window
        $requests = array_filter($requests, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        // Check if limit exceeded
        if (count($requests) >= $this->maxRequests) {
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        $_SESSION[$key] = $requests;
        
        // Log rate limiting for security monitoring
        if (count($requests) > $this->maxRequests * 0.8) {
            log_security('RATE_LIMIT_WARNING', [
                'identifier' => $this->identifier,
                'requests' => count($requests),
                'max_requests' => $this->maxRequests,
                'window_minutes' => $this->windowMinutes
            ]);
        }
        
        return true;
    }
    
    /**
     * Get default identifier based on IP and user
     */
    private function getDefaultIdentifier(): string
    {
        $ip = get_ip();
        $userId = $_SESSION['id'] ?? 'guest';
        
        return $ip . ':' . $userId . ':' . $_SERVER['REQUEST_URI'] ?? '';
    }
    
    /**
     * Deny access with proper headers
     */
    private function denyAccess(): void
    {
        // Set rate limit headers
        $resetTime = time() + ($this->windowMinutes * 60);
        
        if (!headers_sent()) {
            header('X-RateLimit-Limit: ' . $this->maxRequests);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . $resetTime);
            header('Retry-After: ' . ($this->windowMinutes * 60));
        }
        
        // Log the rate limit breach
        log_security('RATE_LIMIT_EXCEEDED', [
            'identifier' => $this->identifier ?? 'unknown',
            'max_requests' => $this->maxRequests,
            'window_minutes' => $this->windowMinutes,
            'ip' => get_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
        if ($this->isApiRequest()) {
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $this->windowMinutes * 60,
                'reset_time' => $resetTime
            ]);
            exit;
        } else {
            $this->showRateLimitPage();
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
     * Show rate limit exceeded page
     */
    private function showRateLimitPage(): void
    {
        $resetTime = time() + ($this->windowMinutes * 60);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Rate Limit Exceeded - <?= APP_NAME ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .countdown { font-size: 2rem; font-weight: bold; color: #dc3545; }
                .retry-time { font-size: 1.2rem; color: #6c757d; }
            </style>
        </head>
        <body class="bg-light">
            <div class="container">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center p-5">
                                <div class="text-warning mb-4">
                                    <i class="bi bi-speedometer2" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="card-title text-warning mb-3">Rate Limit Exceeded</h3>
                                <p class="card-text mb-4">
                                    You've made too many requests. Please wait before trying again.
                                </p>
                                
                                <div class="alert alert-info mb-4">
                                    <div class="countdown" id="countdown">--:--</div>
                                    <div class="retry-time">minutes until you can try again</div>
                                </div>
                                
                                <div class="mb-4">
                                    <small class="text-muted">
                                        Limit: <?= $this->maxRequests ?> requests per <?= $this->windowMinutes ?> minute(s)<br>
                                        IP: <?= get_ip() ?>
                                    </small>
                                </div>
                                
                                <a href="javascript:location.reload()" class="btn btn-primary">Refresh</a>
                                <a href="<?= ROOT ?>" class="btn btn-secondary">Go Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                // Countdown timer
                const resetTime = <?= $resetTime ?> * 1000;
                
                function updateCountdown() {
                    const now = Date.now();
                    const diff = resetTime - now;
                    
                    if (diff <= 0) {
                        document.getElementById('countdown').textContent = 'Ready';
                        document.getElementById('countdown').className = 'countdown text-success';
                        setTimeout(() => location.reload(), 1000);
                        return;
                    }
                    
                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);
                    
                    document.getElementById('countdown').textContent = 
                        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }
                
                updateCountdown();
                setInterval(updateCountdown, 1000);
            </script>
        </body>
        </html>
        <?php
    }
}

/**
 * Authentication Rate Limiting Middleware
 * 
 * Specialized rate limiting for authentication endpoints
 */
class AuthRateLimitMiddleware extends RateLimitMiddleware
{
    public function __construct(int $maxAttempts = 5, int $windowMinutes = 15)
    {
        // Use stricter limits for authentication
        parent::__construct($maxAttempts, $windowMinutes);
        $this->identifier = get_ip() . ':auth';
    }
}