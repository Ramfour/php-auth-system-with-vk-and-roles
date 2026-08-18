<?php
/**
 * Вспомогательные функции
 */

/**
 * Генерация CSRF-токена
 */
function generateCsrfToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    return $token;
}

/**
 * Проверка CSRF-токена
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Токен действителен 30 минут
    if (time() - $_SESSION['csrf_token_time'] > 1800) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Проверка авторизации
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['login']);
}

/**
 * Получение роли текущего пользователя
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Проверка роли пользователя
 */
function hasRole($role) {
    return getUserRole() === $role;
}

/**
 * Редирект на страницу авторизации
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Логирование неудачной попытки входа
 */
function logFailedAuth($pdo, $login, $reason) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $pdo->prepare("
        INSERT INTO auth_logs (login, ip_address, failure_reason) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$login, $ip, $reason]);
}

/**
 * Безопасный редирект
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Экранирование HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
