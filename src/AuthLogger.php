<?php

namespace App;

use PDO;

/**
 * Authentication logging service
 */
class AuthLogger
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Log failed authentication attempt
     */
    public function logFailure(string $login, string $reason): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $stmt = $this->pdo->prepare("
            INSERT INTO auth_logs (login, ip_address, failure_reason) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$login, $ip, $reason]);
    }
}
