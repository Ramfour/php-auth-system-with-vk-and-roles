<?php

namespace App;

/**
 * Authentication and authorization helpers
 */
class Auth
{
    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['login']);
    }

    /**
     * Get current user role
     */
    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::role() === $role;
    }

    /**
     * Require authentication (redirect if not logged in)
     */
    public static function require(): void
    {
        if (!self::check()) {
            header('Location: Login.php');
            exit;
        }
    }

    /**
     * Login user
     */
    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['role'] = $user['role'];
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        session_destroy();
    }
}
