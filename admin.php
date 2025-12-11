<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$db = getDB();
if (!$db) {
    die("Database connection failed. Please check your configuration.");
}

// Get statistics
$playlistsCount = $db->query("SELECT COUNT(*) as count FROM playlists")->fetch_assoc()['count'];
$songsCount = $db->query("SELECT COUNT(*) as count FROM songs")->fetch_assoc()['count'];

// Get playlists
$playlists = $db->query("SELECT p.*, COUNT(ps.song_id) as songs_count 
                         FROM playlists p 
                         LEFT JOIN playlist_song ps ON p.id = ps.playlist_id 
                         GROUP BY p.id 
                         ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);

// Get songs
$songs = $db->query("SELECT s.*, p.name as playlist_name 
                     FROM songs s 
                     LEFT JOIN playlists p ON s.playlist_id = p.id 
                     ORDER BY s.id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mood Playlist Generator</title>
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
            <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-home me-1"></i>Home
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Logout (<?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>)
            </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo sanitize($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-list me-2"></i>Total Playlists</h5>
                        <h2 class="mb-0"><?php echo $playlistsCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-music me-2"></i>Total Songs</h5>
                        <h2 class="mb-0"><?php echo $songsCount; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Playlists</h5>
                        <a href="admin_playlist.php?action=create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Tambah
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (empty($playlists)): ?>
                                <p class="text-muted text-center py-3">Belum ada playlist</p>
                            <?php else: ?>
                                <?php foreach ($playlists as $playlist): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($playlist['name']); ?></h6>
                                                <p class="mb-1 text-muted"><small><?php echo htmlspecialchars($playlist['description']); ?></small></p>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($playlist['mood']); ?></span>
                                                <span class="badge bg-secondary"><?php echo $playlist['songs_count']; ?> songs</span>
                                            </div>
                                            <div>
                                                <a href="admin_playlist.php?action=edit&id=<?php echo $playlist['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin_playlist.php?action=delete&id=<?php echo $playlist['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-music me-2"></i>Songs</h5>
                        <a href="admin_song.php?action=create" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>Tambah
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (empty($songs)): ?>
                                <p class="text-muted text-center py-3">Belum ada lagu</p>
                            <?php else: ?>
                                <?php foreach ($songs as $song): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($song['title']); ?></h6>
                                                <p class="mb-1 text-muted"><small><?php echo htmlspecialchars($song['artist']); ?></small></p>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($song['mood']); ?></span>
                                            </div>
                                            <div>
                                                <a href="admin_song.php?action=edit&id=<?php echo $song['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin_song.php?action=delete&id=<?php echo $song['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

