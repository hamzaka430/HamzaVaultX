<?php
// Temporary debug script - delete after use
set_error_handler(function($severity, $message, $file, $line) {
    echo "PHP Error: $message in $file:$line\n";
});

set_exception_handler(function($e) {
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
});

try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Override APP_DEBUG
    putenv('APP_DEBUG=true');
    $_ENV['APP_DEBUG'] = 'true';
    $_SERVER['APP_DEBUG'] = 'true';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() >= 400) {
        // Try to get exception from response
        if ($response->exception) {
            echo "Exception: " . get_class($response->exception) . "\n";
            echo "Message: " . $response->exception->getMessage() . "\n";
            echo "File: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
            echo "Trace:\n" . $response->exception->getTraceAsString() . "\n";
            
            if ($response->exception->getPrevious()) {
                echo "\nPrevious Exception: " . get_class($response->exception->getPrevious()) . "\n";
                echo "Message: " . $response->exception->getPrevious()->getMessage() . "\n";
            }
        }
    }
    
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "CAUGHT: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
