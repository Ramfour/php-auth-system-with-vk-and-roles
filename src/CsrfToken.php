<?php

namespace App;

/**
 * CSRF token management
 */
class CsrfToken
{
    private const TOKEN_LIFETIME = 1800; // 30 minutes

    /**
     * Generate new CSRF token
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    /**
     * Verify CSRF token
     */
    public static function verify(string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Check token expiration
        if (time() - $_SESSION['csrf_token_time'] > self::TOKEN_LIFETIME) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
