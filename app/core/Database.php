<?php 
declare(strict_types=1);
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Database Core Class
 * 
 * Secure database connection and query handling with proper error handling
 */
trait Database
{
    private static ?PDO $connection = null;
    private static int $connectionAttempts = 0;
    private static int $maxRetries = 3;
    
    /**
     * Create database connection with proper error handling
     */
    private function connect(): ?PDO
    {
        // Use singleton pattern for connection
        if (self::$connection !== null) {
            return self::$connection;
        }
        
        try {
            // Set String/DSN
            $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4";
            
            // Create PDO Instance/Object with proper options
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false, // Better to use connection pooling
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            self::$connection = new PDO($dsn, DBUSER, DBPASS, $options);
            self::$connectionAttempts = 0;
            
            return self::$connection;
            
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Connection failed');
            return null;
        }
    }
    
    /**
     * Execute database query with proper error handling
     */
    public function query(string $query, array $data = []): array|false
    {
        try {
            $con = $this->connect();
            if (!$con) {
                return false;
            }
            
            $stm = $con->prepare($query);
            
            // Log query for debugging (only in development)
            if (DEBUG) {
                $this->logQuery($query, $data);
            }
            
            $check = $stm->execute($data);
            
            if ($check) {
                $result = $stm->fetchAll(PDO::FETCH_OBJ);
                if (is_array($result) && count($result)) {
                    return $result;
                }
            }
            
            return false;
            
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Query execution failed', $query, $data);
            return false;
        }
    }
    
    /**
     * Get single row from database
     */
    public function get_row(string $query, array $data = []): object|false
    {
        try {
            $con = $this->connect();
            if (!$con) {
                return false;
            }
            
            $stm = $con->prepare($query);
            
            // Log query for debugging (only in development)
            if (DEBUG) {
                $this->logQuery($query, $data);
            }
            
            $check = $stm->execute($data);
            
            if ($check) {
                $result = $stm->fetchAll(PDO::FETCH_OBJ);
                if (is_array($result) && count($result)) {
                    return $result[0];
                }
            }
            
            return false;
            
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Get row execution failed', $query, $data);
            return false;
        }
    }
    
    /**
     * Get the last inserted ID
     */
    public function lastInsertId(): string|false
    {
        try {
            $con = $this->connect();
            return $con ? $con->lastInsertId() : false;
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Failed to get last insert ID');
            return false;
        }
    }
    
    /**
     * Begin database transaction
     */
    public function beginTransaction(): bool
    {
        try {
            $con = $this->connect();
            if ($con && !$con->inTransaction()) {
                return $con->beginTransaction();
            }
            return false;
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Transaction start failed');
            return false;
        }
    }
    
    /**
     * Commit database transaction
     */
    public function commit(): bool
    {
        try {
            $con = $this->connect();
            if ($con && $con->inTransaction()) {
                return $con->commit();
            }
            return false;
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Transaction commit failed');
            return false;
        }
    }
    
    /**
     * Rollback database transaction
     */
    public function rollback(): bool
    {
        try {
            $con = $this->connect();
            if ($con && $con->inTransaction()) {
                return $con->rollBack();
            }
            return false;
        } catch (PDOException $e) {
            $this->handleDatabaseError($e, 'Transaction rollback failed');
            return false;
        }
    }
    
    /**
     * Handle database errors securely
     */
    private function handleDatabaseError(PDOException $exception, string $context = '', string $query = '', array $data = []): void
    {
        self::$connectionAttempts++;
        
        // Log detailed error information
        $errorInfo = [
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'context' => $context,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'query' => DEBUG ? $query : '[HIDDEN]',
            'data' => DEBUG ? $data : '[HIDDEN]',
            'attempts' => self::$connectionAttempts
        ];
        
        // Log to file
        $this->logError($errorInfo);
        
        // Show user-friendly error
        if (DEBUG) {
            // Development: show detailed error
            $message = "Database Error: {$context}<br>";
            $message .= "Error: " . $exception->getMessage() . "<br>";
            $message .= "File: " . $exception->getFile() . " (Line: " . $exception->getLine() . ")";
            
            $this->showErrorPage($message, 500);
        } else {
            // Production: show generic error
            $this->showErrorPage('Database connection error. Please try again later.', 500);
        }
        
        // Retry connection if it's a connection error
        if (self::$connectionAttempts <= self::$maxRetries && 
            str_contains($exception->getMessage(), 'connection')) {
            self::$connection = null;
        }
    }
    
    /**
     * Log error to file
     */
    private function logError(array $errorInfo): void
    {
        $logFile = ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'database.log';
        $logDir = dirname($logFile);
        
        // Create logs directory if it doesn't exist
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Format log entry
        $logEntry = "[" . $errorInfo['timestamp'] . "] " .
                   strtoupper($errorInfo['context']) . ": " .
                   $errorInfo['error'] .
                   " (Code: " . $errorInfo['code'] . ")" .
                   " in " . $errorInfo['file'] . ":" . $errorInfo['line'] . "\n";
        
        if (!empty($errorInfo['query'])) {
            $logEntry .= "Query: " . $errorInfo['query'] . "\n";
        }
        
        if (!empty($errorInfo['data'])) {
            $logEntry .= "Data: " . json_encode($errorInfo['data']) . "\n";
        }
        
        $logEntry .= "---\n";
        
        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log query for debugging
     */
    private function logQuery(string $query, array $data): void
    {
        $logFile = ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'queries.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = "[" . date('Y-m-d H:i:s') . "] " . $query . "\n";
        $logEntry .= "Parameters: " . json_encode($data) . "\n---\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Show error page
     */
    private function showErrorPage(string $message, int $statusCode = 500): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
        }
        
        if ($this->isApiRequest()) {
            echo json_encode([
                'success' => false,
                'message' => DEBUG ? $message : 'Database error occurred',
                'error_code' => $statusCode
            ]);
        } else {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Database Error - <?= APP_NAME ?></title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body class="bg-light">
                <div class="container">
                    <div class="row justify-content-center align-items-center min-vh-100">
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-body text-center p-5">
                                    <div class="text-danger mb-4">
                                        <i class="bi bi-database-x" style="font-size: 4rem;"></i>
                                    </div>
                                    <h3 class="card-title text-danger mb-3">Database Error</h3>
                                    <div class="alert alert-danger">
                                        <?= DEBUG ? $message : 'A database error occurred. Please try again later.' ?>
                                    </div>
                                    <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
                                    <a href="<?= ROOT ?>" class="btn btn-primary">Home</a>
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
    
    /**
     * Check if this is an API request
     */
    private function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) && 
               str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }
    
    /**
     * Close database connection
     */
    public function __destruct()
    {
        self::$connection = null;
    }
}