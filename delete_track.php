<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('storage.php');
$stor = new Storage(new JsonIO('tracks.json')); 
$currentUser = $stor->findById($_SESSION['user_id']);

if ($_POST) {
    $trackId = $_POST['track_id'] ?? '';

    $stor->delete($trackId);
    header('Location: main.php');
    exit;
}

$trackId = $_GET['track_id'] ?? '';
$tracks = $stor->findAll(); 
if (isset($tracks[$trackId])) {
    $existingTrack = $tracks[$trackId];
}
  
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>Delete Track</title>
    </head>
    <body>
        <div class = "delete-track-div">
        <h2 class = "delete-track-h2">Track Details</h2>
        <p><strong>Title:</strong> <?php echo $existingTrack['title']; ?></p>
        <p><strong>Artist:</strong> <?php echo $existingTrack['artist']; ?></p>
        <p><strong>Length:</strong> <?php echo $existingTrack['length']; ?></p>
        <p><strong>Year:</strong> <?php echo $existingTrack['year']; ?></p>
        <p><strong>Genres:</strong> <?php echo implode(", ", $existingTrack['genres']); ?></p>
        <p><strong>Source:</strong> <?php echo $existingTrack['source']; ?></p>
        <p><strong>Image:</strong> <?php echo $existingTrack['img']; ?></p>

        <h2 class = "delete-track-h2">Confirmation</h2>
        <p>Are you sure you want to delete this track?</p>
        <form method="POST" action="">
            <input type="hidden" name="track_id" value="<?php echo $trackId; ?>">
            <button type="submit">Delete</button>
        </form>
</div>
    </body>
    </html>
    
