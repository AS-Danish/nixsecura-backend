# Quick Fix Guide

## Step 1: Run Database Migration (REQUIRED)

The 500 error is because your `blogs` table is missing columns. Run:

```bash
cd nixsecura-backend
php artisan migrate
```

This adds: `author_name`, `author_role`, `author_image`, `read_time`

## Step 2: Create Storage Link (REQUIRED)

Images won't display without this:

```bash
cd nixsecura-backend
php artisan storage:link
```

## Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## Step 4: Verify .env

Make sure `.env` has:
```
APP_URL=http://localhost:8000
```

## After These Steps

1. ✅ Blog creation will work (no more 500 errors)
2. ✅ Images will upload to server
3. ✅ Images will display on all pages
4. ✅ Image preview will stay visible in modal

## Troubleshooting

**If migration fails:**
- Check database connection in `.env`
- Ensure you have write permissions
- Try: `php artisan migrate:fresh` (WARNING: deletes all data)

**If images don't display:**
- Verify `public/storage` symlink exists
- Check `storage/app/public/images` folder exists
- Verify file permissions: `chmod -R 775 storage/app/public`

**If image preview disappears:**
- Check browser console for CORS errors
- Verify `APP_URL` matches your backend URL
- Check network tab to see if image request succeeds
