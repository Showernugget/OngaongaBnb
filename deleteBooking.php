<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete booking</title>
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

    // This code was giving errors so slight variation is used below
    //$id = $_GET['id'];
    //if (empty($id) or !is_numeric($id)) {
    //    echo "<h2>Invalid booking ID</h2>";
    //    exit;
    //}

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>";
            exit;
        }
    }

    // Retrieve data from multiple tables
    $query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate
    FROM booking, room
    WHERE booking.roomID = room.roomID AND bookingID='.$id;

    $result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result);
?>

<h1>Booking preview before deletion</h1>
<h2><a href="bookingList.php">[Return to the booking listing]</a><a href="index.php">[Return to the main page]</a></h2>

<?php
    // checking the booking exists
    if ($rowcount > 0) {
        echo "<fieldset><legend>Booking detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        echo "<dt>Roomname: </dt><dd>".$row['roomname']."</dd>".PHP_EOL;
        echo "<dt>Checkin date: </dt><dd>".$row['checkindate']."</dd>".PHP_EOL;
        echo "<dt>Checkout date: </dt><dd>".$row['checkoutdate']."</dd>".PHP_EOL;
    }
?>

<form method="POST" action="deleteBooking.php">
    <h2>Are you sure you want to delete this booking?</h2>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" name="submit" value="delete">
    <a href="bookingList.php">[Cancel]</a>
</form>
<?php
    mysqli_close($DBC);
?>
</body>
</html>