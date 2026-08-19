<?php
/**
 * Защищённая страница с контентом по ролям
 * Текст виден всем авторизованным, картинка - только VK-пользователям
 */

require_once 'config.php';
require_once 'functions.php';

// Требуется авторизация
requireAuth();

$pdo = require 'db.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Auth System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-light me-3">
                    Привет, <?= e($_SESSION['vk_name'] ?? $_SESSION['login']) ?>!
                    <span class="badge bg-<?= getUserRole() === 'vk_user' ? 'info' : 'secondary' ?>">
                        <?= getUserRole() === 'vk_user' ? 'VK Пользователь' : 'Обычный' ?>
                    </span>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Защищённая страница</h4>
                    </div>
                    <div class="card-body">
                        <!-- Текст виден всем авторизованным -->
                        <div class="mb-4">
                            <h5>Добро пожаловать в защищённую зону!</h5>
                            <p class="lead">
                                Этот текст доступен всем авторизованным пользователям, 
                                независимо от способа входа в систему.
                            </p>
                            <p>
                                Наша система поддерживает два типа аутентификации:
                            </p>
                            <ul>
                                <li><strong>Обычная регистрация</strong> — через логин и пароль</li>
                                <li><strong>OAuth ВКонтакте</strong> — быстрый вход через VK</li>
                            </ul>
                        </div>

                        <hr>

                        <!-- Картинка только для VK-пользователей -->
                        <?php if (hasRole('vk_user')): ?>
                            <div class="alert alert-info">
                                <h5>🎉 Эксклюзивный контент для VK-пользователей</h5>
                                <p>Вы вошли через ВКонтакте, поэтому видите это изображение:</p>
                                <div class="text-center mt-3">
                                    <img src="https://via.placeholder.com/600x300/0077FF/FFFFFF?text=VK+User+Exclusive+Content" 
                                         alt="VK Exclusive" 
                                         class="img-fluid rounded shadow">
                                    <p class="text-muted mt-2">
                                        <small>Это изображение доступно только пользователям с ролью "VK User"</small>
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <h5>⚠️ Ограниченный доступ</h5>
                                <p>
                                    Вы вошли как обычный пользователь. Некоторый контент (например, изображения) 
                                    доступен только пользователям, авторизованным через ВКонтакте.
                                </p>
                                <p class="mb-0">
                                    <small>Чтобы получить полный доступ, выйдите и войдите через кнопку "Войти через ВКонтакте".</small>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Информация о сессии -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Информация о сессии</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>User ID:</th>
                                <td><?= e($_SESSION['user_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Логин:</th>
                                <td><?= e($_SESSION['login']) ?></td>
                            </tr>
                            <tr>
                                <th>Роль:</th>
                                <td>
                                    <span class="badge bg-<?= getUserRole() === 'vk_user' ? 'info' : 'secondary' ?>">
                                        <?= e(getUserRole()) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (isset($_SESSION['vk_name'])): ?>
                            <tr>
                                <th>VK Имя:</th>
                                <td><?= e($_SESSION['vk_name']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
