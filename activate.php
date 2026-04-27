<?php
require_once 'admin/db.php';

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
$masterKey = $_ENV['MASTER_LICENSE_KEY'] ?? '';
$trialDays = 14;

$appStatus = $globalSettings['app_status'] ?? 'inactive';
$trialStartedAt = $globalSettings['trial_started_at'] ?? '';

// Check if already fully active
if ($appStatus === 'active') {
    redirect('index.php');
}

// Check if trial is still valid
if ($appStatus === 'trial' && !empty($trialStartedAt)) {
    $trialStart = new DateTime($trialStartedAt);
    $now = new DateTime();
    $daysUsed = $now->diff($trialStart)->days;
    if ($daysUsed < $trialDays) {
        redirect('index.php'); // still valid, let them in
    }
    // trial expired — fall through to activation page
}

$error = '';
$info = '';

// Handle full activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate'])) {
    $inputKey = $_POST['license_key'] ?? '';

    if (!empty($masterKey) && $inputKey === $masterKey) {
        $pdo->exec("UPDATE ms_settings SET s_value = 'active' WHERE s_key = 'app_status'");
        $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = 'app_license_key'")->execute([$inputKey]);
        redirect('index.php');
    } else {
        $error = "License key tidak valid. Silakan hubungi author untuk mendapatkan key resmi.";
    }
}

// Handle start trial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_trial'])) {
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $pdo->exec("UPDATE ms_settings SET s_value = 'trial' WHERE s_key = 'app_status'");
    $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = 'trial_started_at'")->execute([$now]);
    redirect('index.php');
}

// Calculate days left if trial expired
$trialExpiredMsg = '';
if ($appStatus === 'trial' && !empty($trialStartedAt)) {
    $trialExpiredMsg = "Masa trial Anda telah berakhir. Silakan masukkan License Key untuk melanjutkan.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Aplikasi | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #050810;
        }
        .activation-card {
            background: var(--glass);
            padding: 3rem;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            width: 100%;
            max-width: 520px;
            text-align: center;
            backdrop-filter: blur(20px);
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
        }
        input {
            width: 100%;
            padding: 1.2rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input:focus {
            border-color: var(--primary);
            outline: none;
            background: rgba(255,255,255,0.08);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--glass-border);
        }
        .trial-box {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="activation-card">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔑</div>
        <h1 style="margin-bottom: 0.5rem; color: var(--secondary);">Aktivasi Sistem</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem;">
            Aplikasi ini dilindungi lisensi oleh <strong>suryadragn</strong>.
        </p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($trialExpiredMsg): ?>
            <div class="error-msg"><?php echo $trialExpiredMsg; ?></div>
        <?php endif; ?>

        <!-- License Key Form -->
        <form action="" method="POST">
            <div class="form-group">
                <label>Masukkan License Key</label>
                <input type="text" name="license_key" placeholder="Contoh: MYSEKOLAH-PRO-XXXX">
            </div>
            <button type="submit" name="activate" class="btn btn-primary" style="width: 100%; padding: 1.1rem;">
                ✅ Aktivasi Penuh
            </button>
        </form>

        <?php if (empty($trialStartedAt)): // Only show trial button if never tried before ?>
        <div class="divider">atau</div>

        <div class="trial-box">
            <p style="margin: 0 0 0.5rem 0; font-weight: 600;">Coba Gratis <?php echo $trialDays; ?> Hari</p>
            <p style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.85rem;">
                Akses semua fitur tanpa batasan selama <?php echo $trialDays; ?> hari. Tidak perlu key.
            </p>
            <form action="" method="POST">
                <button type="submit" name="start_trial" class="btn btn-glass" style="width: 100%; padding: 1.1rem;">
                    🚀 Mulai Trial <?php echo $trialDays; ?> Hari
                </button>
            </form>
        </div>
        <?php endif; ?>

        <p style="margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            Ingin lisensi penuh? Hubungi
            <a href="https://github.com/suryadragn" target="_blank" style="color: var(--accent);">suryadragn</a>
        </p>
    </div>
</body>
</html>
