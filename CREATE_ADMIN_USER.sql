-- SQL Query to create an admin user
-- Run this query in your database to create an admin user

INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES (
    'Admin User',
    'admin@nixsecura.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    NOW(),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    role = VALUES(role),
    updated_at = NOW();

-- Note: The password hash above is for 'admin123'
-- If you want to use a different password, generate a new hash using:
-- php artisan tinker
-- Hash::make('your-password-here')
