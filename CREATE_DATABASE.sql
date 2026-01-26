-- Create Database for Nixsecura Project
-- Run this SQL query in your MySQL server to create the database

CREATE DATABASE IF NOT EXISTS nixsecura CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- After creating the database, update your .env file with:
-- DB_CONNECTION=mysql
-- DB_HOST=127.0.0.1
-- DB_PORT=3306
-- DB_DATABASE=nixsecura
-- DB_USERNAME=root
-- DB_PASSWORD=your_password

-- Then run: php artisan migrate
