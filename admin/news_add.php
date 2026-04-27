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
    
    <!-- Summernote Dependencies (jQuery & Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Summernote CDN -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <style>
        :root { --sidebar-width: 280px; }
        body { display: flex; min-height: 100vh; background: #050810; font-family: 'Inter', sans-serif; color: white; }
        aside { width: var(--sidebar-width); background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-right: 1px solid var(--glass-border); padding: 2rem; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 10; overflow-y: auto; }
        aside::-webkit-scrollbar { display: none; }
        aside { -ms-overflow-style: none; scrollbar-width: none; }
        main { margin-left: var(--sidebar-width); flex: 1; padding: 3rem; }
        .form-card { background: var(--glass); padding: 3rem; border-radius: 24px; border: 1px solid var(--glass-border); max-width: 900px; }
        .form-group { margin-bottom: 2rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-weight: 600; }
        input[type="text"], input[type="file"] { width: 100%; padding: 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 12px; color: white; }
        
        /* Summernote Overrides for Dark Mode */
        .note-editor { background: white; border-radius: 12px; overflow: hidden; color: #333; }
        .btn-primary { background: #6366f1 !important; border: none; border-radius: 50px; padding: 1rem 2.5rem; }
    </style>
</head>
<body>
    <aside>
        <div class="logo">MYSEKOLAH <span style="font-size: 0.8rem; color: var(--text-muted)">ADMIN</span></div>
        <div style="margin-top: 2rem;">
            <a href="news.php" style="color: var(--text-muted); text-decoration: none;">← Kembali ke List</a>
        </div>
    </aside>

    <main>
        <h1 style="margin-bottom: 2rem; font-family: 'Outfit', sans-serif;">Tambah Berita Baru</h1>
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
        });
    </script>
</body>
</html>


