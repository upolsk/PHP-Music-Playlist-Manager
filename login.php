<?php
    session_start();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    function pass_verify($input, $object) {
        return $object['password'] === $input;
    }

    $error = false;
    if ($_POST){
        include_once('storage.php');
        $stor = new Storage(new JsonIO('users.json'));
        
        $user = $stor -> findOne([ 'username' => $username ]);
        if (!$user){
            $error = true;
        } else {
            if (!pass_verify($password, $user)){
                $error = true;
            } else {
                $_SESSION['user_id'] = $user['id'];
                header('location: main.php');
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="style.css">
  <title>Login Page</title>
</head>
<body>
    <section class = "login-section">
        <div class="form-box">
                <form method="POST" action="login.php">
                    <h2>Login</h2>
                    <div class="inputbox">
                        <input type="text" name="username">
                        <label for="">Username</label>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="password">
                        <label for="">Password</label>
                    </div>
                    <?php if ($error): ?>
                         <span style="color: red; font-weight: bold;">Invalid username and/or password!</span><br><br>
                    <?php endif; ?>                  
                    <button class = "login-button">Login</button>
                    <div class="register">
                        <p>Don't have a account <a href="register.php">Register</a></p>
                    </div>
                    <div class="logout-in-login">
                        <a href="logout.php">Logout</a>
                    </div>
                </form>
            </div>
    </section>
</body>
</html>
