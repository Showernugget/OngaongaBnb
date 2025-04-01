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
        // function for jquery date picker
    $( function() {
        $( "#datepicker" ).datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });

// Validation for contact number, booking extras, and booking reviews
var contactNumberValidation = function (contactnumber) {
    if (contactnumber.validity.valueMissing) {
        contactnumber.setCustomValidity("Your contact number is required.");
    } else if (contactnumber.validity.tooLong) {
        contactnumber.setCustomValidity("Your contact number can't be more than 12 characters long.")
    } else {
        contactnumber.setCustomValidity("");
    }
};

var bookingExtrasValidation = function (bookingextras) {
    if (bookingextras.validity.tooLong) {
        bookingextras.setCustomValidity("Your booking extras can't be more than 200 characters long.")
    } else {
        bookingextras.setCustomValidity("");
    }
};

var bookingReviewsValidation = function (bookingreviews) {
    if (bookingreviews.validity.tooLong) {
        bookingreviews.setCustomValidity("Your room review can't be more than 200 characters long.")
    } else {
        bookingreviews.setCustomValidity("");
    }
};

// Connecting above functions via ID
window.onload = function () {
 
    var form = document.getElementById("form");
    var contactnumber = document.getElementById("contactnumber");
    var bookingextras = document.getElementById("bookingextras");
    var bookingreviews = document.getElementById("bookingreviews");

    form.addEventListener("input", function () {
            contactNumberValidation(contactnumber);
            bookingExtrasValidation(bookingextras);
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
    ?>
    <h1>Edit a booking</h1>
    <h2><a href="bookingList.php">[Return to the bookings list]</a><a href="index.php">[Return to main page]</a></h2>
    <form method="POST" action="editBooking.php" id="form">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <p>
            <label for="roomname">Room (name, type, beds): </label>
            <input type="select" id="roomname" name="roomname" required>
        </p>
        <p>
            <label for="checkindate">Checkin date: </label>
            <input type="text" id="datepicker" name="checkindate" required>
        </p>
        <p>
            <label for="checkoutdate">Checkout date: </label>
            <input type="text" id="datepicker" name="checkoutdate" required>
        </p>
        <p>
            <label for="contactnumber">Contact number: </label>
            <input type="text" id="contactnumber" name="contactnumber" minlength="9" maxlength="12" required>
        </p>
        <p>
            <label for="bookingextras">Booking extras: </label>
            <textarea id="bookingextras" name="bookingextras" minlength="5" maxlength="200"></textarea>
        </p>
        <p>
            <label for="bookingreviews">Room review: </label>
            <textarea id="bookingreviews" name="bookingreviews" minlength="5" maxlength="200"></textarea>
        </p>
        
        <input type="submit" name="submit" value="Update"><a href="createBooking.php">[cancel]</a>
    </form>

    <?php
        mysqli_close($DBC);
    ?>
</body>
</html>