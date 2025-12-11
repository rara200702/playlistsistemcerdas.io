<?php
// Simple check file to diagnose issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>System Check</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Status: " . (version_compare(phpversion(), '7.4.0', '>=') ? "✅ OK" : "❌ Need PHP 7.4+") . "<br><br>";

// Check MySQL extension
echo "<h2>MySQL Extension</h2>";
if (extension_loaded('mysqli')) {
    echo "mysqli extension: ✅ Loaded<br>";
} else {
    echo "mysqli extension: ❌ Not loaded<br>";
}
echo "<br>";

// Check config file
echo "<h2>Config File</h2>";
if (file_exists('config.php')) {
    echo "config.php: ✅ Exists<br>";
    require_once 'config.php';
    
    // Test database connection
    echo "<h2>Database Connection</h2>";
    $db = @getDB();
    if ($db) {
        echo "Database connection: ✅ Connected<br>";
        echo "Database name: " . DB_NAME . "<br>";
        
        // Check if tables exist
        $tables = ['playlists', 'songs', 'playlist_song'];
        echo "<h3>Database Tables</h3>";
        foreach ($tables as $table) {
            $result = $db->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "$table: ✅ Exists<br>";
            } else {
                echo "$table: ❌ Not found (need to import database)<br>";
            }
        }
    } else {
        echo "Database connection: ❌ Failed<br>";
        echo "Please check:<br>";
        echo "1. MySQL is running in XAMPP<br>";
        echo "2. Database 'mood_playlist' exists<br>";
        echo "3. Import database/mood_playlist.sql<br>";
    }
} else {
    echo "config.php: ❌ Not found<br>";
}
echo "<br>";

// Check required files
echo "<h2>Required Files</h2>";
$files = ['index.php', 'admin.php', 'recommend.php', 'share.php', 'config.php'];
foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? "✅" : "❌") . "<br>";
}
echo "<br>";

// Check assets
echo "<h2>Assets</h2>";
$assets = ['assets/css/style.css', 'assets/js/app.js'];
foreach ($assets as $asset) {
    echo "$asset: " . (file_exists($asset) ? "✅" : "❌") . "<br>";
}
?>

