<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('storage.php');
$stor = new Storage(new JsonIO('users.json'));
$currentUser = $stor->findById($_SESSION['user_id']);

$trackId = $_GET['track_id'] ?? '';
$errors = [];


$tracks = json_decode(file_get_contents('tracks.json'), true) ?? [];

if (!empty($tracks) && isset($tracks[$trackId])) {
    $existingTrack = $tracks[$trackId];
    $title = $existingTrack['title'];
    $artist = $existingTrack['artist'];
    $length = $existingTrack['length'];
    $year = $existingTrack['year'];
    $genres = implode(", ", $existingTrack['genres']);
    $source = $existingTrack['source'];
    $img = $existingTrack['img'];

    if ($_POST) {
        $title = $_POST['title'] ?? '';
        $artist = $_POST['artist'] ?? '';
        $length = $_POST['length'] ?? '';
        $year = $_POST['year'] ?? '';
        $genres = isset($_POST['genres']) ? $_POST['genres'] : '';
        $source = $_POST['source'] ?? '';
        $img = $_POST['img'] ?? '';

        if ($title == '') {
            $errors[] = "Title is required.";
        }

        if ($artist == '') {
            $errors[] = "Artist is required.";
        }

        if ($length == '') {
            $errors[] = "Length is required.";
        } elseif (!filter_var($length, FILTER_VALIDATE_INT)) {
            $errors['length'] = "Length must be an integer!";
        }

        if ($year == '') {
            $errors['year'] = "Year is required";
        } elseif (!filter_var($year, FILTER_VALIDATE_INT)) {
            $errors['year'] = "Year must be an integer!";
        }

        $genres = trim($genres);
        if (!empty($genres)) {
            if (strpos($genres, ',') !== false) {
                $genres = explode(',', $genres);
                $genres = array_map('trim', $genres);
            } else {
                $errors[] = "Genres must be comma-separated words.";
            }
        } else {
            $errors[] = "Genres is required";
        }

        if ($source == '') {
            $errors[] = "Source is required.";
        }

        if ($img == '') {
            $errors[] = "Image is required.";
        } elseif (!filter_var($img, FILTER_VALIDATE_URL)) {
            $errors['img'] = 'Please enter a valid image URL.';
        }

        if (empty($errors)) {
            $tracks[$trackId]['title'] = $title;
            $tracks[$trackId]['artist'] = $artist;
            $tracks[$trackId]['length'] = $length;
            $tracks[$trackId]['year'] = $year;
            $tracks[$trackId]['genres'] = $genres;
            $tracks[$trackId]['source'] = $source;
            $tracks[$trackId]['img'] = $img;

            file_put_contents('tracks.json', json_encode($tracks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            header('Location: main.php');
            exit;
        }
    }
} else {
    echo "Track id is not found!";
    header('Location: main.php');
    exit;
}
$errors = array_map(fn ($e) => "<span style='color:red'>$e</span>", $errors);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Track</title>
</head>
<body>
    <h1 class = "add-track-h1">Edit Track</h1>
    <div class = "add-track-div">
    <?php if (!empty($errors)) : ?>
        <div class="error">
            <?php foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" novalidate>
    <input type="hidden" name="track_id" value="<?php echo $trackId; ?>">

<label for="title">Title:</label>
<input type="text" name="title" value="<?php echo $existingTrack['title']; ?>"><br>

<label for="artist">Artist:</label>
<input type="text" name="artist" value="<?php echo $existingTrack['artist']; ?>"><br>

<label for="length">Length:</label>
<input type="text" name="length" value="<?php echo $existingTrack['length']; ?>"><br>

<label for="year">Year:</label>
<input type="text" name="year" value="<?php echo $existingTrack['year']; ?>"><br>

<label for="genres">Genres:</label>
<input type="text" name="genres" value="<?php echo implode(", ", $existingTrack['genres']); ?>"><br>

<label for="source">Source:</label>
<input type="text" name="source" value="<?php echo $existingTrack['source']; ?>"><br>

<label for="img">Image:</label>
<input type="text" name="img" value="<?php echo $existingTrack['img']; ?>"><br>

<button class = "save-button-in-new-paylist" type="submit">Save</button>
</form>
 </div>
</body>
</html>