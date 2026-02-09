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
     * Generate MD5 token for password reset
     *
     * Token format: md5(date('Y-m-d H') . $userId . $appKey)
     *
     * @param int $userId User ID
     * @return string MD5 hash (32 characters)
     */
    public function generateToken(int $userId): string
    {
        $hour = date('Y-m-d H');
        return md5($hour . $userId . $this->appKey);
    }

    /**
     * Validate password reset token
     *
     * Checks if token matches current hour or previous hour (grace period).
     *
     * @param int $userId User ID
     * @param string $token Token to validate
     * @return bool True if token is valid
     */
    public function validateToken(int $userId, string $token): bool
    {
        // Check current hour
        $currentToken = $this->generateToken($userId);
        if ($currentToken === $token) {
            return true;
        }

        // Check previous hour (grace period)
        $previousHour = date('Y-m-d H', strtotime('-1 hour'));
        $previousToken = md5($previousHour . $userId . $this->appKey);

        return $previousToken === $token;
    }

    /**
     * Create password reset link
     *
     * @param int $userId User ID
     * @return string Full password reset URL
     */
    public function createResetLink(int $userId): string
    {
        $token = $this->generateToken($userId);
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
