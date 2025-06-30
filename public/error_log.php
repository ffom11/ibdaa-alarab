<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set error log path
$logFile = __DIR__ . '/../storage/logs/php_errors.log';
ini_set('error_log', $logFile);

// Test log entry
error_log('Error log file loaded successfully');

// Display log file path
echo "Error log file: " . realpath($logFile) . "<br>";
echo "<pre>";
if (file_exists($logFile)) {
    echo htmlspecialchars(file_get_contents($logFile));
} else {
    echo "Log file does not exist. Check directory permissions.";
}
echo "</pre>";
?>
