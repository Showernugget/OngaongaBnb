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

    Include "checkSession.php";
    checkUser();
    loginStatus();

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Finding booking ID from URL
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>";
            exit;
        }
    }

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
        $error = 0;
        $msg = "Error";

        // Validating ID
        if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
            $id = cleanInput($_POST['id']);

            // Else trigger error message
        } else {
            $error++;
            $msg = 'Invalid Booking ID';
            $id = 0;
        }
        // If no error and id is more than 0
        if ($error == 0 and $id > 0) {
            $del_query ="DELETE FROM booking WHERE bookingid=?";
            $stmt = mysqli_prepare($DBC,$del_query);
            mysqli_stmt_bind_param($stmt,'i',$id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking details deleted.</h2>";

        } else {
            echo "<h5>$msg</h5>" .PHP_EOL;
        }
    }

    // Retrieve data from multiple tables
    $query = 'SELECT booking.bookingID, booking.roomID, booking.checkindate, booking.checkoutdate, room.roomID, room.roomname
    FROM booking
    INNER JOIN room on booking.roomID = room.roomID 
    WHERE booking.bookingID=' .$id;

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
        $id = $row['bookingID'];
        echo "<dt>Roomname: </dt><dd>".$row['roomname']."</dd>".PHP_EOL;
        echo "<dt>Checkin date: </dt><dd>".$row['checkindate']."</dd>".PHP_EOL;
        echo "<dt>Checkout date: </dt><dd>".$row['checkoutdate']."</dd>".PHP_EOL;
    
?>

    <form method="POST" action="deleteBooking.php">
        <h2>Are you sure you want to delete this booking?</h2>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="submit" name="submit" value="Delete">
        <a href="bookingList.php">[Cancel]</a>
    </form>
    <?php
        } else echo "<h4>No booking found! </h4>";
        mysqli_free_result($result);
        mysqli_close($DBC);
    ?>
</body>
</html>