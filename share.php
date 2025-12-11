<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    redirect('index.php');
}

$db = getDB();
$stmt = $db->prepare("SELECT p.*, GROUP_CONCAT(s.id) as song_ids FROM playlists p LEFT JOIN playlist_song ps ON p.id = ps.playlist_id LEFT JOIN songs s ON ps.song_id = s.id WHERE p.id = ? GROUP BY p.id");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$playlist = $result->fetch_assoc();

if (!$playlist) {
    redirect('index.php');
}

// Get songs
$songIds = $playlist['song_ids'] ? explode(',', $playlist['song_ids']) : [];
$songs = [];
if (!empty($songIds)) {
    $placeholders = str_repeat('?,', count($songIds) - 1) . '?';
    $stmt = $db->prepare("SELECT * FROM songs WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($songIds)), ...$songIds);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($playlist['name']); ?></title>
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
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($playlist['name']); ?></h1>
                                <p class="text-muted"><?php echo htmlspecialchars($playlist['description']); ?></p>
                                <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($playlist['mood']); ?></span>
                            </div>
                            <button class="btn btn-primary" onclick="sharePlaylist()">
                                <i class="fas fa-share-alt me-2"></i>Bagikan
                            </button>
                        </div>

                        <div class="list-group">
                            <?php if (empty($songs)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Belum ada lagu dalam playlist ini.
                                </div>
                            <?php else: ?>
                                <?php foreach ($songs as $song): ?>
                                    <?php 
                                    $thumbnail = !empty($song['thumbnail']) ? $song['thumbnail'] : 'https://via.placeholder.com/80x80?text=🎵';
                                    ?>
                                    <div class="list-group-item">
                                        <div class="song-item">
                                            <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="song-thumbnail" onerror="this.src='https://via.placeholder.com/80x80?text=🎵'">
                                            <div class="song-info">
                                                <h5 class="mb-1"><?php echo htmlspecialchars($song['title']); ?></h5>
                                                <p class="mb-1 text-muted"><?php echo htmlspecialchars($song['artist']); ?></p>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($song['mood']); ?></span>
                                            </div>
                                            <div class="ms-3">
                                                <?php if ($song['spotify_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($song['spotify_link']); ?>" target="_blank" class="btn btn-sm btn-success me-2">
                                                        <i class="fab fa-spotify"></i> Spotify
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($song['youtube_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($song['youtube_link']); ?>" target="_blank" class="btn btn-sm btn-danger">
                                                        <i class="fab fa-youtube"></i> YouTube
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
    function sharePlaylist() {
        const url = window.location.href;
        
        // Cek apakah Web Share API didukung
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($playlist['name']); ?>',
                text: 'Lihat playlist rekomendasi saya!',
                url: url
            }).catch(function(error) {
                // Jika user cancel atau error, fallback ke clipboard
                if (error.name !== 'AbortError') {
                    copyToClipboard(url);
                }
            });
        } else {
            // Fallback ke clipboard
            copyToClipboard(url);
        }
    }
    
    // Fungsi copy to clipboard dengan toast notification
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                showShareSuccess();
            }).catch(function() {
                fallbackCopyToClipboard(text);
            });
        } else {
            fallbackCopyToClipboard(text);
        }
    }
    
    function fallbackCopyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        textarea.style.top = '-999999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showShareSuccess();
            } else {
                showShareError();
            }
        } catch (err) {
            showShareError();
        } finally {
            document.body.removeChild(textarea);
        }
    }
    
    function showShareSuccess() {
        // Hapus toast sebelumnya jika ada
        const existingToast = document.querySelector('.share-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = 'share-toast';
        toast.innerHTML = '<i class="fas fa-check-circle me-2"></i>Link playlist berhasil disalin!';
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(function() {
            toast.classList.add('show');
        }, 10);
        
        // Animate out setelah 3 detik
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    function showShareError() {
        const existingToast = document.querySelector('.share-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = 'share-toast share-toast-error';
        toast.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Gagal menyalin link. Silakan salin manual: ' + window.location.href;
        document.body.appendChild(toast);
        
        setTimeout(function() {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }
    </script>
</body>
</html>

