<?php
/**
 * PHP Extensions Checker
 * Run this file to check if required PHP extensions are installed and enabled
 * 
 * Usage: php check_php_extensions.php
 */

echo "=== PHP Extensions Check ===\n\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

$required_extensions = [
    'openssl' => 'Required for Laravel encryption and security',
    'pdo' => 'Required for database operations',
    'mbstring' => 'Required for string operations',
    'xml' => 'Required for XML processing',
    'ctype' => 'Required for character type checking',
    'json' => 'Required for JSON operations',
    'bcmath' => 'Required for arbitrary precision mathematics',
    'fileinfo' => 'Required for file type detection',
    'tokenizer' => 'Required for tokenization',
];

$missing = [];
$enabled = [];

foreach ($required_extensions as $ext => $description) {
    if (extension_loaded($ext)) {
        $enabled[] = $ext;
        echo "✅ {$ext}: ENABLED - {$description}\n";
    } else {
        $missing[] = $ext;
        echo "❌ {$ext}: MISSING - {$description}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Enabled: " . count($enabled) . " / " . count($required_extensions) . "\n";
echo "Missing: " . count($missing) . " / " . count($required_extensions) . "\n\n";

if (count($missing) > 0) {
    echo "⚠️  WARNING: Missing required extensions!\n";
    echo "Please enable the following extensions in your php.ini file:\n";
    foreach ($missing as $ext) {
        echo "  - {$ext}\n";
    }
    echo "\n";
    echo "To find your php.ini file, run: php --ini\n";
    echo "Then uncomment (remove the semicolon) the line: extension={$missing[0]}\n";
} else {
    echo "✅ All required extensions are enabled!\n";
}

// Check OpenSSL specifically
echo "\n=== OpenSSL Details ===\n";
if (extension_loaded('openssl')) {
    echo "✅ OpenSSL Extension: ENABLED\n";
    echo "OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";
    
    // Test the specific function that's failing
    if (function_exists('openssl_cipher_iv_length')) {
        echo "✅ openssl_cipher_iv_length() function: AVAILABLE\n";
    } else {
        echo "❌ openssl_cipher_iv_length() function: NOT AVAILABLE\n";
        echo "   This is unusual. Try restarting your web server.\n";
    }
} else {
    echo "❌ OpenSSL Extension: NOT ENABLED\n";
    echo "\nTo enable OpenSSL on Windows:\n";
    echo "1. Open your php.ini file (run: php --ini to find it)\n";
    echo "2. Find the line: ;extension=openssl\n";
    echo "3. Remove the semicolon: extension=openssl\n";
    echo "4. Save the file and restart your web server/PHP-FPM\n";
    echo "5. If extension=openssl doesn't exist, add it to the file\n";
}
