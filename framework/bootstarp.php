<?php
// ======================
// Environment Setup
// ======================
error_reporting(E_ALL);

// PHP Version Check
if (version_compare(phpversion(), '8.0.0', '<')) {
    exit('PHP 8.0+ Required');
}
// Timezone Configuration
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Tehran');
}

// Environment Detection
define('ENVIRONMENT', 'development');

// ======================
// Security Headers
// ======================
header_remove('X-Powered-By');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

// ======================
// Dependency Initialization
// ======================

// Vendor Autoload
require_once(DIR_STORAGE . "/vendor/autoload.php");

// Core Registry
$registry = new \Framework\Core\Registry();

// Configuration
$config = new \Framework\Library\Config(DIR_CONFIG.'default.php');
$registry->set("config", $config);

// Error Reporting by Environment
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    $config->set('errors_display', true);
} else {
    ini_set('display_errors', 0);
    $config->set('errors_display', false);
}

// Error Handling
$log = new \Framework\Library\Log("error_log");
$registry->set("log", $log);

// Cache
$cache = new \Framework\Library\Cache();
$registry->set("cache", $cache);

// QR
// $qr = new \Framework\Library\Qr();
// $registry->set("qr", $qr);

// ======================
// Service Initialization
// ======================

// Database
try {
    $db = new \Framework\Library\Db(
        DB_HOSTNAME, 
        DB_USERNAME, 
        DB_PASSWORD, 
        DB_DATABASE, 
        DB_PORT
    );
    $db->query("SET time_zone = '+3:30'"); // Tehran time
    $registry->set("db", $db);
} catch (\PDOException $e) {
    $log->write("DB Connection Failed: " . $e->getMessage());
    throw new \RuntimeException('Database unavailable');
}

// Document
$document = new \Framework\Library\Document();
$registry->set("document", $document);

// Image
$image = new \Framework\Library\Image();
$registry->set("image", $image);

// Request
$request = new \Framework\Library\Request();
$registry->set("request", $request);

// Verify CSRF token for POST requests
if ($request->isMethod('POST') && !$request->validateCsrfToken()) {
	$response->setStatusCode(403)->json(['error' => 'Invalid CSRF token']);
}

// Response
$response = new \Framework\Library\Response();
$registry->set("response", $response);

// Session
$session = new \Framework\Library\Session($registry);
$session->start();
$registry->set("session", $session);

// Admin
$admin = new \Framework\Library\Admin($registry);
$registry->set("admin", $admin);

// ======================
// Error Handlers
// ======================
set_error_handler(function($code, $message, $file, $line) use($config, $log) {
    if (error_reporting() === 0) return false;

    $errorTypes = [
        E_NOTICE => 'Notice',
        E_USER_NOTICE => 'Notice',
        E_WARNING => 'Warning',
        E_USER_WARNING => 'Warning',
        E_ERROR => 'Fatal Error',
        E_USER_ERROR => 'Fatal Error'
    ];

    $error = $errorTypes[$code] ?? 'Unknown';

    if ($config->get('errors_display')) {
        echo "<b>$error</b>: $message in <b>$file</b> on line <b>$line</b>";
    }

    if ($config->get('errors_log')) {
        $log->write("PHP $error: $message in $file on line $line");
    }

    return true;
});

set_exception_handler(function($e) use ($log) {
    $log->write("Uncaught Exception: " . $e->getMessage());
    if (ENVIRONMENT === 'development') {
        echo "<pre>$e</pre>";
    }
    http_response_code(500);
});

register_shutdown_function(function() use ($log) {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
        $log->write("Shutdown Error: " . print_r($error, true));
    }
});	

// ======================
// Framework Initialization
// ======================
$registry->set('model', new \Framework\Core\Model($registry));

/* // Admin-Specific Checks
if (str_starts_with($registry->get('request')->server['REQUEST_URI'] ?? '', '/admin')) {
    // Add admin authentication check here
} */

?>
