<!--
Assuming the submit button value and function to submit form is called Add
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create booking</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    // function for jquery date picker
    $( function() {
        $( "#datepicker" ).datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $( "fromDate" ).datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $( "toDate" ).datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
// Seatch function for availability
function searchBookings() {
    var fromDate = $("fromDate").val();
    var toDate = $("toDate").val();
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            $("#result").html(this.responseText);
        }
    }
};

// Validation for contact number, and booking extras     
var contactNumberValidation = function (contactnumber) {
    if (contactnumber.validity.valueMissing) {
        contactnumber.setCustomValidity("Your contact number is required.");
    } else if (contactnumber.validity.tooLong) {
        contactnumber.setCustomValidity("Your contact number can't be more than 12 characters long.")
    } else {
        contactnumber.setCustomValidity("");
    }};

var bookingExtrasValidation = function (bookingextras) {
    if (bookingextras.validity.tooLong) {
        bookingextras.setCustomValidity("Your booking extras can't be more than 200 characters long.")
    } else {
        bookingextras.setCustomValidity("");
    }};
 
    window.onload = function () {
 
        var form = document.getElementById("form");
        var contactnumber = document.getElementById("contactnumber");
        var bookingextras = document.getElementById("bookingextras");
 
        form.addEventListener("input", function () {
            contactNumberValidation(contactnumber);
            bookingExtrasValidation(bookingextras);
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
    <h1>Make a booking</h1>
    <h2><a href="bookingList.php">[Return to bookings list]</a><a href="Index.php">[Return to the main page]</a></h2>
    <form method="POST" action="createBooking.php" id="form">
        <p>
            <label for="roomname">Room (name, type, beds): </label>
            <input type="select" id="roomname" name="roomname" required>
        </p>
        <p>
            <label for="checkindate">Checkin date: </label>
            <input type="text" id="datepicker" name="checkindate" placeholder="dd-mm-yyyy" required>
        </p>
        <p>
            <label for="checkoutdate">Checkout date: </label>
            <input type="text" id="datepicker" name="checkoutdate" placeholder="dd-mm-yyyy" required>
        </p>
        <p>
            <label for="contactnumber">Contact number: </label>
            <input type="text" id="contactnumber" name="contactnumber" placeholder="(###) ### ####" minlength="9" maxlength="12" required>
        </p>
        <p>
            <label for="bookingextras">Booking extras: </label>
            <input type="text" id="bookingextras" name="bookingextras" size="100" minlength="5" maxlength="200">
        </p>
        
        <input type="submit" name="submit" value="Add"><a href="createBooking.php">[cancel]</a>
    </form>
<hr>
<div class="container">
    <h1>Search for room availability</h1>
    <p>
        <label for="checkindate">Start date: </label>
        <input type="text" id="fromDate" name="checkindate" required>
        
        <label for="checkoutdate">End date: </label>
        <input type="text" id="toDate" name="checkoutdate" required>

        <input type="submit" value="Search" onclick="searchBookings()">
    </p>

    <table id="result" border="1"></table>
</div>
<?php
        mysqli_close($DBC);
?>
</body>
</html>