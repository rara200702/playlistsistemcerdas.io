<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mood_playlist');

// Konfigurasi Admin Login (bisa diubah sesuai kebutuhan)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Ganti dengan password yang lebih kuat!

// Koneksi Database
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            // Don't die, just show error message
            echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px; margin:20px;'>";
            echo "<h3>Database Connection Error</h3>";
            echo "<p><strong>Error:</strong> " . $conn->connect_error . "</p>";
            echo "<p><strong>Solution:</strong></p>";
            echo "<ol>";
            echo "<li>Make sure MySQL is running in XAMPP</li>";
            echo "<li>Import database from: <code>database/mood_playlist.sql</code></li>";
            echo "<li>Check database settings in <code>config.php</code></li>";
            echo "</ol>";
            echo "</div>";
            return null;
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is admin (helper function)
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
