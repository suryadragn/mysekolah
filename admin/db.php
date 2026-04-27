<?php
session_start();

function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"");
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');

$db_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db_port = $_ENV['DB_PORT'] ?? '3306';
$db_name = $_ENV['DB_NAME'] ?? 'db_cms_sekolahku';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($path) {
    header("Location: $path");
    exit();
}

// Fetch global settings
$globalSettings = [];
try {
    $stmt = $pdo->query("SELECT * FROM ms_settings");
    if ($stmt) {
        foreach ($stmt->fetchAll() as $s) {
            $globalSettings[$s['s_key']] = $s['s_value'];
        }
    }
} catch (PDOException $e) {
    // Ignore if table doesn't exist yet
}

// License Activation Check
$currentFile = basename($_SERVER['PHP_SELF']);
if ($currentFile !== 'activate.php') {
    $appStatus = $globalSettings['app_status'] ?? 'inactive';
    $appLicenseKey = $globalSettings['app_license_key'] ?? '';
    $trialStartedAt = $globalSettings['trial_started_at'] ?? '';
    $trialDays = 14;
    $isAllowed = false;

    // Strong License Validation
    define('DB_LICENSE_SALT', 'SURYADRAGN-SECRET-2026-!@#XQZP');
    $currentDomain = strtolower(preg_replace('/^www\./', '', explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0]));
    $validKey = strtoupper(substr(hash('sha256', $currentDomain . DB_LICENSE_SALT), 0, 8) . '-' .
               substr(hash('sha256', DB_LICENSE_SALT . $currentDomain), 8, 8) . '-' .
               substr(hash('sha256', $currentDomain . $currentDomain . DB_LICENSE_SALT), 16, 8));

    if ($appStatus === 'active' && $appLicenseKey === $validKey) {
        $isAllowed = true;
    } elseif ($appStatus === 'trial' && !empty($trialStartedAt)) {
        $trialStart = new DateTime($trialStartedAt);
        $now = new DateTime();
        $daysUsed = $now->diff($trialStart)->days;
        if ($daysUsed < $trialDays) {
            $isAllowed = true;
            $GLOBALS['trialDaysLeft'] = $trialDays - $daysUsed;
        }
    }

    if (!$isAllowed) {
        $baseDir = dirname($_SERVER['PHP_SELF']);
        $prefix = (basename($baseDir) === 'admin') ? '../' : '';
        redirect($prefix . 'activate.php');
    }
}
?>
