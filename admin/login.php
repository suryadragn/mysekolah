<?php
require_once 'db.php';
if (isLoggedIn()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM ms_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        redirect('index.php');
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MySekolah Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #050810;
            overflow: hidden;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 3.5rem;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(40px);
            border-radius: 32px;
            border: 1px solid var(--glass-border);
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .form-group {
            text-align: left;
            margin-bottom: 2rem;
        }
        label {
            display: block;
            margin-bottom: 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        input {
            width: 100%;
            padding: 1.2rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="login-card">
        <div class="logo" style="margin-bottom: 2rem;">MYSEKOLAH <span style="color: var(--text-muted)">ADMIN</span></div>
        <h2 style="margin-bottom: 0.5rem;">Selamat Datang</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">Silakan masuk untuk mengelola portal sekolah.</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login ke Dashboard</button>
        </form>
    </div>
</body>
</html>
