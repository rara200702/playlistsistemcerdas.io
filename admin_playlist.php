<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$db = getDB();
$action = isset($_GET['action']) ? $_GET['action'] : 'create';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $mood = sanitize($_POST['mood']);
    
    if ($action == 'create') {
        $stmt = $db->prepare("INSERT INTO playlists (name, description, mood) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $mood);
        $stmt->execute();
        redirect("admin.php?success=Playlist berhasil ditambahkan");
    } elseif ($action == 'edit' && $id > 0) {
        $stmt = $db->prepare("UPDATE playlists SET name = ?, description = ?, mood = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $description, $mood, $id);
        $stmt->execute();
        redirect("admin.php?success=Playlist berhasil diupdate");
    }
}

// Handle delete
if ($action == 'delete' && $id > 0) {
    $stmt = $db->prepare("DELETE FROM playlists WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    redirect("admin.php?success=Playlist berhasil dihapus");
}

// Get playlist data for edit
$playlist = null;
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM playlists WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $playlist = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action == 'edit' ? 'Edit' : 'Tambah'; ?> Playlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-music me-2"></i>Mood Playlist Generator
            </a>
            <a href="admin.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-<?php echo $action == 'edit' ? 'edit' : 'plus'; ?> me-2"></i><?php echo $action == 'edit' ? 'Edit' : 'Tambah'; ?> Playlist</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Playlist <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $playlist ? htmlspecialchars($playlist['name']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $playlist ? htmlspecialchars($playlist['description']) : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="mood" class="form-label">Mood <span class="text-danger">*</span></label>
                                <select class="form-select" id="mood" name="mood" required>
                                    <option value="">Pilih Mood</option>
                                    <option value="energi" <?php echo ($playlist && $playlist['mood'] == 'energi') ? 'selected' : ''; ?>>Energi</option>
                                    <option value="tenang" <?php echo ($playlist && $playlist['mood'] == 'tenang') ? 'selected' : ''; ?>>Tenang</option>
                                    <option value="galau" <?php echo ($playlist && $playlist['mood'] == 'galau') ? 'selected' : ''; ?>>Galau</option>
                                    <option value="bahagia" <?php echo ($playlist && $playlist['mood'] == 'bahagia') ? 'selected' : ''; ?>>Bahagia</option>
                                    <option value="romantis" <?php echo ($playlist && $playlist['mood'] == 'romantis') ? 'selected' : ''; ?>>Romantis</option>
                                    <option value="semangat" <?php echo ($playlist && $playlist['mood'] == 'semangat') ? 'selected' : ''; ?>>Semangat</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

