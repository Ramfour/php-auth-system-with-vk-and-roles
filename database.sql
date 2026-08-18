-- База данных для системы авторизации с ролями и VK OAuth
-- PostgreSQL 12+

-- Удаляем таблицы если существуют (для пересоздания)
DROP TABLE IF EXISTS auth_logs CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Таблица пользователей
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255),  -- NULL для пользователей VK
    role VARCHAR(20) NOT NULL DEFAULT 'regular',  -- 'regular' или 'vk_user'
    vk_id BIGINT UNIQUE,  -- ID пользователя ВКонтакте (NULL для обычных)
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Таблица логов неудачных попыток авторизации
CREATE TABLE auth_logs (
    id SERIAL PRIMARY KEY,
    login VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,  -- IPv4 или IPv6
    attempt_time TIMESTAMP NOT NULL DEFAULT NOW(),
    failure_reason VARCHAR(255)
);

-- Индексы для быстрого поиска
CREATE INDEX idx_users_login ON users(login);
CREATE INDEX idx_users_vk_id ON users(vk_id);
CREATE INDEX idx_auth_logs_login ON auth_logs(login);
CREATE INDEX idx_auth_logs_time ON auth_logs(attempt_time);

-- Тестовые данные (опционально)
-- Пароль для тестового пользователя: "password123"
INSERT INTO users (login, password_hash, role) VALUES 
('testuser', '$2y$10$YourHashedPasswordHere', 'regular');

-- Комментарии к таблицам
COMMENT ON TABLE users IS 'Таблица пользователей системы';
COMMENT ON COLUMN users.role IS 'Роль: regular - обычный пользователь, vk_user - авторизованный через VK';
COMMENT ON COLUMN users.vk_id IS 'ID пользователя ВКонтакте (заполняется только при OAuth)';
COMMENT ON TABLE auth_logs IS 'Логи неудачных попыток авторизации';
