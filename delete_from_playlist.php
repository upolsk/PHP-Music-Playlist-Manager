<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_POST) {
    $selectedPlaylistId = $_POST['playlist_id'];
    $trackId = $_POST['track_id'];

    $playlists = json_decode(file_get_contents('playlists.json'), true);

    if (isset($playlists[$selectedPlaylistId])) {
        $selectedPlaylist = $playlists[$selectedPlaylistId];
        $tracks = $selectedPlaylist['tracks'];

        if (in_array($trackId, $tracks)) {
            $index = array_search($trackId, $tracks);
            unset($playlists[$selectedPlaylistId]['tracks'][$index]);
            file_put_contents('playlists.json', json_encode($playlists, JSON_PRETTY_PRINT));
            header("Location: details.php?playlist=$selectedPlaylistId");
            exit;
        }
    }
}

header("Location: details.php?playlist=$selectedPlaylistId");
exit;
?>
