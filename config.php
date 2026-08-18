<?php
/**
 * Конфигурация приложения
 */

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'auth_system');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_PORT', '5432');

// Секретный ключ для CSRF-токенов
define('SECRET_KEY', 'your-secret-key-change-in-production-2026');

// Настройки VK OAuth
// Получить на https://vk.com/apps?act=manage
define('VK_APP_ID', 'your_vk_app_id');
define('VK_APP_SECRET', 'your_vk_app_secret');
define('VK_REDIRECT_URI', 'http://localhost/27.6-php-auth-system-with-vk-and-roles/vk-callback.php');

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Установить 1 для HTTPS

session_start();
