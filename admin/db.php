<?php
session_start();

if (!defined('LICENSE_SALT')) {
    define('LICENSE_SALT', 'SURYADRAGN-SECRET-2026-!@#XQZP');
}

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

function normalizeDomain($host) {
    $host = strtolower(trim($host));
    $host = explode(':', $host)[0];
    return preg_replace('/^www\./', '', $host);
}

function getCurrentDomain() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return normalizeDomain($host);
}

function generateLicenseKeyForDomain($domain) {
    $clean = normalizeDomain($domain);
    return strtoupper(
        substr(hash('sha256', $clean . LICENSE_SALT), 0, 8) . '-' .
        substr(hash('sha256', LICENSE_SALT . $clean), 8, 8) . '-' .
        substr(hash('sha256', $clean . $clean . LICENSE_SALT), 16, 8)
    );
}

function isLicenseAllowed($globalSettings) {
    $appStatus = $globalSettings['app_status'] ?? 'inactive';
    $appLicenseKey = strtoupper(trim($globalSettings['app_license_key'] ?? ''));
    $trialStartedAt = $globalSettings['trial_started_at'] ?? '';
    $trialDays = 14;

    $currentDomain = getCurrentDomain();
    $validKey = generateLicenseKeyForDomain($currentDomain);

    if ($appStatus === 'active' && $appLicenseKey !== '' && hash_equals($validKey, $appLicenseKey)) {
        return true;
    }

    if ($appStatus === 'trial' && !empty($trialStartedAt)) {
        $trialStart = new DateTime($trialStartedAt);
        $now = new DateTime();
        $daysUsed = $now->diff($trialStart)->days;
        if ($daysUsed < $trialDays) {
            $GLOBALS['trialDaysLeft'] = $trialDays - $daysUsed;
            return true;
        }
    }

    return false;
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
    if (!isLicenseAllowed($globalSettings)) {
        $baseDir = dirname($_SERVER['PHP_SELF']);
        $prefix = (basename($baseDir) === 'admin') ? '../' : '';
        redirect($prefix . 'activate.php');
    }
}
?>
