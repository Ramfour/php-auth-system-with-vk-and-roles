<?php
/**
 * Главная страница (публичная)
 */

require_once 'config.php';
require_once 'functions.php';

// Если уже авторизован - на dashboard
if (isAuthenticated()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система авторизации PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Auth System</a>
            <div class="navbar-nav ms-auto">
                <a href="login.php" class="btn btn-outline-light btn-sm me-2">Вход</a>
                <a href="register.php" class="btn btn-primary btn-sm">Регистрация</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <h1 class="display-4 mb-4">Система авторизации и аутентификации</h1>
                <p class="lead mb-4">
                    Демонстрация работы системы аутентификации с поддержкой 
                    классической регистрации и OAuth через ВКонтакте
                </p>

                <div class="row mt-5">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">🔐 Классическая регистрация</h5>
                                <p class="card-text">
                                    Создайте аккаунт с логином и паролем. 
                                    Защита CSRF-токенами и хеширование паролей.
                                </p>
                                <a href="register.php" class="btn btn-primary">Зарегистрироваться</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">🔵 OAuth ВКонтакте</h5>
                                <p class="card-text">
                                    Быстрый вход через аккаунт VK. 
                                    Дополнительные привилегии для VK-пользователей.
                                </p>
                                <a href="login.php" class="btn btn-primary">Войти через VK</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <h5>Возможности системы:</h5>
                    <ul class="text-start">
                        <li>Регистрация и авторизация через логин-пароль</li>
                        <li>Интеграция с VK OAuth 2.0</li>
                        <li>Система ролей (обычный пользователь / VK-пользователь)</li>
                        <li>Защита CSRF-токенами</li>
                        <li>Логирование неудачных попыток входа</li>
                        <li>Разграничение доступа к контенту по ролям</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
