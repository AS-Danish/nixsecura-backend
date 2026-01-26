# Storage Setup Instructions

## Creating the Storage Link

After setting up the Laravel backend, you need to create a symbolic link so that uploaded images are accessible via the web.

### Run this command in your Laravel backend directory:

```bash
php artisan storage:link
```

This will create a symbolic link from `public/storage` to `storage/app/public`, allowing uploaded images to be served via URLs like:
- `http://localhost:8000/storage/images/filename.jpg`

### Verify the link was created:

Check that `public/storage` exists and points to `storage/app/public`.

### Directory Structure:

```
storage/
  app/
    public/
      images/          <- Images will be stored here
        (uploaded files)
```

### Image URLs:

Images will be accessible at:
- `http://localhost:8000/storage/images/{filename}`

Make sure your `.env` file has:
```
APP_URL=http://localhost:8000
```

## Troubleshooting

If images don't load:
1. Make sure `php artisan storage:link` was run
2. Check that `storage/app/public/images` directory exists and is writable
3. Verify `APP_URL` in `.env` matches your frontend's API base URL
4. Check file permissions: `chmod -R 775 storage/app/public`
