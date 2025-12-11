<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to load config, but don't fail if database not connected
try {
    require_once 'config.php';
} catch (Exception $e) {
    // Continue even if config fails
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood-Based Playlist Generator</title>
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
            <a href="login.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-cog me-1"></i>Admin
            </a>
        </div>
    </nav>

    <!-- Decorative Elements -->
    <div class="decorative-left">
        <i class="fas fa-music"></i>
        <i class="fas fa-headphones"></i>
        <i class="fas fa-compact-disc"></i>
    </div>
    <div class="decorative-right">
        <i class="fas fa-music"></i>
        <i class="fas fa-headphones"></i>
        <i class="fas fa-compact-disc"></i>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-heart text-danger"></i> Mood-Based Playlist Generator
                    </h1>
                    <p class="lead text-muted">Temukan playlist yang sesuai dengan mood Anda hari ini!</p>
                </div>

                <!-- Mood Questionnaire -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">Bagaimana Perasaan Anda Hari Ini?</h2>
                        
                        <form id="moodForm">
                            <!-- Mood Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih Mood Anda:</label>
                                <div class="row g-3">
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_energi" value="energi" required>
                                        <label class="btn btn-outline-primary w-100 mood-btn" for="mood_energi">
                                            <i class="fas fa-bolt fa-2x d-block mb-2"></i>
                                            <span>Energi</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_tenang" value="tenang" required>
                                        <label class="btn btn-outline-info w-100 mood-btn" for="mood_tenang">
                                            <i class="fas fa-cloud fa-2x d-block mb-2"></i>
                                            <span>Tenang</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_galau" value="galau" required>
                                        <label class="btn btn-outline-secondary w-100 mood-btn" for="mood_galau">
                                            <i class="fas fa-moon fa-2x d-block mb-2"></i>
                                            <span>Galau</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_bahagia" value="bahagia" required>
                                        <label class="btn btn-outline-warning w-100 mood-btn" for="mood_bahagia">
                                            <i class="fas fa-smile fa-2x d-block mb-2"></i>
                                            <span>Bahagia</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_romantis" value="romantis" required>
                                        <label class="btn btn-outline-danger w-100 mood-btn" for="mood_romantis">
                                            <i class="fas fa-heart fa-2x d-block mb-2"></i>
                                            <span>Romantis</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="radio" class="btn-check" name="mood" id="mood_semangat" value="semangat" required>
                                        <label class="btn btn-outline-success w-100 mood-btn" for="mood_semangat">
                                            <i class="fas fa-fire fa-2x d-block mb-2"></i>
                                            <span>Semangat</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Energy Level -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tingkat Energi (1-5):</label>
                                <div class="d-flex align-items-center">
                                    <span class="me-3">Rendah</span>
                                    <input type="range" class="form-range flex-grow-1" name="energy_level" id="energy_level" min="1" max="5" value="3">
                                    <span class="ms-3">Tinggi</span>
                                </div>
                                <div class="text-center mt-2">
                                    <span id="energy_value" class="badge bg-primary">3</span>
                                </div>
                            </div>

                            <!-- Platform Preference -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Platform Pilihan:</label>
                                <select class="form-select" name="preference" id="preference">
                                    <option value="mixed">Campuran (Spotify & YouTube)</option>
                                    <option value="spotify">Spotify</option>
                                    <option value="youtube">YouTube</option>
                                </select>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                    <i class="fas fa-magic me-2"></i>Generate Playlist
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="results" class="mt-5" style="display: none;">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <h2 class="card-title text-center mb-4">
                                <i class="fas fa-music text-primary"></i> Playlist Rekomendasi Anda
                            </h2>
                            <div id="playlistContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-4 mt-5">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Generator Playlist sesuai mood.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>

