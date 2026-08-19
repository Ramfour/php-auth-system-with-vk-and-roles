<?php
/**
 * Обработчик callback от VK OAuth
 */

require_once 'config.php';
require_once 'functions.php';

$pdo = require 'db.php';

// Проверяем наличие кода авторизации
if (!isset($_GET['code'])) {
    redirect('login.php?error=vk_auth_failed');
}

$code = $_GET['code'];

// Обмен кода на access_token
$token_url = 'https://oauth.vk.com/access_token?' . http_build_query([
    'client_id' => VK_APP_ID,
    'client_secret' => VK_APP_SECRET,
    'redirect_uri' => VK_REDIRECT_URI,
    'code' => $code
]);

$response = file_get_contents($token_url);
$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    redirect('login.php?error=vk_token_failed');
}

$access_token = $data['access_token'];
$vk_user_id = $data['user_id'];

// Получаем информацию о пользователе VK
$user_info_url = 'https://api.vk.com/method/users.get?' . http_build_query([
    'user_ids' => $vk_user_id,
    'fields' => 'photo_200',
    'access_token' => $access_token,
    'v' => '5.131'
]);

$user_response = file_get_contents($user_info_url);
$user_data = json_decode($user_response, true);

if (!isset($user_data['response'][0])) {
    redirect('login.php?error=vk_user_info_failed');
}

$vk_user = $user_data['response'][0];
$vk_login = 'vk_' . $vk_user_id;
$vk_name = $vk_user['first_name'] . ' ' . $vk_user['last_name'];

// Проверяем существование пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE vk_id = ?");
$stmt->execute([$vk_user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Создаём нового пользователя VK
    $stmt = $pdo->prepare("
        INSERT INTO users (login, password_hash, role, vk_id) 
        VALUES (?, NULL, 'vk_user', ?)
    ");
    $stmt->execute([$vk_login, $vk_user_id]);
    $user_id = $pdo->lastInsertId();
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['login'] = $vk_login;
    $_SESSION['role'] = 'vk_user';
    $_SESSION['vk_name'] = $vk_name;
} else {
    // Авторизуем существующего пользователя
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['login'] = $user['login'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['vk_name'] = $vk_name;
}

redirect('dashboard.php');
