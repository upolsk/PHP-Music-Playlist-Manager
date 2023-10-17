<?php
  session_start();
  if(isset($_SESSION['user_id']))
  {
    include_once('storage.php');
    $stor = new Storage(new JsonIO('users.json'));
    $currentUser = $stor -> findById($_SESSION['user_id']);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>
<div class="header">
    <nav>
        <img src="src/logo.png" class="logo">
        <?php if (isset($_SESSION['user_id']) && $currentUser['isAdmin']):?>
            <form method="get" action="add_track.php">
                <button class="add-track-button" type="submit" name="add_track">Add track+</button>
            </form>
        <?php endif;?>
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div>
            <button onclick="window.location.href = 'login.php'" type="submit">Login</button>
            <button onclick="window.location.href = 'register.php'" type="submit">Register</button>
        </div>
        <?php else: ?>
            <?php if ($currentUser['isAdmin']):?>
                <button>admin</button>
            <?php endif;?>
            <button onclick="window.location.href = 'logout.php'" type="submit">
                Logout (<?php echo $_SESSION['user_id']; ?>)
            </button>
        <?php endif; ?>
    </nav>
    <div class="header-content">
        <h1>Welcome to Music Vibes!</h1>
        <h1>Discover the perfect soundtrack for every moment!</h1>
        <h3>Start listening now and let the music captivate your senses.</h3>
        <p>So, what are you waiting for? Start searching now and let the melodies take you away!</p>
        <form class="search-form" method="GET">
            <input type="text" name="title" placeholder="Enter track title: ">
            <input type="text" name="artist" placeholder="Enter track artist: ">
            <input type="number" name="length" placeholder="Track length: ">
            <input type="number" name="year" placeholder="Track year: ">
            <input type="text" name="genres" placeholder="Enter track genres:">
            <button type="submit">Search</button>
        </form>
    </div>
</div>

<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

function filterTracks($tracks, $filters) {
    if (!is_array($tracks)) {
        return [];
    }

    $filteredTracks = array_filter($tracks, function ($track) use ($filters) {
        if (!is_array($track)) {
            return false;
        }

        if (!empty($filters['title']) && stripos($track['title'], $filters['title']) === false) {;
            return false;
        }
        if (!empty($filters['artist']) && stripos($track['artist'], $filters['artist']) === false) {
            return false;
        }
        if (!empty($filters['length']) && $track['length'] != $filters['length']) {
            return false;
        }
        if (!empty($filters['year']) && $track['year'] != $filters['year']) {
            return false;
        }
        if (!empty($filters['genres'])) {
            $trackGenres = $track['genres'] ?? [];
            $filterGenres = $filters['genres'];
            $filterGenres = array_map('trim', $filterGenres);
            foreach ($filterGenres as $filterGenre) {
                $matched = false;
                foreach ($trackGenres as $trackGenre) {
                    if (stripos($trackGenre, $filterGenre) !== false) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            }
            
        }
        return true;
    });
    
    return array_values($filteredTracks);
}


$tracks = json_decode(file_get_contents('tracks.json'), true);
$filters = array(
    'title' => $_GET['title'] ?? '',
    'artist' => $_GET['artist'] ?? '',
    'length' => $_GET['length'] ?? '',
    'year' => $_GET['year'] ?? '',
    'genres' => isset($_GET['genres']) ? explode(',', $_GET['genres']) : array(),
);
$filters['genres'] = isset($_GET['genres']) && $_GET['genres'] !== '' ? array_map('trim', explode(',', $_GET['genres'])) : [];

$filteredPlaylists = $tracks;
if (!empty($filters)) {
    $filteredPlaylists = filterTracks($tracks, $filters);
}

?>

<div class="filtered-tracks">
    <?php if (!empty($_GET) && !empty($filteredPlaylists)): ?>
        <h1 class="section-heading">Filtered Tracks</h1>
        <ul class="track-list">
            <?php foreach ($filteredPlaylists as $trackId => $track): ?>
                <li class="track-item">
                    <div class="track-details">
                        <div class="track-title"><?php echo $track['title']; ?></div>
                    </div>
                    <div class="track-info">
                       <div class="track-artist">
                            <span class="label">Artist:</span><?php echo $track['artist']; ?>
                       </div>
                        <div class="track-length">
                            <span class="label">Length:</span> <?php echo $track['length']; ?>
                        </div>
                        <div class="track-year">
                            <span class="label">Year:</span> <?php echo $track['year']; ?>
                        </div>
                        <div class="track-genres">
                            <span class="label">Genres:</span> <?php echo implode(', ', $track['genres']); ?>
                        </div>
                    </div>
                    <div class="add-delete-buttons">
                        <?php if (isset($currentUser) && $currentUser['isAdmin']): ?>
                            <form method="get" action="delete_track.php">
                                <input type="hidden" name="track_id" value="<?php echo $track['track_id'] ?>">
                                <button class="delete-track-button" type="submit" name="delete_track">Delete</button>
                            </form>
                            <form method="get" action="edit_track.php">
                                <input type="hidden" name="track_id" value="<?php echo $track['track_id'] ?>">
                                <button class="edit-track-button" type="submit" name="edit_track">Edit</button>
                            </form>
                        <?php endif; ?>
                        <?php if (isset($currentUser)): ?>
                            <form method="post" action="add_to_playlist.php">
                                <input type="hidden" name="playlist_id" value="<?php echo $selectedPlaylistId ?>">
                                <input type="hidden" name="track_id" value="<?php echo $track['track_id'] ?>">
                                <button class="delete-track-button" type="submit" name="add_to_playlist">Add to My Playlist</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>




<div class="playlists">
    <h1 id="my-heading">AVAILABLE ONLINE PLAYLISTS</h1>
    <div class="playlist-container">
        <?php
        $playlists = json_decode(file_get_contents('playlists.json'), true);
        if (isset($_SESSION['user_id'])) {
            echo '<img class="add-playlist" src="https://cdn2.iconfinder.com/data/icons/picol-vector/32/music_add-512.png">';
            echo '<p class="create-playlist"><a href="add_new_playlist.php">Create your own playlist</a></p>';
            foreach ($playlists as $playlistId => $playlist) {
                if ($playlist['public']) {
                    $trackCount = count($playlist['tracks']);
                    echo '<div class="playlist">';
                    echo '<img src="' . $playlist['image'] . '">';
                    echo '<p>' . $playlist['name'] . '</p>';
                    echo '<p>Created by: ' . $playlist['created_by'] . '</p>';
                    echo '<p>No. of Tracks: ' . $trackCount . '</p>';
                    echo '<button class="load-more-button" onclick="window.location.href=\'details.php?playlist=' . urlencode($playlistId) . '\'">Load More</button>';
                    echo '</div>';
                }
            }
        }
        else{
            foreach ($playlists as $playlistId => $playlist) {
                if ($playlist['public']) {
                    $trackCount = count($playlist['tracks']);
                    echo '<div class="playlist">';
                    echo '<img src="' . $playlist['image'] . '">';
                    echo '<p>' . $playlist['name'] . '</p>';
                    echo '<p>Created by: ' . $playlist['created_by'] . '</p>';
                    echo '<p>No. of Tracks: ' . $trackCount . '</p>';
                    echo '<button class="load-more-button" onclick="window.location.href=\'details.php?playlist=' . urlencode($playlistId) . '\'">Load More</button>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
</div>

<?php 
if (isset($_SESSION['user_id'])) { ?>
    <h1 id="my-heading">MY PLAYLISTS</h1>
    <div class="playlist-container">
        <?php
        $playlists = json_decode(file_get_contents('playlists.json'), true);

        $myPlaylists = [];
        foreach ($playlists as $playlistId => $playlist) {
            if ($playlist['created_by'] == $currentUser['username']) {
                $myPlaylists[$playlistId] = $playlist;
            }
        }

        foreach ($myPlaylists as $playlistId => $playlist) {
            $trackCount = count($playlist['tracks']);
            echo '<div class="playlist">';
            echo '<img src="' . $playlist['image'] . '">';
            echo '<p>' . $playlist['name'] . '</p>';
            echo '<p>Created by: ' . $playlist['created_by'] . '</p>';
            echo '<p>No. of Tracks: ' . $trackCount . '</p>';
            echo '<button class="load-more-button" onclick="window.location.href=\'details.php?playlist=' . urlencode($playlistId) . '\'">Load More</button>';
            echo '</div>';
        }
        ?>
    </div>
<?php } ?>


<div class="footer">
    <h2>Questions? Contact me: +36 20 586 7447</h2>
</div>


<script>
    const titleInput = document.querySelector('input[name="title"]');
    const artistInput = document.querySelector('input[name="artist"]');
    const lengthInput = document.querySelector('input[name="length"]');
    const yearInput = document.querySelector('input[name="year"]');
    const genresInput = document.querySelector('input[name="genres"]');
    const playlistsContainer = document.querySelector('.playlist-container');

    async function refreshPlaylists() {
        const params = new URLSearchParams({
            title: titleInput.value,
            artist: artistInput.value,
            length: lengthInput.value,
            year: yearInput.value,
            genres: genresInput.value
        });
        let resp = await fetch('playlists.php?' + params.toString());
        let data = await resp.json();
        console.log(data);
        playlistsContainer.innerHTML = "";
        for (const playlist of data) {
            let playlistDiv = document.createElement('div');
            playlistDiv.classList.add('playlist');

            let image = document.createElement('img');
            image.src = playlist.image;
            playlistDiv.appendChild(image);

            let name = document.createElement('p');
            name.textContent = playlist.name;
            playlistDiv.appendChild(name);

            let createdBy = document.createElement('p');
            createdBy.textContent = 'Created by: ' + playlist.created_by;
            playlistDiv.appendChild(createdBy);

            let trackCount = document.createElement('p');
            trackCount.textContent = 'No. of Tracks: ' + playlist.tracks.length;
            playlistDiv.appendChild(trackCount);

            let loadMoreButton = document.createElement('button');
            loadMoreButton.classList.add('load-more-button');
            loadMoreButton.textContent = 'Load More';
            loadMoreButton.addEventListener('click', function () {
                window.location.href = 'details.php?playlist=' + encodeURIComponent(playlistId);
            });
            playlistDiv.appendChild(loadMoreButton);

            playlistsContainer.appendChild(playlistDiv);
        }
    }

    refreshPlaylists();
    titleInput.addEventListener('input', refreshPlaylists);
    artistInput.addEventListener('input', refreshPlaylists);
    lengthInput.addEventListener('input', refreshPlaylists);
    yearInput.addEventListener('input', refreshPlaylists);
    genresInput.addEventListener('input', refreshPlaylists);
</script>
</body>
</html>
