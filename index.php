<?php

/**
 * Simple .env Loader
 */
function loadEnv($path)
{
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

loadEnv(__DIR__ . '/.env');

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($name && $email && $message) {
        // Re-use db connection logic briefly
        $db_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $db_port = $_ENV['DB_PORT'] ?? '3306';
        $db_name = $_ENV['DB_NAME'] ?? 'db_cms_sekolahku';
        $db_user = $_ENV['DB_USER'] ?? 'root';
        $db_pass = $_ENV['DB_PASS'] ?? '';
        $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);

        $stmt = $pdo->prepare("INSERT INTO ms_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        $success_msg = "Pesan Anda telah terkirim!";
    }
}

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
$schoolTagline = $_ENV['SCHOOL_TAGLINE'] ?? 'Excellence in Education';

// Fetch settings from DB
$db_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db_port = $_ENV['DB_PORT'] ?? '3306';
$db_name = $_ENV['DB_NAME'] ?? 'mysekolah';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$pdo_init = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
$settings_raw = $pdo_init->query("SELECT * FROM ms_settings")->fetchAll(PDO::FETCH_ASSOC);
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['s_key']] = $s['s_value'];
}
$socials = [];
try {
    $socials = $pdo_init->query("SELECT * FROM ms_socials ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// --- License Check (independent dari db.php) ---
define('LICENSE_SALT', 'SURYADRAGN-SECRET-2026-!@#XQZP');
$_appStatus = $settings['app_status'] ?? 'inactive';
$_appLicenseKey = $settings['app_license_key'] ?? '';
$_trialStartedAt = $settings['trial_started_at'] ?? '';
$_trialDays = 14;
$_isAllowed = false;

$_currentDomain = strtolower(preg_replace('/^www\./', '', explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0]));
$_validKey = strtoupper(substr(hash('sha256', $_currentDomain . LICENSE_SALT), 0, 8) . '-' .
       substr(hash('sha256', LICENSE_SALT . $_currentDomain), 8, 8) . '-' .
       substr(hash('sha256', $_currentDomain . $_currentDomain . LICENSE_SALT), 16, 8));

if ($_appStatus === 'active' && $_appLicenseKey === $_validKey) {
    $_isAllowed = true;
} elseif ($_appStatus === 'trial' && !empty($_trialStartedAt)) {
    $trialStart = new DateTime($_trialStartedAt);
    $daysUsed = (new DateTime())->diff($trialStart)->days;
    if ($daysUsed < $_trialDays) {
        $_isAllowed = true;
        $GLOBALS['trialDaysLeft'] = $_trialDays - $daysUsed;
    }
}
if (!$_isAllowed) {
    header('Location: activate.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appName; ?> | Portal Modern</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="description" content="Portal modern MySekolah dengan desain premium dan fitur lengkap.">
    <?php if(!empty($settings['site_favicon'])): ?>
    <link rel="icon" href="uploads/<?php echo $settings['site_favicon']; ?>">
    <?php endif; ?>
</head>

<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <?php if (isset($GLOBALS['trialDaysLeft'])): ?>
    <div style="background: linear-gradient(90deg, #7c3aed, #4f46e5); color: white; text-align: center; padding: 0.7rem 1rem; font-size: 0.9rem; position: sticky; top: 0; z-index: 9999;">
        ⏳ Mode Trial — <strong><?php echo $GLOBALS['trialDaysLeft']; ?> hari tersisa</strong>. Untuk aktivasi penuh, hubungi <a href="https://github.com/suryadragn" target="_blank" style="color: #c4b5fd; font-weight: bold;">suryadragn</a>.
    </div>
    <?php endif; ?>

    <nav>
        <div class="logo">
            <a href="#home" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <?php if(!empty($settings['site_logo'])): ?>
                    <img src="uploads/<?php echo $settings['site_logo']; ?>" alt="Logo" style="height: 55px; border-radius: 8px;">
                <?php endif; ?>
                <?php echo strtoupper($appName); ?>
            </a>
        </div>
        <button class="nav-toggle" id="navToggle">☰</button>
        <ul class="nav-links" id="navLinks">
            <li><a href="#home">Beranda</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#news">Berita</a></li>
            <li><a href="#gallery">Galeri</a></li>
            <li><a href="#admission">PPDB</a></li>
            <li><a href="#contact">Kontak</a></li>
            <li class="mobile-only"><a href="admin/login.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem; display: block;">Masuk</a></li>
        </ul>
        <a href="admin/login.php" class="btn btn-glass desktop-only" style="padding: 0.5rem 1.5rem;">Masuk</a>
    </nav>

    <section class="hero" id="home" <?php if(!empty($settings['hero_bg'])) echo 'style="background: linear-gradient(rgba(5, 8, 16, 0.8), rgba(5, 8, 16, 0.95)), url(\'uploads/' . $settings['hero_bg'] . '\') center/cover no-repeat;"'; ?>>
        <h1>Transformasi Digital <br><span style="color: var(--secondary)">Pendidikan Modern.</span></h1>
        <p><?php echo $schoolTagline; ?></p>
        <div class="cta-group">
            <a href="#admission" class="btn btn-primary">Daftar Sekarang</a>
            <a href="#about" class="btn btn-glass">Pelajari Lebih Lanjut</a>
        </div>
    </section>

    <section class="stats">
        <div class="stat-card">
            <h3><?php echo $settings['stat_students'] ?? '0'; ?></h3>
            <p>Siswa Aktif</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $settings['stat_teachers'] ?? '0'; ?></h3>
            <p>Tenaga Pendidik</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $settings['stat_achievements'] ?? '0'; ?></h3>
            <p>Prestasi Nasional</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $settings['stat_extracurriculars'] ?? '0'; ?></h3>
            <p>Ekstrakurikuler</p>
        </div>
    </section>

    <section id="news" style="padding: 5rem 10%;">
        <h2 style="font-size: 2.5rem; margin-bottom: 3rem; text-align: center;">Berita <span style="color: var(--secondary)">Terbaru</span></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php
            // Fetch News from DB
            $db_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $db_port = $_ENV['DB_PORT'] ?? '3306';
            $db_name = $_ENV['DB_NAME'] ?? 'mysekolah';
            $db_user = $_ENV['DB_USER'] ?? 'root';
            $db_pass = $_ENV['DB_PASS'] ?? '';
            $pdo_front = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);

            $stmt = $pdo_front->query("SELECT * FROM ms_news ORDER BY created_at DESC LIMIT 3");
            $news_count = 0;
            while ($row = $stmt->fetch()):
                $news_count++;
                $img = $row['image'] ? 'uploads/' . $row['image'] : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=1000';
            ?>
                <div class="stat-card" style="text-align: left; padding: 0; overflow: hidden;">
                    <div style="height: 200px; background: url('<?php echo $img; ?>') center/cover no-repeat;"></div>
                    <div style="padding: 2rem;">
                        <span style="color: var(--secondary); font-size: 0.8rem; font-weight: 700;">BERITA</span>
                        <h4 style="margin: 1rem 0; font-size: 1.25rem;"><?php echo $row['title']; ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;"><?php echo substr(strip_tags($row['content']), 0, 100); ?>...</p>
                        <a href="read_news.php?id=<?php echo $row['id']; ?>" style="color: var(--primary); text-decoration: none; display: block; margin-top: 1rem; font-weight: 600;">Baca Selengkapnya →</a>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($news_count == 0): ?>
                <div style="text-align: center; grid-column: 1 / -1; color: var(--text-muted); padding: 3rem; background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border);">
                    <p>Mohon maaf, belum ada berita yang dipublikasikan saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($news_count > 0): ?>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="news.php" class="btn btn-glass">Lihat Semua Berita →</a>
            </div>
        <?php endif; ?>
    </section>

    <section id="gallery" style="padding: 5rem 10%;">
        <h2 style="font-size: 2.5rem; margin-bottom: 3rem; text-align: center;">Galeri <span style="color: var(--secondary)">Sekolah</span></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php
            $stmt_gal = $pdo_front->query("SELECT * FROM ms_gallery ORDER BY created_at DESC LIMIT 3");
            $gal_count = 0;
            while ($gal = $stmt_gal->fetch()):
                $gal_count++;
            ?>
                <div style="height: 300px; background: url('uploads/<?php echo $gal['image']; ?>') center/cover no-repeat; border-radius: 20px; border: 1px solid var(--glass-border); position: relative; overflow: hidden;" class="stat-card" title="<?php echo $gal['title']; ?>">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <p style="font-weight: 600; font-size: 1.1rem;"><?php echo $gal['title']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($gal_count == 0): ?>
                <div style="text-align: center; grid-column: 1 / -1; color: var(--text-muted); padding: 3rem; background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border);">
                    <p>Koleksi galeri foto sekolah belum tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($gal_count > 0): ?>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="gallery.php" class="btn btn-glass">Lihat Selengkapnya →</a>
            </div>
        <?php endif; ?>
    </section>

    <section id="about" style="padding: 5rem 10%; background: rgba(255,255,255,0.02);">
        <div style="display: flex; gap: 4rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Membangun <span style="color: var(--primary)">Masa Depan</span> Digital</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">MySekolah berkomitmen untuk memberikan pendidikan berkualitas tinggi dengan mengintegrasikan teknologi terbaru dalam setiap proses belajar mengajar. Kami percaya bahwa setiap siswa memiliki potensi unik yang harus dikembangkan.</p>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--secondary);">✓</span> Kurikulum Standar Internasional
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--secondary);">✓</span> Fasilitas Lab Modern
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--secondary);">✓</span> Ekosistem Digital Terpadu
                    </li>
                </ul>
            </div>
            <div style="flex: 1; min-width: 300px; position: relative;">
                <div class="stat-card" style="height: 400px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(6, 182, 212, 0.2));">
                    <div style="position: absolute; bottom: -20px; right: -20px; background: var(--primary); padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
                        <h4 style="font-size: 2rem;">25+</h4>
                        <p style="font-size: 0.8rem; opacity: 0.8;">Tahun Pengalaman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="admission" style="padding: 8rem 10%; text-align: center;">
        <div class="stat-card" style="background: linear-gradient(to right, rgba(99, 102, 241, 0.1), rgba(6, 182, 212, 0.1)); padding: 4rem;">
            <h2 style="font-size: 3rem; margin-bottom: 1.5rem;">Penerimaan Siswa Baru <br>Tahun Ajaran <?php echo $settings['academic_year'] ?? '2026/2027'; ?></h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 2.5rem;">Bergabunglah dengan komunitas pembelajar kami dan mulailah perjalanan akademik Anda menuju kesuksesan.</p>
            <a href="admission.php" class="btn btn-primary">Daftar Sekarang Secara Online</a>
        </div>
    </section>

    <section id="contact" style="padding: 5rem 10%;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem;">
            <div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Hubungi Kami</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Punya pertanyaan? Tim kami siap membantu Anda kapan saja.</p>
                <div style="margin-bottom: 1.5rem;">
                    <p style="font-weight: 700; color: var(--secondary);">Alamat</p>
                    <p>Jl. Pendidikan No. 123, Jakarta Selatan, Indonesia</p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <p style="font-weight: 700; color: var(--secondary);">Email</p>
                    <p><?php echo $_ENV['SCHOOL_EMAIL'] ?? 'info@mysekolah.edu'; ?></p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <p style="font-weight: 700; color: var(--secondary);">Telepon</p>
                    <p><?php echo $_ENV['SCHOOL_PHONE'] ?? '+62 21 1234 5678'; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <?php if (isset($success_msg)): ?>
                    <p style="color: #22c55e; margin-bottom: 1.5rem; font-weight: 600;"><?php echo $success_msg; ?></p>
                <?php endif; ?>
                <form action="" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <input type="text" name="name" placeholder="Nama Lengkap" required style="padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px;">
                    <input type="email" name="email" placeholder="Email Anda" required style="padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px;">
                    <textarea name="message" placeholder="Pesan Anda" rows="5" required style="padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px;"></textarea>
                    <button type="submit" name="send_message" class="btn btn-primary">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </section>

    <footer style="padding: 5rem 10%; background: #070b14; border-top: 1px solid var(--glass-border);">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 3rem; margin-bottom: 3rem;">
            <div style="max-width: 300px;">
                <div class="logo" style="margin-bottom: 1.5rem;"><?php echo strtoupper($appName); ?></div>
                <p style="color: var(--text-muted);">Platform pendidikan masa depan yang memberdayakan setiap individu untuk meraih potensi maksimalnya.</p>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem;">Navigasi</h4>
                <ul style="list-style: none; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.8rem;">
                    <li><a href="#home" style="color: inherit; text-decoration: none;">Beranda</a></li>
                    <li><a href="#about" style="color: inherit; text-decoration: none;">Tentang</a></li>
                    <li><a href="#news" style="color: inherit; text-decoration: none;">Berita</a></li>
                    <li><a href="#admission" style="color: inherit; text-decoration: none;">PPDB</a></li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem;">Ikuti Kami</h4>
                <div style="display: flex; gap: 1rem;">
                    <?php if (!empty($socials)): ?>
                        <?php foreach ($socials as $soc): ?>
                            <a href="<?php echo htmlspecialchars($soc['url']); ?>" target="_blank" style="text-decoration: none; color: inherit;">
                                <div style="width: 40px; height: 40px; background: var(--glass); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;" onmouseover="this.style.background='var(--secondary)'; this.style.color='#fff';" onmouseout="this.style.background='var(--glass)'; this.style.color='inherit';">
                                    <?php echo htmlspecialchars($soc['icon_text']); ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada tautan sosial media.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div style="border-top: 1px solid var(--glass-border); padding-top: 2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            <p>&copy; 2026 <?php echo $appName; ?>. All Rights Reserved. Developed by Antigravity.</p>
        </div>
    </footer>
    <script>
        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Mobile Nav Toggle & Scroll Effect
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.getElementById('navLinks');
        const nav = document.querySelector('nav');
        
        if (navToggle && navLinks) {
            navToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                navLinks.classList.toggle('active');
                navToggle.textContent = navLinks.classList.contains('active') ? '✕' : '☰';
            });

            // Close menu when clicking link
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                    navToggle.textContent = '☰';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!nav.contains(e.target) && navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    navToggle.textContent = '☰';
                }
            });
        }

        // Sticky Nav Blur on Scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(15, 23, 42, 0.95)';
                nav.style.padding = '1rem 5%';
            } else {
                nav.style.background = 'rgba(15, 23, 42, 0.8)';
                nav.style.padding = '1.5rem 5%';
            }
        });
    </script>
</body>

</html>