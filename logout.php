<?php
/**
 * Выход из системы
 */

require_once 'config.php';

// Уничтожаем сессию
session_unset();
session_destroy();

// Редирект на главную
header('Location: index.php');
exit;
