<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blogs = App\Models\Blog::all();
foreach ($blogs as $blog) {
    echo "ID: " . $blog->id . "\n";
    echo "Title: " . $blog->title . "\n";
    echo "Image: " . $blog->image . "\n";
    echo "-------------------\n";
}
