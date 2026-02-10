<?php

namespace App\Libraries;

use Config\Encryption;

/**
 * PasswordResetService
 *
 * Handles password reset token generation and validation using MD5 hashing.
 * Tokens are valid for 1 hour with a grace period.
 */
class PasswordResetService
{
    /**
     * @var string Secret key used for token generation
     */
    private string $appKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Use encryption key as secret for token generation
        $encryptionConfig = new Encryption();
        $this->appKey = $encryptionConfig->key;

        if (empty($this->appKey)) {
            throw new \RuntimeException('Encryption key not configured. Please set encryption.key in .env file.');
        }
    }

    /**
     * Generate a random token for password reset
     *
     * @return string 32-character hex token
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Validate password reset token against the database
     *
     * @param int $userId User ID
     * @param string $token Token to validate
     * @return bool True if token is valid (exists, not expired, not used)
     */
    public function validateToken(int $userId, string $token): bool
    {
        $resetModel = new \App\Models\PasswordResetModel();
        $record = $resetModel->getValidToken($userId, $token);

        return $record !== null;
    }

    /**
     * Create password reset link and save token to database
     *
     * @param int $userId User ID
     * @return string Full password reset URL
     */
    public function createResetLink(int $userId): string
    {
        $token = $this->generateToken();
        $expiresAt = $this->getExpirationTime();

        $resetModel = new \App\Models\PasswordResetModel();

        // Invalidate any existing unused tokens for this user
        $resetModel->deleteUserTokens($userId);

        // Save new token
        $resetModel->skipValidation(true)->insert([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);

        return base_url("password-reset/{$userId}/{$token}");
    }

    /**
     * Get token expiration time
     *
     * @return string DateTime string (1 hour from now)
     */
    public function getExpirationTime(): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 hour'));
    }

    /**
     * Calculate remaining validity time for a token
     *
     * @param string $createdAt Token creation timestamp
     * @return int Minutes remaining (0 if expired)
     */
    public function getRemainingMinutes(string $createdAt): int
    {
        $expiresAt = strtotime($createdAt . ' +1 hour');
        $now = time();
        $remaining = $expiresAt - $now;

        return max(0, (int) ($remaining / 60));
    }

    /**
     * Check if token has expired based on creation time
     *
     * @param string $createdAt Token creation timestamp
     * @return bool True if expired
     */
    public function isExpired(string $createdAt): bool
    {
        $expiresAt = strtotime($createdAt . ' +1 hour');
        return time() > $expiresAt;
    }
}
