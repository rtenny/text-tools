<?php

namespace App\Libraries;

use CodeIgniter\Encryption\Encryption;
use Config\Services;

/**
 * EncryptionService
 *
 * Wrapper for CodeIgniter's Encryption service to handle
 * encryption and decryption of sensitive data (e.g., API keys).
 */
class EncryptionService
{
    /**
     * @var Encryption
     */
    protected $encrypter;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->encrypter = Services::encrypter();
    }

    /**
     * Encrypt plaintext
     *
     * @param string $plaintext The text to encrypt
     * @return string Base64-encoded encrypted string
     * @throws \Exception If encryption fails
     */
    public function encrypt(string $plaintext): string
    {
        if (empty($plaintext)) {
            throw new \InvalidArgumentException('Cannot encrypt empty string');
        }

        try {
            $encrypted = $this->encrypter->encrypt($plaintext);
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            log_message('error', 'Encryption failed: ' . $e->getMessage());
            throw new \Exception('Encryption failed');
        }
    }

    /**
     * Decrypt ciphertext
     *
     * @param string $ciphertext Base64-encoded encrypted string
     * @return string Decrypted plaintext
     * @throws \Exception If decryption fails
     */
    public function decrypt(string $ciphertext): string
    {
        if (empty($ciphertext)) {
            throw new \InvalidArgumentException('Cannot decrypt empty string');
        }

        try {
            $decoded = base64_decode($ciphertext, true);

            if ($decoded === false) {
                throw new \Exception('Invalid base64 encoding');
            }

            return $this->encrypter->decrypt($decoded);
        } catch (\Exception $e) {
            log_message('error', 'Decryption failed: ' . $e->getMessage());
            throw new \Exception('Decryption failed');
        }
    }

    /**
     * Check if encryption is properly configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        try {
            // Test encryption/decryption
            $testString = 'test';
            $encrypted = $this->encrypt($testString);
            $decrypted = $this->decrypt($encrypted);

            return $decrypted === $testString;
        } catch (\Exception $e) {
            return false;
        }
    }
}
