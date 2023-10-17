<?php
session_start();

include_once 'storage.php';
$stor = new Storage(new JsonIO('users.json'));
$users = $stor->findAll();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$passwordConfirmation = trim($_POST['passwordConfirmation'] ?? '');

$errors = [];

if ($_POST) {
  if (empty($username)) {
    $errors['username'] = 'The username is required.';
  }
  if (empty($email)) {
    $errors['email'] = 'The email is required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'The email is invalid.';
  } else {
    foreach ($users as $user) {
      if ($user['email'] == $email) {
        $errors['email'] = 'The email is already in use.';
        break;
      }
    }
  }
  
  if (empty($password)) {
    $errors['password'] = 'The password is required.';
  }

  if (empty($passwordConfirmation)) {
    $errors['passwordConfirmation'] = 'Confirm the password!';
  } elseif ($password != $passwordConfirmation) {
    $errors['passwordConfirmation'] = 'The passwords do not match.';
  }

  if (empty($errors)) {
    $stor->add(
      [
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'isAdmin' => false,
      ]);
    header('Location: main.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <section class="register-section">
    <div class="form-box-register">
      <form method="post" action="" novalidate>
        <h2 class="signup-h2">Sign Up</h2>
        <div class="inputbox-register">
          <label for="username">Username:</label><br>
          <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>">
          <?php if (!empty($errors['username'])): ?>
            <span class="error-message"><?= $errors['username'] ?></span>
          <?php endif; ?>
        </div>
        <div class="inputbox-register">
          <label for="email">Email:</label><br>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
          <?php if (!empty($errors['email'])): ?>
            <span class="error-message"><?= $errors['email'] ?></span>
          <?php endif; ?>
        </div>
        <div class="inputbox-register">
          <label for="password">Password:</label><br>
          <input type="password" id="password" name="password">
          <?php if (!empty($errors['password'])): ?>
            <span class="error-message"><?= $errors['password'] ?></span>
          <?php endif; ?>
        </div>
        <div class="inputbox-register">
          <label for="passwordConfirmation">Confirm Password:</label><br>
          <input type="password" id="passwordConfirmation" name="passwordConfirmation">
          <?php if (!empty($errors['passwordConfirmation'])): ?>
            <span class="error-message"><?= $errors['passwordConfirmation'] ?></span>
          <?php endif; ?>
        </div>
        <div class="register-submit">
          <button class="signup-button">Sign Up</button>
        </div>
        <div class="register-signup">
          <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
      </form>
    </div>
  </section>
</body>
</html>
