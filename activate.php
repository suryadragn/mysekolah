<?php
require_once 'admin/db.php';

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
$trialDays = 14;

$___d = getCurrentDomain();
$___k = generateLicenseKeyForDomain($___d);

$appStatus = $globalSettings['app_status'] ?? 'inactive';
$trialStartedAt = $globalSettings['trial_started_at'] ?? '';

// Already fully active
$appLicenseKey = $globalSettings['app_license_key'] ?? '';
if ($appStatus === 'active' && $appLicenseKey === $___k) {
    redirect('index.php');
}

// Trial still valid
if ($appStatus === 'trial' && !empty($trialStartedAt)) {
    $trialStart = new DateTime($trialStartedAt);
    $now = new DateTime();
    $daysUsed = $now->diff($trialStart)->days;
    if ($daysUsed < $trialDays) {
        redirect('index.php');
    }
}

$error = '';
if ($appStatus === 'active' && $appLicenseKey !== $___k && $appLicenseKey !== '') {
    $error = "Sistem mendeteksi License Key tidak valid untuk domain ini. Silakan masukkan key yang benar.";
}

// Handle full activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate'])) {
    $inputKey = strtoupper(trim($_POST['license_key'] ?? ''));

    if ($inputKey !== '' && hash_equals($___k, $inputKey)) {
        $pdo->exec("UPDATE ms_settings SET s_value = 'active' WHERE s_key = 'app_status'");
        $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = 'app_license_key'")->execute([$inputKey]);
        redirect('index.php');
    } else {
        $error = "License key tidak valid untuk domain <strong>{$___d}</strong>. Pastikan Anda meminta key ke author sesuai domain Anda.";
    }
}

// Handle start trial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_trial'])) {
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $pdo->exec("UPDATE ms_settings SET s_value = 'trial' WHERE s_key = 'app_status'");
    $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = 'trial_started_at'")->execute([$now]);
    redirect('index.php');
}

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
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <?php echo $themeCss ?? ''; ?>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #050810;
            overflow: hidden;
        }
        .activation-card {
            background: rgba(255, 255, 255, 0.02);
            padding: 3.5rem;
            border-radius: 32px;
            border: 1px solid var(--glass-border);
            width: 100%;
            max-width: 550px;
            text-align: center;
            backdrop-filter: blur(40px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 1rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        input {
            width: 100%;
            padding: 1.4rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: white;
            font-size: 1.1rem;
            box-sizing: border-box;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            letter-spacing: 2px;
            text-align: center;
            transition: all 0.3s;
        }
        input:focus {
            border-color: var(--primary);
            outline: none;
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 1.2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .error-msg::before { content: '⚠️'; }
        .divider {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin: 2.5rem 0;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--glass-border), transparent);
        }
        .trial-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            padding: 2rem;
            transition: 0.3s;
        }
        .trial-box:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }
        .domain-chip {
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 100px;
            padding: 0.5rem 1.2rem;
            font-size: 0.85rem;
            color: var(--secondary);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
        }
        @media (max-width: 480px) {
            .activation-card { padding: 2rem 1.5rem; margin: 1rem; border-radius: 24px; }
            h1 { font-size: 1.75rem; }
            input { padding: 1.1rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="activation-card">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔐</div>
        <h1 style="margin-bottom: 0.5rem; color: var(--secondary);">Aktivasi Sistem</h1>
        <p style="color: var(--text-muted); margin-bottom: 0.75rem; font-size: 0.95rem;">
            Aplikasi ini dilindungi lisensi oleh <strong>suryadragn</strong>.
        </p>
        <div class="domain-chip">🌐 <?php echo $___d; ?></div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($trialExpiredMsg): ?>
            <div class="error-msg"><?php echo $trialExpiredMsg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Masukkan License Key untuk domain ini</label>
                <input type="text" name="license_key" placeholder="XXXXXXXX-XXXXXXXX-XXXXXXXX">
            </div>
            <button type="submit" name="activate" class="btn btn-primary" style="width: 100%; padding: 1.1rem;">
                ✅ Aktivasi Penuh
            </button>
        </form>

        <?php if (empty($trialStartedAt)): ?>
        <div class="divider">atau</div>
        <div class="trial-box">
            <p style="margin: 0 0 0.5rem 0; font-weight: 600;">Coba Gratis <?php echo $trialDays; ?> Hari</p>
            <p style="margin: 0 0 1rem 0; color: var(--text-muted); font-size: 0.85rem;">
                Akses semua fitur tanpa batasan selama <?php echo $trialDays; ?> hari.
            </p>
            <form action="" method="POST">
                <button type="submit" name="start_trial" class="btn btn-glass" style="width: 100%; padding: 1rem;">
                    🚀 Mulai Trial <?php echo $trialDays; ?> Hari
                </button>
            </form>
        </div>
        <?php endif; ?>

        <p style="margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            Minta license key ke: <a href="https://github.com/suryadragn" target="_blank" style="color: var(--accent);">github.com/suryadragn</a>
        </p>
    </div>
</body>
</html>
