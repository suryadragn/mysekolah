<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Fetch stats
$newsCount = $pdo->query("SELECT COUNT(*) FROM ms_news")->fetchColumn();
$admissionCount = $pdo->query("SELECT COUNT(*) FROM ms_admission")->fetchColumn();
$messageCount = $pdo->query("SELECT COUNT(*) FROM ms_messages")->fetchColumn();

// Fetch recent admissions
$recentAdmissions = $pdo->query("SELECT * FROM ms_admission ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .table-container {
            background: var(--glass);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
        }
        th {
            background: rgba(255,255,255,0.02);
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-warning { background: rgba(234, 179, 8, 0.1); color: #eab308; }
    </style>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'dashboard';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
        <div class="dashboard-header">
            <div>
                <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem;">Halo, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?> 👋</h1>
                <p style="color: var(--text-muted)">Selamat datang kembali di panel kendali MySekolah.</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center; background: var(--glass); padding: 0.8rem 1.5rem; border-radius: 16px; border: 1px solid var(--glass-border);">
                <div style="text-align: right;">
                    <p style="font-weight: 600; font-size: 0.95rem;"><?php echo $_SESSION['full_name']; ?></p>
                    <p style="font-size: 0.75rem; color: var(--secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Administrator</p>
                </div>
                <div style="width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                    <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                </div>
            </div>
        </div>

        <div class="card-grid">
            <div class="stat-card" style="text-align: left; padding: 2rem; border-left: 4px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <p style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Pendaftar PPDB</p>
                    <span style="font-size: 1.5rem;">📝</span>
                </div>
                <h3 style="font-size: 2.5rem; margin: 0.5rem 0; color: white;"><?php echo $admissionCount; ?></h3>
                <p style="color: var(--secondary); font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: var(--secondary); border-radius: 50%;"></span>
                    Total siswa mendaftar
                </p>
            </div>
            <div class="stat-card" style="text-align: left; padding: 2rem; border-left: 4px solid var(--secondary);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <p style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Total Berita</p>
                    <span style="font-size: 1.5rem;">📰</span>
                </div>
                <h3 style="font-size: 2.5rem; margin: 0.5rem 0; color: white;"><?php echo $newsCount; ?></h3>
                <p style="color: var(--secondary); font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: var(--secondary); border-radius: 50%;"></span>
                    Konten aktif di web
                </p>
            </div>
            <div class="stat-card" style="text-align: left; padding: 2rem; border-left: 4px solid var(--accent);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <p style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Pesan Baru</p>
                    <span style="font-size: 1.5rem;">📧</span>
                </div>
                <h3 style="font-size: 2.5rem; margin: 0.5rem 0; color: white;"><?php echo $messageCount; ?></h3>
                <p style="color: var(--accent); font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></span>
                    Perlu ditanggapi
                </p>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem;">Pendaftar PPDB Terbaru</h2>
            <a href="admission.php" style="color: var(--secondary); text-decoration: none; font-size: 0.9rem; font-weight: 600;">Lihat Semua →</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Tanggal Daftar</th>
                        <th>Asal Sekolah</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAdmissions as $row): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?php echo $row['full_name']; ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $row['email']; ?></div>
                        </td>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td><?php echo $row['school_origin']; ?></td>
                        <td><span class="badge badge-<?php echo $row['status'] === 'verified' ? 'success' : 'warning'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td style="text-align: right;"><a href="admission.php" class="btn btn-glass" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Detail</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentAdmissions)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">Belum ada data pendaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) {
            openSidebar.addEventListener('click', () => {
                sidebar.classList.add('active');
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }

        // Show/hide close button based on screen size
        function checkWidth() {
            if (window.innerWidth <= 992) {
                closeSidebar.style.display = 'block';
            } else {
                closeSidebar.style.display = 'none';
                sidebar.classList.remove('active');
            }
        }

        window.addEventListener('resize', checkWidth);
        checkWidth();
    </script>
</body>
</html>

