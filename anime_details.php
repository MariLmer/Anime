<?php
// Database connection details
$host = "anime-db-server.database.windows.net";
$username = "databaseadmin";
$password = "dbuser@12345";
$dbname = "anime";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the anime ID from the query string
$anime_id = isset($_GET['anime_id']) ? intval($_GET['anime_id']) : 0;

// Fetch anime details
$anime_sql = "SELECT name FROM anime WHERE anime_id = ?";
$anime_stmt = $conn->prepare($anime_sql);
$anime_stmt->bind_param("i", $anime_id);
$anime_stmt->execute();
$anime_result = $anime_stmt->get_result();
$anime = $anime_result->fetch_assoc();

// Fetch episodes for the selected anime
$episodes_sql = "SELECT episode_id, episode_title, episode_url FROM episodes WHERE anime_id = ?";
$episodes_stmt = $conn->prepare($episodes_sql);
$episodes_stmt->bind_param("i", $anime_id);
$episodes_stmt->execute();
$episodes_result = $episodes_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php if ($anime): ?>
        <h1 class="mb-4">Episodes for <?= htmlspecialchars($anime['name']) ?></h1>
        <ul class="list-group">
            <?php while ($row = $episodes_result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <a href="<?= htmlspecialchars($row['episode_url']) ?>" target="_blank">
                        <?= htmlspecialchars($row['episode_title']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-danger">Anime not found!</p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary mt-4">Back to Anime List</a>
</div>
</body>
</html>
<?php
$anime_stmt->close();
$episodes_stmt->close();
$conn->close();
?>
