# Database Migration Instructions

## IMPORTANT: Run These Commands

Your database is missing required columns. You **MUST** run these commands:

```bash
cd nixsecura-backend
php artisan migrate
```

This will add the missing columns to your `blogs` table:
- `author_name`
- `author_role` 
- `author_image`
- `read_time`

## If Migration Fails

If you get an error saying the columns already exist, you can manually add them:

```sql
ALTER TABLE `blogs` 
ADD COLUMN `read_time` VARCHAR(255) NULL AFTER `category`,
ADD COLUMN `author_name` VARCHAR(255) NOT NULL DEFAULT 'Admin' AFTER `published_at`,
ADD COLUMN `author_image` LONGTEXT NULL AFTER `author_name`,
ADD COLUMN `author_role` VARCHAR(255) NULL AFTER `author_image`;
```

## After Running Migrations

1. Clear config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Create storage link (if not done):
```bash
php artisan storage:link
```

3. Ensure storage directory is writable:
```bash
# On Windows, check folder permissions
# On Linux/Mac:
chmod -R 775 storage/app/public
```

## Verify

After running migrations, try creating a blog again. The 500 error should be resolved.
