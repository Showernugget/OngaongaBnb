<!--
Assuming the update value and function to submit form is called update
Assuming the variable that holds booking id is called as $id
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
        $.datepicker.setDefaults({
            dateFormat: 'dd-mm-yy'
        });
        $(function() {
            checkout = $("#checkout").datepicker();
            checkin = $("#checkin").datepicker();
            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }
                return date;
            }
        });
    });
</script>
</head>
<?php

    // Link to config.php for credentials
    include "config.php";
    $DBC = mysqli_connect(DBHOST,DBUSER,DBPASSWORD,DBDATABASE);

    if (mysqli_connect_errno())
    {
        // Print error if unable to connect to the database
        echo "Error: unable to connect to MySQL" .mysqli_connect_error();
        exit;
    }

    Include "checkSession.php";
    checkUser();
    loginStatus();

    // Validating any data inputted
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Identifying ID
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        // Validating ID
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid Booking ID</h2>";
            exit;
        }
    }

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] =='Update')) {
        $error = 0;
        $msg = "Error";
        // Checking if ID is valid
        if (isset($_POST['id']) and !empty($_POST['id']) and is_numeric($_POST['id'])) {
            $id = cleanInput($_POST['id']);
            // Else raise error flag
        } else {
            $error++;
            $msg = 'Invalid booking ID';
            $id = 0;
        }

        // Cleaning input of all fields
        $roomID = isset($_POST['room']) ? intval($_POST['room']) : 0;
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $contactnumber = cleanInput($_POST['contactnumber']);
        $bookingextras = cleanInput($_POST['bookingextras']);
        $bookingreviews = cleanInput($_POST['bookingreviews']);
        //$id = cleanInput($_POST['bookingID']);

        if ($error == 0 and $id > 0) {
            $update = "UPDATE booking
            SET roomID=?, checkindate=?, checkoutdate=?, contactnumber=?, bookingextras=?, bookingreviews=?
            WHERE bookingID=?";
            $stmt = mysqli_prepare($DBC,$update);
            mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkin, $checkout, $contactnumber, $bookingextras, $bookingreviews, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking details updated</h2>";
        } else {
            echo "<h2>$msg</h2>" .PHP_EOL;
        }
    }

    $query = 'SELECT booking.bookingID, room.roomID, room.roomname, room.roomtype, room.beds, booking.checkindate,
    booking.checkoutdate, booking.contactnumber, booking.bookingextras, booking.bookingreviews
    FROM booking
    INNER JOIN room on booking.roomID = room.roomID
    WHERE bookingID=' .$id;
    $result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result);
    if ($rowcount > 0) {
        $row = mysqli_fetch_assoc($result);
?>
<body>
    <h1>Edit a booking</h1>
    <h2><a href="bookingList.php">[Return to the bookings list]</a><a href="index.php">[Return to main page]</a></h2>
    <form method="POST" action="editBooking.php">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <p>
            <label for="room">Room (name, type, beds): </label>
            <select name="room" id="room">
                <?php
                    if ($rowcount > 0) {
                        // $row = mysqli_fetch_assoc($result);
                    ?>
                    <option value="<?php echo $row['roomID']; ?>">
                        <?php
                        echo $row['roomname'] . " "
                        . $row['roomtype'] . " "
                        . $row['beds'] . " "
                        ?>
                    </option>
                    <?php
                    } else {
                        echo "<option>No bookings found</option>";
                    }
                    ?>
                
            </select>
        </p>
        <p>
            <label for="checkindate">Checkin date: </label>
            <input type="text" id="checkin" name="checkin" required
            value="<?php echo $row['checkindate'];?>" >
        </p>
        <p>
            <label for="checkoutdate">Checkout date: </label>
            <input type="text" id="checkout" name="checkout" required
            value="<?php echo $row['checkoutdate'];?>" >
        </p>
        <p>
            <label for="contactnumber">Contact number: </label>
            <input type="text" id="contactnumber" name="contactnumber" minlength="9" maxlength="12" required
            value="<?php echo $row['contactnumber'];?>" >
        </p>
        <p>
            <label for="bookingextras">Booking extras: </label>
            <textarea id="bookingextras" name="bookingextras" minlength="5" maxlength="200">
                <?php echo htmlspecialchars($row['bookingextras']); ?>
            </textarea>
        </p>
        <p>
            <label for="bookingreviews">Room review: </label>
            <textarea id="bookingreviews" name="bookingreviews" minlength="5" maxlength="200">
                <?php echo htmlspecialchars($row['bookingreviews']); ?>
            </textarea>
        </p>
        
        <input type="submit" name="submit" value="Update"><a href="createBooking.php">[cancel]</a>
    </form>
<?php
    } else {
        echo "<h2>Booking not found with that ID</h2>";
    }
    mysqli_close($DBC);
?>
</body>
</html>