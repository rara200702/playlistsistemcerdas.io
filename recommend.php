<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

$mood = isset($_POST['mood']) ? sanitize($_POST['mood']) : '';
$energyLevel = isset($_POST['energy_level']) ? intval($_POST['energy_level']) : 3;
$preference = isset($_POST['preference']) ? sanitize($_POST['preference']) : 'mixed';

if (empty($mood)) {
    echo json_encode(['success' => false, 'message' => 'Mood harus dipilih']);
    exit;
}

$db = getDB();
if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Rule-Based Recommendation System
$songs = getRecommendations($db, $mood, $energyLevel, $preference);

// Create or find playlist
$playlistName = ucfirst($mood) . ' Playlist';
$playlistDesc = 'Auto-generated playlist based on mood: ' . $mood;

$stmt = $db->prepare("SELECT id FROM playlists WHERE mood = ? LIMIT 1");
$stmt->bind_param("s", $mood);
$stmt->execute();
$result = $stmt->get_result();
$playlist = $result->fetch_assoc();

if (!$playlist) {
    $stmt = $db->prepare("INSERT INTO playlists (name, description, mood) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $playlistName, $playlistDesc, $mood);
    $stmt->execute();
    $playlistId = $db->insert_id;
} else {
    $playlistId = $playlist['id'];
}

// Attach songs to playlist
if (!empty($songs)) {
    $songIds = array_column($songs, 'id');
    $placeholders = str_repeat('?,', count($songIds) - 1) . '?';
    $stmt = $db->prepare("INSERT IGNORE INTO playlist_song (playlist_id, song_id) VALUES (?, ?)");
    foreach ($songIds as $songId) {
        $stmt->bind_param("ii", $playlistId, $songId);
        $stmt->execute();
    }
}

$shareUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/share.php?id=" . $playlistId;

echo json_encode([
    'success' => true,
    'playlist_id' => $playlistId,
    'playlist_name' => $playlistName,
    'songs' => array_map(function($song) {
        return [
            'id' => $song['id'],
            'title' => $song['title'],
            'artist' => $song['artist'],
            'spotify_link' => $song['spotify_link'],
            'youtube_link' => $song['youtube_link'],
            'thumbnail' => $song['thumbnail'] ?? null,
            'mood' => $song['mood'],
        ];
    }, $songs),
    'share_url' => $shareUrl
]);

function getRecommendations($db, $mood, $energyLevel, $preference) {
    // Base query - get songs with matching mood
    $stmt = $db->prepare("SELECT * FROM songs WHERE mood = ? LIMIT 20");
    $stmt->bind_param("s", $mood);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = [];
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
    
    // Energy level filtering - filter results
    if ($energyLevel <= 2) {
        // Low energy - prefer calm songs
        $songs = array_filter($songs, function($song) {
            return in_array($song['mood'], ['tenang', 'galau', 'romantis']);
        });
    } elseif ($energyLevel >= 4) {
        // High energy - prefer energetic songs
        $songs = array_filter($songs, function($song) {
            return in_array($song['mood'], ['energi', 'semangat', 'bahagia']);
        });
    }
    $songs = array_values($songs); // Re-index array
    
    // If not enough songs, get similar moods
    if (count($songs) < 10) {
        $similarMoods = getSimilarMoods($mood);
        $songIds = array_column($songs, 'id');
        
        if (!empty($similarMoods)) {
            $placeholders = str_repeat('?,', count($similarMoods) - 1) . '?';
            $query = "SELECT * FROM songs WHERE mood IN ($placeholders)";
            $params = $similarMoods;
            $types = str_repeat('s', count($similarMoods));
            
            if (!empty($songIds)) {
                $idsPlaceholder = str_repeat('?,', count($songIds) - 1) . '?';
                $query .= " AND id NOT IN ($idsPlaceholder)";
                $params = array_merge($params, $songIds);
                $types .= str_repeat('i', count($songIds));
            }
            
            $query .= " LIMIT " . (10 - count($songs));
            
            $stmt = $db->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $songs[] = $row;
            }
        }
    }
    
    // Shuffle and limit
    shuffle($songs);
    return array_slice($songs, 0, 15);
}

function getSimilarMoods($mood) {
    $moodMap = [
        'energi' => ['semangat', 'bahagia'],
        'tenang' => ['romantis'],
        'galau' => ['romantis', 'tenang'],
        'bahagia' => ['energi', 'semangat'],
        'romantis' => ['tenang', 'galau'],
        'semangat' => ['energi', 'bahagia'],
    ];
    
    return isset($moodMap[$mood]) ? $moodMap[$mood] : ['energi', 'tenang', 'bahagia'];
}

