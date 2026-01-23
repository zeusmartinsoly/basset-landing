<?php
// Upload this to public_html/public/test.php
// Then visit: brandatcamp.com/test.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP is working!</h1>";

echo "<h2>Checking paths...</h2>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Parent directory: " . dirname(__DIR__) . "</p>";

// Check if key files exist
$checks = [
    'vendor/autoload.php' => __DIR__ . '/../vendor/autoload.php',
    'bootstrap/app.php' => __DIR__ . '/../bootstrap/app.php',
    '.env file' => __DIR__ . '/../.env',
    'storage folder' => __DIR__ . '/../storage',
    'storage/logs' => __DIR__ . '/../storage/logs',
];

echo "<h2>File checks:</h2>";
echo "<ul>";
foreach ($checks as $name => $path) {
    $exists = file_exists($path);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    echo "<li style='color: $color'>$name: $status</li>";
}
echo "</ul>";

// Check .env content
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check for APP_KEY
    if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
        $key = trim($matches[1]);
        if (empty($key)) {
            echo "<p style='color: red'>⚠️ APP_KEY is EMPTY! Run: php artisan key:generate</p>";
        } else {
            echo "<p style='color: green'>✓ APP_KEY is set</p>";
        }
    } else {
        echo "<p style='color: red'>⚠️ APP_KEY not found in .env</p>";
    }
}

// Check storage permissions
$storagePath = __DIR__ . '/../storage';
if (file_exists($storagePath)) {
    $writable = is_writable($storagePath);
    $color = $writable ? 'green' : 'red';
    $status = $writable ? '✓ Writable' : '✗ NOT Writable - Run: chmod -R 755 storage';
    echo "<p style='color: $color'>Storage folder: $status</p>";
}

echo "<h2>PHP Version: " . phpversion() . "</h2>";

// Try to load Laravel
echo "<h2>Trying to load Laravel...</h2>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "<p style='color: green'>✓ Autoloader loaded successfully</p>";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "<p style='color: green'>✓ Application bootstrapped successfully</p>";
} catch (Throwable $e) {
    echo "<p style='color: red'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
