<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming request details
error_log("Received request parameters: " . print_r($_POST, true));

// Database configuration
$host = "anime-db-server.database.windows.net";
$username = "databaseadmin";
$password = "dbuser@12345";
$dbname = "anime_world";

try {
    // Create connection using PDO for SQL Server
    $conn = new PDO("sqlsrv:Server=$host;Database=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Database connection successful");
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Retrieve anime_id from POST
$anime_id = isset($_POST['anime_id']) ? intval($_POST['anime_id']) : 0;
error_log("Processing anime_id: " . $anime_id);

try {
    // Fetch anime details
    $anime_sql = "SELECT name FROM anime WHERE anime_id = ?";
    $anime_stmt = $conn->prepare($anime_sql);
    $anime_stmt->execute([$anime_id]);
    $anime = $anime_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch episodes for the selected anime
    $episodes_sql = "SELECT episode_id, episode_title, episode_url FROM episodes WHERE anime_id = ?";
    $episodes_stmt = $conn->prepare($episodes_sql);
    $episodes_stmt->execute([$anime_id]);
    $episodes = $episodes_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Query error: " . $e->getMessage());
    die("Query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: white;
        }
        .list-group-item {
            background-color: #333;
            border-color: #444;
            color: white;
        }
        .list-group-item a {
            color: #f00;
            text-decoration: none;
        }
        .list-group-item a:hover {
            color: #ff6666;
        }
        .btn-primary {
            background-color: #f00;
            border-color: #f00;
        }
        .btn-primary:hover {
            background-color: #cc0000;
            border-color: #cc0000;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <?php if ($anime): ?>
        <h1 class="mb-4">Episodes for <?= htmlspecialchars($anime['name']) ?></h1>
        <?php if (count($episodes) > 0): ?>
            <ul class="list-group">
                <?php foreach ($episodes as $episode): ?>
                    <li class="list-group-item">
                        <a href="<?= htmlspecialchars($episode['episode_url']) ?>" target="_blank">
                            <?= htmlspecialchars($episode['episode_title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-warning">No episodes found for this anime.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-danger">Anime not found!</p>
    <?php endif; ?>
    <a href="index.html" class="btn btn-primary mt-4">Back to Anime List</a>
</div>

<script>
// Add error logging for client-side issues
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.error('Error: ' + msg + '\nURL: ' + url + '\nLine: ' + lineNo + '\nColumn: ' + columnNo + '\nError object: ' + JSON.stringify(error));
    return false;
};
</script>
</body>
</html>
<?php
// Close all statements and connection
$anime_stmt = null;
$episodes_stmt = null;
$conn = null;
?>
