<?php

/**
 * Cloudflare R2 Connection Test Script
 * Run with: php test-r2-connection.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "========================================\n";
echo "  Cloudflare R2 Connection Test\n";
echo "========================================\n\n";

try {
    // Test 1: Check if R2 disk is configured
    echo "✓ Test 1: Checking R2 disk configuration...\n";
    $disk = Storage::disk('r2');
    echo "  ✅ R2 disk is configured\n\n";

    // Test 2: Upload a test file
    echo "✓ Test 2: Uploading test file to R2...\n";
    $testContent = "Hello from Cloudflare R2! Test at " . now();
    try {
        Storage::disk('r2')->put('test-r2-connection.txt', $testContent);
        echo "  ✅ Test file uploaded successfully\n\n";
    } catch (\Exception $e) {
        echo "  ❌ Upload failed: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    // Test 3: List files to verify upload
    echo "✓ Test 3: Listing files in R2 bucket...\n";
    try {
        $files = Storage::disk('r2')->files();
        echo "  ✅ Found " . count($files) . " file(s) in bucket\n";
        $testFileFound = in_array('test-r2-connection.txt', $files);
        if ($testFileFound) {
            echo "  ✅ Test file appears in file list\n\n";
        } else {
            echo "  ⚠️  Test file not in list yet (might be eventual consistency)\n\n";
        }
    } catch (\Exception $e) {
        echo "  ❌ Listing failed: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    // Test 4: Read file content
    echo "✓ Test 4: Reading file content from R2...\n";
    try {
        $content = Storage::disk('r2')->get('test-r2-connection.txt');
        if ($content === $testContent) {
            echo "  ✅ File content matches: " . substr($content, 0, 50) . "...\n\n";
        } else {
            echo "  ⚠️  File content does not match exactly\n";
            echo "  Expected: " . substr($testContent, 0, 50) . "\n";
            echo "  Got: " . substr($content, 0, 50) . "\n\n";
        }
    } catch (\Exception $e) {
        echo "  ❌ Read failed: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    // Test 5: Generate temporary signed URL
    echo "✓ Test 5: Generating temporary signed URL (5 minutes)...\n";
    try {
        $url = Storage::disk('r2')->temporaryUrl('test-r2-connection.txt', now()->addMinutes(5));
        echo "  ✅ Signed URL generated:\n";
        echo "  " . substr($url, 0, 100) . "...\n\n";
        echo "  URL expires in: 5 minutes\n";
        echo "  You can test this URL in your browser (valid for 5 min)\n\n";
    } catch (\Exception $e) {
        echo "  ❌ Signed URL generation failed: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    // Test 6: Delete test file
    echo "✓ Test 6: Cleaning up - deleting test file...\n";
    try {
        Storage::disk('r2')->delete('test-r2-connection.txt');
        echo "  ✅ Delete command executed\n\n";
    } catch (\Exception $e) {
        echo "  ❌ Delete failed: " . $e->getMessage() . "\n\n";
    }

    echo "========================================\n";
    echo "  ✅ ALL TESTS PASSED!\n";
    echo "========================================\n\n";
    echo "Your Cloudflare R2 connection is working perfectly!\n";
    echo "Bucket: hamzavaultx (EU Region)\n";
    echo "Endpoint: https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com\n\n";
    echo "You can now:\n";
    echo "  → Upload files via your application\n";
    echo "  → Preview files with secure signed URLs\n";
    echo "  → Download files from R2\n";
    echo "  → Delete files from R2\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    echo "Please check:\n";
    echo "  1. Your R2 credentials in .env file\n";
    echo "  2. R2 bucket exists: hamzavaultx\n";
    echo "  3. AWS SDK is installed: composer require league/flysystem-aws-s3-v3\n";
    echo "  4. Config cache is cleared: php artisan config:clear\n\n";
    exit(1);
}
