<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = '';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '.' . $ext;
        $upload_dir = '../uploads/';
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO ms_news (title, content, image) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $image]);
    redirect('news.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita | MySekolah Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php echo $themeCss ?? ''; ?>
    
    <!-- Summernote Dependencies (jQuery & Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Summernote CDN -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <style>
        .form-card { background: var(--glass); padding: 3rem; border-radius: 24px; border: 1px solid var(--glass-border); max-width: 900px; backdrop-filter: blur(10px); }
        
        /* Summernote Overrides for Dark Mode */
        .note-editor { background: white; border-radius: 12px; overflow: hidden; color: #333; }
        .note-editor .note-editing-area { background: white; }
    </style>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName ?? 'MySekolah'); ?></div>
    </div>

    <?php 
    $page = 'news';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
         <div style="margin-bottom: 2rem;">
             <a href="news.php" style="color: var(--secondary); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                 <span>←</span> Kembali ke List Berita
             </a>
             <h1 style="margin-top: 1rem; font-family: 'Outfit', sans-serif;">Tambah Berita Baru</h1>
         </div>
         <div class="form-card">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="title" placeholder="Masukkan judul berita" required>
                </div>
                <div class="form-group">
                    <label>Gambar Utama</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Konten Berita</label>
                    <textarea name="content" id="summernote" required></textarea>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Simpan Berita</button>
                    <a href="news.php" style="color: var(--text-muted); padding: 1rem;">Batal</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Tulis konten berita di sini...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Sidebar Toggle Logic
            const sidebar = document.getElementById('sidebar');
            const openSidebar = document.getElementById('openSidebar');
            const closeSidebar = document.getElementById('closeSidebar');

            if (openSidebar) {
                openSidebar.addEventListener('click', () => sidebar.classList.add('active'));
            }
            if (closeSidebar) {
                closeSidebar.addEventListener('click', () => sidebar.classList.remove('active'));
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    if(closeSidebar) closeSidebar.style.display = 'none';
                } else {
                    if(closeSidebar) closeSidebar.style.display = 'block';
                }
            });
            
            if (window.innerWidth <= 992 && closeSidebar) closeSidebar.style.display = 'block';
        });
    </script>
</body>
</html>

