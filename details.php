<?php
session_start(); 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
<section class = "section-for-details">
<?php
$playlists = json_decode(file_get_contents('playlists.json'), true);
$selectedPlaylistId = $_GET['playlist'];

if (isset($playlists[$selectedPlaylistId])) {
    $selectedPlaylist = $playlists[$selectedPlaylistId];
    $tracks = json_decode(file_get_contents('tracks.json'), true);

    $totalPlayingTime = 0;

    foreach ($selectedPlaylist['tracks'] as $trackId) {
        if (isset($tracks[$trackId])) {
            $track = $tracks[$trackId];
            $trackDurationInSeconds = $track['length'];    
            $totalPlayingTime += $trackDurationInSeconds;
        }
    }
    
    $hours = floor($totalPlayingTime / 3600);
    $minutes = floor(($totalPlayingTime % 3600) / 60);
    $seconds = $totalPlayingTime % 60;
    
    $formattedTotalPlayingTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
    echo '<h1>Playlist Details: ' . $selectedPlaylist['name'] . ' (' . $formattedTotalPlayingTime . ')</h1>';
    


    echo '<div class="logout-in-details">
    <a href="main.php" class="logout-button">Back</a>
</div>';
    $tracks = json_decode(file_get_contents('tracks.json'), true);
    echo '<table>';
    echo '<tr>
            <th>#</th>
            <th>TITLE</th>
            <th>ARTIST</th>
            <th>LENGTH</th>
            <th>YEAR</th>
            <th>GENRES</th>
            <th>AUDIO</th>
          </tr>';
    
    $rowNumber = 1; 
    
    foreach ($selectedPlaylist['tracks'] as $trackId) {
        if (isset($tracks[$trackId])) {
            $track = $tracks[$trackId];
            
            echo '<tr>';
            echo '<td>' . $rowNumber . '</td>'; 
            echo '<td><img src="' . ($track['img'] ?? '') . '"></td>'; 
            echo '<td>' . $track['artist'] . '</td>';
            echo '<td>' . $track['length'] . '</td>';
            echo '<td>' . $track['year'] . '</td>';
            echo '<td>' . implode(", ", $track['genres']) . '</td>';
            echo '<td>
                    <audio controls>
                        <source src="' . ($track['source'] ?? '') . '" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <div class="playlist-buttons-container">
                    <form method="post" action="add_to_playlist.php">
                    <input type="hidden" name="playlist_id" value="'.$selectedPlaylistId.'">
                    <input type="hidden" name="track_id" value="'.$trackId.'">
                    <button class="add-to-playlist-button" type="submit" name="add_to_playlist">Add to My Playlist</button>
                    </form>

                <form method="post" action="delete_from_playlist.php">
                    <input type="hidden" name="playlist_id" value="'. $selectedPlaylistId.'">
                    <input type="hidden" name="track_id" value="' . ($trackId ). '">
                    <button class="delete-from-playlist-button" type="submit" name="delete_from_playlist">Delete From Playlist</button>
                </form>
                </div>
                  </td>';

            $rowNumber++; 
        }
    }
    
    echo '</table>';   
} else {
    echo '<h1>Invalid playlist ID</h1>';
}
?>
</section>

<div class="footer">
    <h2>Questions? Contact me: +36 20 586 7447</h2>
</div>

</body>

</html>