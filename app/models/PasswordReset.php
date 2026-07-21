<?php
defined('ROOTPATH') or exit('Access Denied!');

class PasswordReset
{
    use Model;

    protected $table = 'password_resets';
    protected $allowedColumns = ['email', 'token', 'date_created'];

    /**
     * Generate & store a reset token
     */
    public function createToken(string $email): string
    {
        $token = bin2hex(random_bytes(16));

        // delete any existing tokens for this email
        $sql = "DELETE FROM {$this->table} WHERE email = :email";
        $this->query($sql, ['email' => $email]);

        // insert new one
        $this->insert([
            'email'      => $email,
            'token'      => $token,
            'date_created' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Validate token and return email or false
     */
    public function validateToken(string $token): string|false
    {
        $row = $this->first(['token' => $token]);
        if (!$row) return false;

        // optional: expire after 60 minutes
        $created = strtotime($row->date_created);
        if (time() - $created > 3600) {
            // expired → delete it
            $this->deleteToken($token);
            return false;
        }

        return $row->email;
    }

    /**
     * Clean up token after use
     */
    public function deleteToken(string $token): void
    {
        $sql = "DELETE FROM {$this->table} WHERE token = :token";
        $this->query($sql, ['token' => $token]);
    }
}
