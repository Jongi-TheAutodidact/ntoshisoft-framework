<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/**
 * Input Validation and Sanitization Class
 * 
 * Comprehensive input validation with security features
 */
class Validator
{
    private $errors = [];
    private $data;
    private $rules;
    
    public function __construct(array $data, array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        
        // Apply XSS protection to all input
        $this->data = $this->sanitizeAll($this->data);
    }
    
    /**
     * Validate all rules
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }
            
            foreach ($fieldRules as $rule) {
                if (!$this->applyRule($field, $value, $rule)) {
                    break; // Stop at first error for this field
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get specific field error
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Get validated and sanitized data
     */
    public function getValidatedData(): array
    {
        return $this->data;
    }
    
    /**
     * Apply individual validation rule
     */
    private function applyRule(string $field, mixed $value, string $rule): bool
    {
        // Handle rules with parameters (e.g., max:100)
        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }
        
        switch (strtolower($ruleName)) {
            case 'required':
                return $this->validateRequired($field, $value);
            case 'email':
                return $this->validateEmail($field, $value);
            case 'min':
                return $this->validateMin($field, $value, $parameter);
            case 'max':
                return $this->validateMax($field, $value, $parameter);
            case 'numeric':
                return $this->validateNumeric($field, $value);
            case 'alpha':
                return $this->validateAlpha($field, $value);
            case 'alphanum':
                return $this->validateAlphaNum($field, $value);
            case 'phone':
                return $this->validatePhone($field, $value);
            case 'url':
                return $this->validateUrl($field, $value);
            case 'date':
                return $this->validateDate($field, $value);
            case 'regex':
                return $this->validateRegex($field, $value, $parameter);
            case 'in':
                return $this->validateIn($field, $value, $parameter);
            case 'file':
                return $this->validateFile($field, $value, $parameter);
            case 'sa_id':
                return $this->validateSAId($field, $value);
            default:
                return true; // Unknown rule, skip
        }
    }
    
    /**
     * Validate required field
     */
    private function validateRequired(string $field, mixed $value): bool
    {
        if (is_null($value) || $value === '') {
            $this->errors[$field] = "The {$field} field is required.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email
     */
    private function validateEmail(string $field, mixed $value): bool
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "The {$field} must be a valid email address.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     */
    private function validateMin(string $field, mixed $value, string $min): bool
    {
        if (!empty($value) && strlen($value) < (int)$min) {
            $this->errors[$field] = "The {$field} must be at least {$min} characters.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     */
    private function validateMax(string $field, mixed $value, string $max): bool
    {
        if (!empty($value) && strlen($value) > (int)$max) {
            $this->errors[$field] = "The {$field} must not exceed {$max} characters.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric
     */
    private function validateNumeric(string $field, mixed $value): bool
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = "The {$field} must be a number.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate alphabetic characters only
     */
    private function validateAlpha(string $field, mixed $value): bool
    {
        if (!empty($value) && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
            $this->errors[$field] = "The {$field} may only contain letters and spaces.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate alphanumeric characters only
     */
    private function validateAlphaNum(string $field, mixed $value): bool
    {
        if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            $this->errors[$field] = "The {$field} may only contain letters and numbers.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate phone number (South African format)
     */
    private function validatePhone(string $field, mixed $value): bool
    {
        if (!empty($value)) {
            // Remove all non-digit characters
            $digits = preg_replace('/\D/', '', $value);
            
            // Check if it's a valid South African phone number
            if (!preg_match('/^0?[678][0-9]{8}$/', $digits)) {
                $this->errors[$field] = "The {$field} must be a valid South African phone number.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate URL
     */
    private function validateUrl(string $field, mixed $value): bool
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = "The {$field} must be a valid URL.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate date
     */
    private function validateDate(string $field, mixed $value): bool
    {
        if (!empty($value)) {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                $this->errors[$field] = "The {$field} must be a valid date (YYYY-MM-DD format).";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate using regular expression
     */
    private function validateRegex(string $field, mixed $value, string $pattern): bool
    {
        if (!empty($value) && !preg_match($pattern, $value)) {
            $this->errors[$field] = "The {$field} format is invalid.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate value is in allowed list
     */
    private function validateIn(string $field, mixed $value, string $allowed): bool
    {
        if (!empty($value)) {
            $allowedValues = explode(',', $allowed);
            if (!in_array($value, $allowedValues)) {
                $this->errors[$field] = "The {$field} must be one of: " . implode(', ', $allowedValues);
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate file upload
     */
    private function validateFile(string $field, mixed $value, string $allowedTypes): bool
    {
        if (!isset($_FILES[$field]) || empty($_FILES[$field]['name'])) {
            if (in_array('required', $this->rules[$field] ?? [])) {
                $this->errors[$field] = "The {$field} file is required.";
                return false;
            }
            return true;
        }
        
        $file = $_FILES[$field];
        $allowedMimes = explode(',', $allowedTypes);
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->errors[$field] = "The {$field} file must not exceed " . formatSizeUnits(MAX_FILE_SIZE);
            return false;
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimes)) {
            $this->errors[$field] = "The {$field} file type is not allowed.";
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate South African ID number
     */
    private function validateSAId(string $field, mixed $value): bool
    {
        if (!empty($value)) {
            if (!preg_match('/^[0-9]{13}$/', $value)) {
                $this->errors[$field] = "The {$field} must be a 13-digit ID number.";
                return false;
            }
            
            // Validate Luhn algorithm
            if (!$this->validateLuhn($value)) {
                $this->errors[$field] = "The {$field} is not a valid ID number.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate using Luhn algorithm (for SA ID)
     */
    private function validateLuhn(string $number): bool
    {
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }
        
        return ($sum % 10) === 0;
    }
    
    /**
     * Sanitize all input data against XSS
     */
    private function sanitizeAll(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeAll($value);
            } else {
                $data[$key] = $this->sanitize($value);
            }
        }
        return $data;
    }
    
    /**
     * Sanitize individual value
     */
    private function sanitize(mixed $value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        // Convert to string if not already
        $value = (string)$value;
        
        // Remove invisible characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Standardize newlines
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        
        // Remove potential XSS attacks
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Strip potentially dangerous HTML tags if needed
        $value = strip_tags($value);
        
        return trim($value);
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Rate limiting helper
     */
    public static function checkRateLimit(string $identifier, int $maxAttempts = 5, int $windowMinutes = 15): bool
    {
        $key = 'rate_limit_' . md5($identifier);
        $now = time();
        $window = $windowMinutes * 60;
        
        // Get existing attempts
        $attempts = $_SESSION[$key] ?? [];
        
        // Clean old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        // Check if exceeded
        if (count($attempts) >= $maxAttempts) {
            return false;
        }
        
        // Add current attempt
        $attempts[] = $now;
        $_SESSION[$key] = $attempts;
        
        return true;
    }
}

/**
 * Convenience functions for common validations
 */
function validate_input(array $data, array $rules): array
{
    $validator = new Validator($data, $rules);
    
    if ($validator->validate()) {
        return ['success' => true, 'data' => $validator->getValidatedData()];
    } else {
        return ['success' => false, 'errors' => $validator->getErrors()];
    }
}

function sanitize_input(mixed $input): string
{
    return (new Validator(['input' => $input]))->getValidatedData()['input'] ?? '';
}