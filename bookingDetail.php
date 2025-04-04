<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking details</title>
</head>
<body>
<?php
    // Link to config.php for credentials
    include "config.php";
    $DBC = mysqli_connect(DBHOST,DBUSER,DBPASSWORD,DBDATABASE);

    if(mysqli_connect_errno())
    {
        // Print error if unable to connect to the database
        echo "Error: unable to connect to MySQL" .mysqli_connect_error();
        exit;
    }

    Include "checkSession.php";
    checkUser();
    loginStatus();

    // Getting booking ID based on what booking you clicked 'view detail'
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>";
        exit;
    }
    
    // retrieve data from multiple tables
    $query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate, booking.contactnumber, booking.bookingextras, booking.bookingreviews
    FROM booking, room
    WHERE booking.roomID = room.roomID AND bookingID='.$id;
    $result = mysqli_query($DBC,$query);
    
    // This code did not work and showed me a fatal error
    // $rowcount = mysqli_num_rows($result) 

    // This code show results but also shows notice at top of the screen however is still functional
    if($result != 0)
    {
        $rowcount = mysqli_num_rows($result);
    }
?>
    <h1>Booking details view</h1>
    <h2><a href="bookingList.php">[Return to the booking listing]</a><a href="index.php">[Return to the main page]</a></h2>
<?php
    // Checking room exists and if so, then print details
    if ($rowcount > 0) {
        echo "<fieldset><legend>Booking detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        echo "<dt>Roomname: </dt><dd>".$row['roomname']."</dd>".PHP_EOL;
        echo "<dt>Checkin date: </dt><dd>".$row['checkindate']."</dd>".PHP_EOL;
        echo "<dt>Checkout date: </dt><dd>".$row['checkoutdate']."</dd>".PHP_EOL;
        echo "<dt>Contact number: </dt><dd>".$row['contactnumber']."</dd>".PHP_EOL;
        echo "<dt>Extras: </dt><dd>".$row['bookingextras']."</dd>".PHP_EOL;
        echo "<dt>Room review: </dt><dd>".$row['bookingreviews']."</dd>".PHP_EOL;
        echo "</dl></fieldset>".PHP_EOL;
    } else echo "<h2>No booking found!</h2>";

    mysqli_close($DBC);
?>
</body>
</html>