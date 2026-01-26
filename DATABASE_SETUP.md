# Database Setup Guide

## Database Name

The default database name for this Laravel project is: **`nixsecura`** (or `laravel` if using default)

## Setting Up MySQL Database

### Step 1: Create the Database

Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line) and run:

```sql
CREATE DATABASE nixsecura CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or if you prefer a different name, you can use:
```sql
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Configure Laravel .env File

1. Open `backend/.env` file
2. Update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nixsecura
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Important:** Replace:
- `nixsecura` with your actual database name if different
- `root` with your MySQL username if different
- `your_mysql_password` with your actual MySQL password

### Step 3: Run Migrations

After configuring the database, run migrations to create all tables:

```bash
cd backend
php artisan migrate
```

This will create the following tables:
- `users` (with role field)
- `workshops`
- `testimonials`
- `faculty`
- `certificates`
- `gallery`
- `blogs`
- `courses`
- `sessions`
- `cache`
- `jobs`
- `password_reset_tokens`
- `personal_access_tokens`

### Step 4: Create Admin User

Run the seeder to create an admin user:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Or use the SQL query from `CREATE_ADMIN_USER.sql`:

```sql
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES (
    'Admin User',
    'admin@nixsecura.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    NOW(),
    NOW(),
    NOW()
);
```

**Default Credentials:**
- Email: `admin@nixsecura.com`
- Password: `admin123`

## Verify Database Connection

Test the connection:

```bash
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
```

If it works, you'll see the PDO object. If not, check your `.env` configuration.

## Troubleshooting

### Database Not Found Error
- Make sure the database exists in MySQL
- Check that `DB_DATABASE` in `.env` matches the actual database name
- Verify MySQL is running

### Access Denied Error
- Check `DB_USERNAME` and `DB_PASSWORD` in `.env`
- Verify the MySQL user has permissions to access the database

### Migration Errors
- Make sure all previous migrations have run
- If needed, run `php artisan migrate:fresh` (WARNING: This will delete all data)
