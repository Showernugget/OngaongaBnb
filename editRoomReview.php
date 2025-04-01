<!--
$id is hard set to 1 so there are still results on the screen however this will change during next assessment
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit or add review</title>
<script>
// Validating booking reviews field
var bookingReviewsValidation = function (bookingreviews) {
    if (bookingreviews.validity.tooLong) {
        bookingreviews.setCustomValidity("Your room review can't be more than 200 characters long.")
    } else {
        bookingreviews.setCustomValidity("");
    }
};

// Connecting above functions to form via ID
window.onload = function() {
    var form = document.getElementById("form");
    var bookingreviews = document.getElementById("bookingreviews");

    form.addEventListener("input", function () {
            bookingReviewsValidation(bookingreviews);
    });
};

</script>
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

    // The following code has been giving fatal errors and warnings so I commented it for the front-end development
    // but will be essential for back-end 

    // Getting booking ID based on what booking you clicked 'view detail'
    //$id = $_GET['id'];
    //if (empty($id) or !is_numeric($id)) {
    //    echo "<h2>Invalid booking ID</h2>";
    //    exit;
    //}

    // Above function did not work so the below code is an alteration

    //if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //    $id = $_GET['id'];
    //    if (empty($id) or !is_numeric($id)) {
    //        echo "<h2>Invalid booking ID</h2>";
    //        exit;
    //    }
    //}

    // Set at 1 for testing
    $id = 1;

    // Retrieve data from multiple tables
    $query = 'SELECT booking.bookingID, customer.firstname, booking.roomreviews
    FROM booking, customer
    WHERE bookingID='.$id;

    $result = mysqli_query($DBC,$query);
    //$rowcount = mysqli_num_rows($result);
?>
<h1>Edit/add room review</h1>
<h2><a href="bookingList.php">[Return to booking listing]</a><a href="index.php">[Return to the main page]</a></h2>
<form method="POST" action="editRoomReview.php" id="form">
    <h2>Review made by Test</h2> <!-- Unsure how to link customer.firstname into the <h2> tag -->
    <p>
        <label for="bookingreviews">Room review: </label>
        <textarea id="bookingreviews" name="bookingreviews" minlength="5" maxlength="200"></textarea>
    </p>
    <input type="submit" name="submit" value="Update">
</form>
</body>
</html>