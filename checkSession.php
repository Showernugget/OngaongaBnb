<?php
session_start();

function checkUser(){
    $_SESSION['URI'] = '';

    if ($_SESSION['loggedin'] == 1){
        return true;
    } else {
        $_SESSION['URI'] = '/'.$_SERVER['REQUEST_URI'];
        header(('Location:/partTwoBnb/login.php'), true, 303);
        exit();
    }
}

function loginStatus(){
    $un = $_SESSION['username'];
    
    if ($_SESSION['loggedin'] == 1){
        echo "<h6>Logged in as $un</h6>";
    } else {
        echo "<h6>Logged out</h6>";
    }
}

function login($id, $username) {
    
    if ($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI'])){
        $uri = $_SESSION['URI'];
    } else {
        $_SESSION['URI'] = '/partTwoBnb/bookingList.php';
        $uri = $_SESSION['URI'];
    }

    header('Location:/partTwoBnb/bookingList.php', true, 303);
    $_SESSION['loggedin'] = 1;
    $_SESSION['userID'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['URI'] = '';
}

function logout() {
    $_SESSION['loggedin'] = 0;
    $_SESSION['userID'] = -1;
    $_SESSION['username'] = '';
    $_SESSION['URI'] = '';

    header(('Location:/partTwoBnb/bookingList.php'), true, 303);
}

?>