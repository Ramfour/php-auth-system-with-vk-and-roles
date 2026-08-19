<?php
/**
 * Страница авторизации с CSRF-токеном
 */

require_once 'config.php';
require_once 'functions.php';

// Если уже авторизован - на главную
if (isAuthenticated()) {
    redirect('index.php');
}

$pdo = require 'db.php';
$error = '';

// Генерация CSRF-токена для формы
$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';
    
    // Проверка CSRF-токена
    if (!verifyCsrfToken($token)) {
        $error = 'Ошибка безопасности. Обновите страницу и попробуйте снова.';
        logFailedAuth($pdo, $login, 'CSRF token validation failed');
    }
    // Валидация данных
    elseif (empty($login) || empty($password)) {
        $error = 'Заполните все поля';
        logFailedAuth($pdo, $login, 'Empty login or password');
    }
    else {
        // Поиск пользователя
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = 'Неверный логин или пароль';
            logFailedAuth($pdo, $login, 'User not found');
        }
        // Проверка пароля (только для обычных пользователей)
        elseif ($user['password_hash'] && !password_verify($password, $user['password_hash'])) {
            $error = 'Неверный логин или пароль';
            logFailedAuth($pdo, $login, 'Invalid password');
        }
        // Пользователь VK не может войти через логин-пароль
        elseif ($user['role'] === 'vk_user' && empty($user['password_hash'])) {
            $error = 'Этот аккаунт создан через VK. Используйте кнопку "Войти через VK".';
            logFailedAuth($pdo, $login, 'VK user tried to login with password');
        }
        else {
            // Успешная авторизация
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['role'] = $user['role'];
            
            redirect('dashboard.php');
        }
    }
    
    // Генерируем новый токен после обработки
    $csrf_token = generateCsrfToken();
}

// URL для авторизации через VK
$vk_auth_url = 'https://oauth.vk.com/authorize?' . http_build_query([
    'client_id' => VK_APP_ID,
    'redirect_uri' => VK_REDIRECT_URI,
    'display' => 'page',
    'scope' => 'email',
    'response_type' => 'code',
    'v' => '5.131'
]);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vk-btn {
            background-color: #0077FF;
            color: white;
        }
        .vk-btn:hover {
            background-color: #0066DD;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Вход в систему</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= e($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                            
                            <div class="mb-3">
                                <label for="login" class="form-label">Логин</label>
                                <input type="text" class="form-control" id="login" name="login" 
                                       value="<?= e($login ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Войти</button>
                        </form>
                        
                        <hr>
                        
                        <a href="<?= e($vk_auth_url) ?>" class="btn vk-btn w-100 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-vk" viewBox="0 0 16 16">
                                <path d="M15.684 0H.316C.141 0 0 .141 0 .316v15.368c0 .175.141.316.316.316h15.368c.175 0 .316-.141.316-.316V.316C16 .141 15.859 0 15.684 0zM13.5 9.836c.397.39.817.765 1.17 1.179.156.183.305.373.424.588.168.305.015.64-.251.659l-1.654.024c-.427.035-.763-.134-1.04-.408-.217-.214-.419-.442-.629-.663-.088-.092-.18-.177-.283-.248-.206-.144-.385-.106-.507.123-.126.234-.154.492-.166.752-.017.357-.136.45-.494.466-.761.034-1.48-.082-2.15-.465a5.692 5.692 0 0 1-1.63-1.31c-.896-.974-1.613-2.06-2.27-3.188-.144-.247-.039-.379.232-.386.44-.012.88-.011 1.32-.002.178.004.293.098.364.26.257.588.565 1.152.932 1.683.096.139.195.278.33.377.159.116.281.08.357-.094.048-.112.07-.232.084-.352.048-.411.055-.822-.015-1.229-.041-.242-.196-.398-.437-.444a.82.82 0 0 1 .373-.226c.25-.076.512-.111.773-.111h.593c.303.006.381.096.406.402.006.08.006.162.006.243l-.001 1.036c-.001.096.048.382.22.445.137.049.227-.063.31-.152.376-.407.644-.885.877-1.38.104-.223.193-.455.276-.687.063-.178.162-.254.36-.25l1.796.005c.053 0 .107 0 .16.008.286.043.365.153.276.432-.15.468-.435.857-.728 1.24l-.627.819c-.268.348-.246.522.067.822z"/>
                            </svg>
                            Войти через ВКонтакте
                        </a>
                        
                        <p class="text-center mb-0">
                            Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
