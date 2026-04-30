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
        :root {
            --sidebar-width: 280px;
        }
        body {
            display: flex;
            min-height: 100vh;
            background: #050810;
        }
        /* Sidebar Customization */
        aside {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        aside::-webkit-scrollbar { display: none; }
        aside { -ms-overflow-style: none; scrollbar-width: none; }
        .admin-nav {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .admin-nav-item {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-nav-item:hover, .admin-nav-item.active {
            background: var(--glass);
            color: var(--secondary);
            border: 1px solid var(--glass-border);
        }
        /* Main Content */
        main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 3rem;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
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
<body>
    <div class="mobile-admin-header" style="display: none;">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'dashboard';
    require 'layout/sidebar.php'; 
    ?>

    <main>
        <div class="dashboard-header">
            <div>
                <h1 style="font-size: 2rem;">Halo, <?php echo $_SESSION['full_name']; ?></h1>
                <p style="color: var(--text-muted)">Selamat datang kembali di panel kendali MySekolah.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div style="text-align: right;">
                    <p style="font-weight: 600;"><?php echo $_SESSION['full_name']; ?></p>
                    <p style="font-size: 0.8rem; color: var(--text-muted)">Administrator</p>
                </div>
                <div style="width: 45px; height: 45px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800;">A</div>
            </div>
        </div>

        <div class="card-grid">
            <div class="stat-card" style="text-align: left; padding: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Pendaftar PPDB</p>
                <h3 style="font-size: 2rem; margin: 0.5rem 0;"><?php echo $admissionCount; ?></h3>
                <p style="color: var(--secondary); font-size: 0.8rem;">Total siswa mendaftar</p>
            </div>
            <div class="stat-card" style="text-align: left; padding: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Total Berita</p>
                <h3 style="font-size: 2rem; margin: 0.5rem 0;"><?php echo $newsCount; ?></h3>
                <p style="color: var(--secondary); font-size: 0.8rem;">Konten aktif</p>
            </div>
            <div class="stat-card" style="text-align: left; padding: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Pesan Baru</p>
                <h3 style="font-size: 2rem; margin: 0.5rem 0;"><?php echo $messageCount; ?></h3>
                <p style="color: var(--accent); font-size: 0.8rem;">Perlu ditanggapi</p>
            </div>
        </div>

        <h2 style="margin-bottom: 1.5rem;">Pendaftar PPDB Terbaru</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Tanggal Daftar</th>
                        <th>Asal Sekolah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAdmissions as $row): ?>
                    <tr>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td><?php echo $row['school_origin']; ?></td>
                        <td><span class="badge badge-<?php echo $row['status'] === 'verified' ? 'success' : 'warning'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td><a href="admission.php" style="color: var(--secondary); text-decoration: none;">Detail</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentAdmissions)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada data pendaftar.</td></tr>
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

