<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit or add review</title>
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

    // Validating any data inputted
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Finding ID via get
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        // Validating ID
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>";
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

        $bookingreviews = cleanInput($_POST['bookingreviews']);

        if ($error == 0 and $id > 0) {
            $update = 'UPDATE booking
            SET bookingreviews=?
            WHERE bookingID=?';
            $stmt = mysqli_prepare($DBC,$update);
            mysqli_stmt_bind_param($stmt, 'si', $bookingreviews, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking details updated</h2>";
        } else {
            echo "<h2>$msg</h2>" .PHP_EOL;
        }
    }
    

    // Retrieve data from multiple tables
    $query = 'SELECT booking.bookingID, customer.customerID, customer.firstname, booking.bookingreviews
    FROM booking
    INNER JOIN customer on booking.customerID = customer.customerID
    WHERE bookingID=' .$id;

    $result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result);
    if ($rowcount > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstname = $row['firstname'];
?>
<h1>Edit/add room review</h1>
<h2><a href="bookingList.php">[Return to booking listing]</a><a href="index.php">[Return to the main page]</a></h2>
<form method="POST" action="editRoomReview.php" id="form">
    <?php
        // If review is set then show who made the review
        if (!empty($row['bookingreviews'])) {
            echo "<h2>Review made by $firstname</h2>";
        }
    ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <p>
        <label for="bookingreviews">Room review: </label>
        <textarea id="bookingreviews" name="bookingreviews" minlength="5" maxlength="200">
            <?php echo htmlspecialchars($row['bookingreviews']); ?>
        </textarea>
    </p>
    <input type="submit" name="submit" value="Update">
</form>
<?php
    } else {
        echo "<h2>Booking not found with that ID</h2>";
    }
    mysqli_close($DBC);
?>
</body>
</html>