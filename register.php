<?php
/**
 * Страница регистрации
 */

require_once 'config.php';
require_once 'functions.php';

// Если уже авторизован - на главную
if (isAuthenticated()) {
    redirect('index.php');
}

$pdo = require 'db.php';
$error = '';
$success = '';

// Генерация CSRF-токена для формы
$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $token = $_POST['csrf_token'] ?? '';
    
    // Проверка CSRF-токена
    if (!verifyCsrfToken($token)) {
        $error = 'Ошибка безопасности. Обновите страницу и попробуйте снова.';
    }
    // Валидация данных
    elseif (empty($login) || empty($password)) {
        $error = 'Заполните все поля';
    }
    elseif (strlen($login) < 3 || strlen($login) > 50) {
        $error = 'Логин должен быть от 3 до 50 символов';
    }
    elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    }
    elseif ($password !== $password_confirm) {
        $error = 'Пароли не совпадают';
    }
    else {
        // Проверка занятости логина
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        
        if ($stmt->fetch()) {
            $error = 'Логин уже занят';
        } else {
            // Хеширование пароля и создание пользователя
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (login, password_hash, role) 
                VALUES (?, ?, 'regular')
            ");
            
            if ($stmt->execute([$login, $password_hash])) {
                $success = 'Регистрация успешна! Теперь вы можете войти.';
                // Очистка формы
                $login = '';
            } else {
                $error = 'Ошибка при регистрации. Попробуйте позже.';
            }
        }
    }
    
    // Генерируем новый токен после обработки
    $csrf_token = generateCsrfToken();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Регистрация</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= e($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= e($success) ?></div>
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
                            
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Подтвердите пароль</label>
                                <input type="password" class="form-control" id="password_confirm" 
                                       name="password_confirm" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                        </form>
                        
                        <hr>
                        
                        <p class="text-center mb-0">
                            Уже есть аккаунт? <a href="login.php">Войти</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
