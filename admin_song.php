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
    $title = sanitize($_POST['title']);
    $artist = sanitize($_POST['artist']);
    $spotify_link = sanitize($_POST['spotify_link']);
    $youtube_link = sanitize($_POST['youtube_link']);
    $thumbnail = sanitize($_POST['thumbnail']);
    $mood = sanitize($_POST['mood']);
    $playlist_id = !empty($_POST['playlist_id']) ? intval($_POST['playlist_id']) : null;
    
    // Auto-generate thumbnail from YouTube if not provided
    if (empty($thumbnail) && !empty($youtube_link)) {
        // Extract video ID from YouTube URL
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_link, $matches);
        if (!empty($matches[1])) {
            $thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
        }
    }
    
    if ($action == 'create') {
        $stmt = $db->prepare("INSERT INTO songs (title, artist, spotify_link, youtube_link, thumbnail, mood, playlist_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $title, $artist, $spotify_link, $youtube_link, $thumbnail, $mood, $playlist_id);
        $stmt->execute();
        redirect("admin.php?success=Lagu berhasil ditambahkan");
    } elseif ($action == 'edit' && $id > 0) {
        $stmt = $db->prepare("UPDATE songs SET title = ?, artist = ?, spotify_link = ?, youtube_link = ?, thumbnail = ?, mood = ?, playlist_id = ? WHERE id = ?");
        $stmt->bind_param("ssssssii", $title, $artist, $spotify_link, $youtube_link, $thumbnail, $mood, $playlist_id, $id);
        $stmt->execute();
        redirect("admin.php?success=Lagu berhasil diupdate");
    }
}

// Handle delete
if ($action == 'delete' && $id > 0) {
    $stmt = $db->prepare("DELETE FROM songs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    redirect("admin.php?success=Lagu berhasil dihapus");
}

// Get song data for edit
$song = null;
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM songs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
}

// Get playlists for dropdown
$playlists = $db->query("SELECT * FROM playlists ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action == 'edit' ? 'Edit' : 'Tambah'; ?> Lagu</title>
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
                        <h4><i class="fas fa-<?php echo $action == 'edit' ? 'edit' : 'plus'; ?> me-2"></i><?php echo $action == 'edit' ? 'Edit' : 'Tambah'; ?> Lagu</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Lagu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $song ? htmlspecialchars($song['title']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="artist" class="form-label">Artis <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="artist" name="artist" value="<?php echo $song ? htmlspecialchars($song['artist']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="mood" class="form-label">Mood <span class="text-danger">*</span></label>
                                <select class="form-select" id="mood" name="mood" required>
                                    <option value="">Pilih Mood</option>
                                    <option value="energi" <?php echo ($song && $song['mood'] == 'energi') ? 'selected' : ''; ?>>Energi</option>
                                    <option value="tenang" <?php echo ($song && $song['mood'] == 'tenang') ? 'selected' : ''; ?>>Tenang</option>
                                    <option value="galau" <?php echo ($song && $song['mood'] == 'galau') ? 'selected' : ''; ?>>Galau</option>
                                    <option value="bahagia" <?php echo ($song && $song['mood'] == 'bahagia') ? 'selected' : ''; ?>>Bahagia</option>
                                    <option value="romantis" <?php echo ($song && $song['mood'] == 'romantis') ? 'selected' : ''; ?>>Romantis</option>
                                    <option value="semangat" <?php echo ($song && $song['mood'] == 'semangat') ? 'selected' : ''; ?>>Semangat</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="spotify_link" class="form-label">Link Spotify</label>
                                <input type="url" class="form-control" id="spotify_link" name="spotify_link" value="<?php echo $song ? htmlspecialchars($song['spotify_link']) : ''; ?>" placeholder="https://open.spotify.com/track/...">
                            </div>

                            <div class="mb-3">
                                <label for="youtube_link" class="form-label">Link YouTube</label>
                                <input type="url" class="form-control" id="youtube_link" name="youtube_link" value="<?php echo $song ? htmlspecialchars($song['youtube_link']) : ''; ?>" placeholder="https://www.youtube.com/watch?v=...">
                                <small class="text-muted">Thumbnail akan otomatis di-generate dari YouTube link</small>
                            </div>

                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail (URL Gambar)</label>
                                <input type="url" class="form-control" id="thumbnail" name="thumbnail" value="<?php echo $song ? htmlspecialchars($song['thumbnail']) : ''; ?>" placeholder="https://img.youtube.com/vi/VIDEO_ID/mqdefault.jpg">
                                <small class="text-muted">Kosongkan untuk auto-generate dari YouTube (format: https://img.youtube.com/vi/VIDEO_ID/mqdefault.jpg)</small>
                            </div>

                            <div class="mb-3">
                                <label for="playlist_id" class="form-label">Playlist (Opsional)</label>
                                <select class="form-select" id="playlist_id" name="playlist_id">
                                    <option value="">Tidak ada</option>
                                    <?php foreach ($playlists as $playlist): ?>
                                        <option value="<?php echo $playlist['id']; ?>" <?php echo ($song && $song['playlist_id'] == $playlist['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($playlist['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-success">
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

