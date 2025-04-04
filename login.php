<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<?php
Include "checkSession.php";
loginStatus();

if (isset($_POST['logout'])) logout();

if (isset($_POST['login']) and !empty($_POST['login']) and $_POST['login'] == 'Login'){
    include "config.php";
    $DBC = mysqli_connect(DBHOST,DBUSER,DBPASSWORD,DBDATABASE) or die();
    $error = 0;
    $msg = 'Error';

    if (isset($_POST['username']) and !empty($_POST['username']) and is_string($_POST['username'])){
        $un = htmlspecialchars(stripslashes(trim($_POST['username'])));
        $username = (strlen($un)>100)?substr($un,1,100):$un;
    } else {
        $error++;
        $msg = 'Invalid Username';
        $username = '';
    }

    $password = trim($_POST['password']);

    if ($error == 0){
        $query = "SELECT customerID, password 
        FROM customer
        WHERE email = '$username' and password = '$password' ";

        $result = mysqli_query($DBC, $query);

        if (mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_close($DBC);

            if ($password == $row['password']){
                login($row['customerID'], $username);
            }
        } else {
            echo "<h4>Login failed</h4>";
        }
    } else {
        echo "<h4>$msg</h4>" .PHP_EOL;
    }
}
?>
<h1>Customer Login</h1>
<h2><a href="index.php">[Return to the main page]</a></h2>
<form method="POST">
    <p>
        <label for="username">Email: </label>
        <input type="text" id="username" name="username" maxlength="100" autocomplete="off">
    </p>
    <p>
        <label for="password">Password: </label>
        <input type="password" id="password" name="password" maxlength="40" autocomplete="off">
    </p>
    <input type="submit" name="login" value="Login">
    <input type="submit" name="logout" value="Logout">
</form>
<p>can use following credentials for login testing:</p>
<p>Username: zak.andrews1603@gmail.com</p>
<p>Password: 123456789</p>
</body>
</html>