<?php
session_start();

if (!defined('LICENSE_SALT')) {
    $__a = 'U1VSWUFkUkFHTi1TRUNSRVQtMjAyNi0hQCNYUVpQ';
    define('LICENSE_SALT', base64_decode($__a));
    unset($__a);
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
    $h = strtolower(trim((string)$host));
    $h = explode(':', $h)[0];
    $w = 'w' . 'w' . 'w';
    return preg_replace('/^' . $w . '\./', '', $h);
}

function getCurrentDomain() {
    $k = 'HT' . 'TP_' . 'HO' . 'ST';
    return normalizeDomain($_SERVER[$k] ?? 'localhost');
}

function generateLicenseKeyForDomain($domain) {
    $d = normalizeDomain($domain);
    $h = 'sh' . 'a256';
    $a = hash($h, $d . LICENSE_SALT);
    $b = hash($h, LICENSE_SALT . $d);
    $c = hash($h, $d . $d . LICENSE_SALT);
    return strtoupper(substr($a, 0, 8) . '-' . substr($b, 8, 8) . '-' . substr($c, 16, 8));
}

function isLicenseAllowed($globalSettings) {
    $s1 = 'app' . '_' . 'status';
    $s2 = 'app' . '_' . 'license' . '_' . 'key';
    $s3 = 'trial' . '_' . 'started' . '_' . 'at';
    $t = 14;
    $st = $globalSettings[$s1] ?? 'inactive';
    $lk = strtoupper(trim($globalSettings[$s2] ?? ''));
    $ts = $globalSettings[$s3] ?? '';

    $cd = getCurrentDomain();
    $vk = generateLicenseKeyForDomain($cd);

    if ($st === 'active' && $lk !== '' && hash_equals($vk, $lk)) {
        return true;
    }

    if ($st === 'trial' && !empty($ts)) {
        $trialStart = new DateTime($ts);
        $now = new DateTime();
        $daysUsed = $now->diff($trialStart)->days;
        if ($daysUsed < $t) {
            $GLOBALS['trialDaysLeft'] = $t - $daysUsed;
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

$__f = basename($_SERVER['P' . 'HP_SELF'] ?? '');
$__a = 'act' . 'ivate' . '.php';
if ($__f !== $__a) {
    if (!isLicenseAllowed($globalSettings)) {
        $__d = dirname($_SERVER['P' . 'HP_SELF'] ?? '');
        $__p = (basename($__d) === ('ad' . 'min')) ? '../' : '';
        redirect($__p . $__a);
    }
}
unset($__f, $__a, $__d, $__p);
?>
