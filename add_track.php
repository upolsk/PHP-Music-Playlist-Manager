
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('storage.php');
$stor = new Storage(new JsonIO('users.json'));
$currentUser = $stor->findById($_SESSION['user_id']);
if (!$currentUser['isAdmin']) {
    $_SESSION['message'] = "Access denied. You do not have admin privileges.";
    header('Location: main.php');
    exit;
}

$title = $_POST['title'] ?? '';
$artist = $_POST['artist'] ?? '';
$length = $_POST['length'] ?? '';
$year = $_POST['year'] ?? '';
$source = $_POST['source'] ?? '';
$genres = isset($_POST['genres']) ? $_POST['genres'] : '';
$img = $_POST['img'] ?? '';

$errors = [];

if ($_POST) {
    if ($title == '') {
        $errors[] = "Title is required.";
    }

    if ($artist == '') {
        $errors[] = "Artist is required.";
    }

    if ($length == '') {
        $errors[] = "Length is required.";
    }
    else if(false === (filter_var($length, FILTER_VALIDATE_INT)))
    {
        $errors['length'] = "Length must be an integer!";
    }

    if($year == '')
    {
        $errors['year'] = "Year is required";
    }
    else if(false === (filter_var($year, FILTER_VALIDATE_INT)))
    {
        $errors['year'] = "Year must be an integer!";
    }


    $genres = trim($genres);
    if (!empty($genres)) {
        $pattern = '/^[\w\s]+(?:,[\w\s]+)*$/';

        if (!preg_match($pattern, $genres)) {
            $errors[] = "Genres must be comma-separated words.";
        } else {
            $genres = explode(',', $genres);
            $genres = array_map('trim', $genres);
        }
    } else {
        $errors[] = "Genres is required.";
    }

    if ($source == '') {
        $errors[] = "Source is required.";
    }

    if ($img == '') {
        $errors[] = "Image is required.";
    }
    elseif (!filter_var($img, FILTER_VALIDATE_URL)) {
        $errors['img'] = 'Please enter a valid image URL.';
    }


    if (empty($errors)) {
        $tracks = json_decode(file_get_contents('tracks.json'), true) ?? [];

        $newTrack = [
            'track_id' => "track" . (count($tracks) + 1),
            'title' => $title,
            'artist' => $artist,
            'length' => $length,
            'year' => $year,
            'genres' => $genres,
            'source' => $source,
            'img' => $img
        ];

        $tracks[$newTrack['track_id']] = $newTrack;
        file_put_contents('tracks.json', json_encode($tracks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        header('Location: main.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Add Track</title>
</head>

<body>
    <h1 class = "add-track-h1">Add Track</h1>
    <?php if (!empty($errors)) : ?>
        <div class="error">
        <?php foreach ($errors as $error) : ?>
        <span style="color:red"><?php echo $error; ?></span><br>
    <?php endforeach; ?>

        </div>
    <?php endif; ?>
    <div class = "add-track-div">
    <form method="POST" nonvalidate>
        <input type="hidden" name="track_id" value="">

        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo $title; ?>"><br>

        <label for="artist">Artist:</label>
        <input type="text" name="artist" value="<?php echo $artist; ?>"><br>

        <label for="length">Length:</label>
        <input type="text" name="length" value="<?php echo $length; ?>"><br>

        <label for="year">Year:</label>
        <input type="text" name="year" value="<?php echo $year; ?>"><br>

        <label for="genres">Genres:</label>
        <input type="text" name="genres" value=""><br>

        <label for="source">Source:</label>
        <input type="text" name="source" value="<?php echo $year; ?>"><br>

        <label for="img">Image:</label>
        <input type="text" name="img" value="<?php echo $img; ?>"><br>

        <button class = "save-button-in-new-paylist" type="submit">Save</button>
    </form>
            </div>
</body>

</html>