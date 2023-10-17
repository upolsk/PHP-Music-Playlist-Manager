<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include_once('storage.php');
$stor = new Storage(new JsonIO('users.json'));
$currentUser = $stor -> findById($_SESSION['user_id']);

$playlistId = $_POST['playlist_id'];
$trackId = $_POST['track_id'];
$targerPlaylist = $_POST['target_playlist'] ?? "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Playlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        $playlists = json_decode(file_get_contents('playlists.json'), true);

        $myPlaylists = [];
        foreach ($playlists as $playlistId => $playlist) {
            if ($playlist['created_by'] == $currentUser['username']) {
                $myPlaylists[$playlistId] = $playlist;
            }
        }

        if ( count($myPlaylists) == 0) {
            header('Location: add_new_playlist.php');
            exit;
        }
        else if ( count($myPlaylists) == 1 ) {
            $playlistId = array_key_first($myPlaylists);
            $playlists[$playlistId]['tracks'][] = $trackId;
            file_put_contents('playlists.json', json_encode($playlists, JSON_PRETTY_PRINT));
            header("Location: details.php?playlist=$playlistId");
            exit;
        }
        else {
            if ($targerPlaylist != "") {
                $playlists[$targerPlaylist]['tracks'][] = $trackId;
                file_put_contents('playlists.json', json_encode($playlists, JSON_PRETTY_PRINT));
                header("Location: details.php?playlist=$targerPlaylist");
                exit;
            }
            else {
                echo '<h1 class = "add-to-playlist-h1">Choose a playlist</h1>';
                echo '<ul class="playlist-list">';
                $i = 1;
                foreach ($myPlaylists as $playlistId => $playlist) {
                    echo '<form class="add-to-playlist-form" method="post" action="add_to_playlist.php">';
                    echo '<li class="playlist-item">';
                    echo '<span class="playlist-number">' . $i . ') </span>'; 
                    echo '<span class="playlist-name">' . $playlist['name'] . '</span>';
                    echo '<input type="hidden" name="playlist_id" value="' . $playlistId . '">';
                    echo '<input type="hidden" name="track_id" value="' . $trackId . '">';
                    echo '<input type="hidden" name="target_playlist" value="' . $playlistId . '">';
                    echo '<input class = "add-button" type="submit" value="Add">';
                    echo '</li>';
                    echo '</form>';
                    $i++;
                }

                echo '</ul>';

            }
            
        }
    ?>
</body>
</html>
