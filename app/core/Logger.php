<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/**
 * Application Logger Class
 * 
 * Comprehensive logging system with multiple log levels and rotation
 */
class Logger
{
    private static $instance = null;
    private $logFile;
    private $logLevel;
    private $maxFileSize;
    private $maxFiles;
    
    // Log levels
    const EMERGENCY = 'EMERGENCY';
    const ALERT = 'ALERT';
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    private static $levels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7
    ];
    
    private function __construct()
    {
        $this->logFile = ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'app.log';
        $this->logLevel = EnvLoader::isDebug() ? self::DEBUG : self::INFO;
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        $this->maxFiles = 5;
        
        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log emergency message
     */
    public static function emergency(mixed $message, array $context = []): void
    {
        self::log(self::EMERGENCY, $message, $context);
    }
    
    /**
     * Log alert message
     */
    public static function alert(mixed $message, array $context = []): void
    {
        self::log(self::ALERT, $message, $context);
    }
    
    /**
     * Log critical message
     */
    public static function critical(mixed $message, array $context = []): void
    {
        self::log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Log error message
     */
    public static function error(mixed $message, array $context = []): void
    {
        self::log(self::ERROR, $message, $context);
    }
    
    /**
     * Log warning message
     */
    public static function warning(mixed $message, array $context = []): void
    {
        self::log(self::WARNING, $message, $context);
    }
    
    /**
     * Log notice message
     */
    public static function notice(mixed $message, array $context = []): void
    {
        self::log(self::NOTICE, $message, $context);
    }
    
    /**
     * Log info message
     */
    public static function info(mixed $message, array $context = []): void
    {
        self::log(self::INFO, $message, $context);
    }
    
    /**
     * Log debug message
     */
    public static function debug(mixed $message, array $context = []): void
    {
        self::log(self::DEBUG, $message, $context);
    }
    
    /**
     * Log message with specified level
     */
    public static function log(string $level, mixed $message, array $context = []): void
    {
        $logger = self::getInstance();
        $logger->writeLog($level, $message, $context);
    }
    
    /**
     * Write log entry to file
     */
    private function writeLog(string $level, mixed $message, array $context): void
    {
        // Check if this log level should be written
        if (!isset(self::$levels[$level]) || 
            self::$levels[$level] > self::$levels[$this->logLevel]) {
            return;
        }
        
        // Rotate log if it's too large
        $this->rotateLog();
        
        // Format timestamp
        $timestamp = date('Y-m-d H:i:s');
        $ip = get_ip();
        $userId = $_SESSION['id'] ?? 'guest';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        // Build log entry
        $logEntry = sprintf(
            "[%s] %s [%s] [%s] [%s] %s\n",
            $timestamp,
            $level,
            $ip,
            $userId,
            $url,
            $message
        );
        
        // Add context if available
        if (!empty($context)) {
            $logEntry .= "Context: " . json_encode($context, JSON_UNESCAPED_SLASHES) . "\n";
        }
        
        $logEntry .= "---\n";
        
        // Write to log file with exclusive lock
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Rotate log files when they get too large
     */
    private function rotateLog(): void
    {
        if (!file_exists($this->logFile) || filesize($this->logFile) < $this->maxFileSize) {
            return;
        }
        
        // Remove oldest log if we have too many
        for ($i = $this->maxFiles; $i > 1; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        
        // Shift existing logs
        for ($i = $this->maxFiles - 1; $i > 0; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            $newFile = $this->logFile . '.' . ($i + 1);
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }
        
        // Move current log to .1
        rename($this->logFile, $this->logFile . '.1');
    }
    
    /**
     * Log security events separately
     */
    public static function security(string $event, array $context = []): void
    {
        $securityLogFile = ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'security.log';
        $logDir = dirname($securityLogFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = get_ip();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $userId = $_SESSION['id'] ?? 'guest';
        
        $logEntry = sprintf(
            "[%s] SECURITY_EVENT [%s] [%s] [%s] %s\n",
            $timestamp,
            $ip,
            $userId,
            $event,
            json_encode($context, JSON_UNESCAPED_SLASHES)
        );
        
        $logEntry .= "User-Agent: " . $userAgent . "\n---\n";
        
        file_put_contents($securityLogFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log user actions for audit trail
     */
    public static function audit(string $action, string $resource, array $details = []): void
    {
        $auditLogFile = ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'audit.log';
        $logDir = dirname($auditLogFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = get_ip();
        $userId = $_SESSION['id'] ?? 'guest';
        $userRole = $_SESSION['userRole'] ?? 'guest';
        
        $logEntry = sprintf(
            "[%s] AUDIT [%s] [%s:%s] %s %s\n",
            $timestamp,
            $ip,
            $userId,
            $userRole,
            $action,
            $resource
        );
        
        if (!empty($details)) {
            $logEntry .= "Details: " . json_encode($details, JSON_UNESCAPED_SLASHES) . "\n";
        }
        
        $logEntry .= "---\n";
        
        file_put_contents($auditLogFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Clean old log files
     */
    public static function cleanup(int $days = 30): void
    {
        $logsDir = ROOTPATH . 'logs';
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        if (!is_dir($logsDir)) {
            return;
        }
        
        foreach (glob($logsDir . '/*.log*') as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
}

/**
 * Convenience function for quick logging
 */
function log_info(mixed $message, array $context = []): void
{
    Logger::info($message, $context);
}

function log_error(mixed $message, array $context = []): void
{
    Logger::error($message, $context);
}

function log_warning(mixed $message, array $context = []): void
{
    Logger::warning($message, $context);
}

function log_debug(mixed $message, array $context = []): void
{
    Logger::debug($message, $context);
}

function log_security(string $event, array $context = []): void
{
    Logger::security($event, $context);
}

function log_audit(string $action, string $resource, array $details = []): void
{
    Logger::audit($action, $resource, $details);
}