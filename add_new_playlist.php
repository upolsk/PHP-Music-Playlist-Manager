<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include_once('storage.php');
$stor = new Storage(new JsonIO('users.json'));
$currentUser = $stor -> findById($_SESSION['user_id']);

$tracks = json_decode(file_get_contents('tracks.json'), true);

$errors = [];

$playlistName = $_POST['playlist_name'] ?? '';
$createdBy = $currentUser['username'];
$selectedTracks = $_POST['tracks'] ?? [];
$imageUrl = $_POST['image_url'] ?? 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bXVzaWN8ZW58MHx8MHx8fDA%3D&w=1000&q=80';
$isPublic = isset($_POST['public']);

if ($_POST) {
    if (empty($playlistName)) {
        $errors['playlist_name'] = 'Please enter a playlist name.';
    }
    if (empty($selectedTracks)) {
        $errors['tracks'] = 'Please select at least one track.';
    }
    if (empty($imageUrl)) {
        $errors['image_url'] = 'Please enter the image URL.';
    } elseif (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $errors['image_url'] = 'Please enter a valid image URL.';
    }

    if (empty($errors)) {
        $newPlaylist = [
            'image' => $imageUrl,
            'name' => $playlistName,
            'public' => $isPublic,
            'created_by' => $createdBy,
            'tracks' => $selectedTracks,
        ];
        $existingPlaylists = json_decode(file_get_contents('playlists.json'), true);
        $playlistId = 'playlist' . (count($existingPlaylists) + 1);
        $existingPlaylists[$playlistId] = $newPlaylist;

        file_put_contents('playlists.json', json_encode($existingPlaylists, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        header('Location: main.php');
        exit();
    }
}

$errors = array_map(fn ($e) => "<span style='color:red'>$e</span>", $errors);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Playlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2 class = "delete-track-h2">Create Playlist</h2>
    <div class = "delete-track-div">
    <?php if (!empty($errors)) : ?>
        <div class="error-messages">
            <?php foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" nonvalidate>
        <div class = "add_new_playlist_name">
        <label for="playlist_name">Playlist Name:</label>
        <input type="text" id="playlist_name" name="playlist_name" value="<?php echo $playlistName; ?>">
        </div>

        <div class = "add_new_image_url">
        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" value="<?php echo $imageUrl; ?>">
        </div>

        <div class = "add_new_public">
        <label for="public">Public:</label>
        <input type="checkbox" id="public" name="public" <?php if ($isPublic) echo 'checked'; ?>>
        </div>

        <div class = "add_new_tracks">
        <label for="tracks">Select Tracks:</label>
        <?php foreach ($tracks as $trackId => $track) : ?>
            <div>
                <input type="checkbox" id="track_<?php echo $trackId; ?>" name="tracks[]" value="<?php echo $trackId; ?>" <?php if (in_array($trackId, $selectedTracks)) echo 'checked'; ?>>
                <label for="track_<?php echo $trackId; ?>"><?php echo $track['title']; ?></label>
            </div>
        <?php endforeach; ?>
        </div>

        <button type="submit">Create Playlist</button>

    </form>
        </div>
</body>
</html> 


<?php
