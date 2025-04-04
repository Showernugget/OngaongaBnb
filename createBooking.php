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
    $(document).ready(function() {
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd'
        });
        $(function(){
            checkinBooking = $("#checkin").datepicker()
            checkoutBooking = $("#checkout").datepicker()

            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }
                return date;
            }
        })
    })
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

    Include "checkSession.php";
    checkUser();
    loginStatus();

    // Validating any data inputted
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')) {
        $error = 0;
        $msg = 'Error';

        $room = cleanInput($_POST['rooms']);
        // $customer = cleanInput($_POST['customers']);
        $checkinBooking = $_POST['checkin'];
        $checkoutBooking = $_POST['checkout'];
        $contactnumber = cleanInput($_POST['contactnumber']);
        $bookingextras = cleanInput($_POST['bookingextras']);

        $in = new DateTime($checkinBooking);
        $out = new DateTime($checkoutBooking);

        if (isset($_SESSION['userID'])) {
            $customerID = $_SESSION['userID'];
        } else {
            echo "<h4>No user is logged in</h4>";
        }

        if ($in >= $out) {
            $error++;
            $msg = "Check-out date cannot be the same day or earlier as check-in date.";
            $checkoutBooking = '';
        }

        if ($error == 0) {
            $create_query = "INSERT INTO booking 
            (roomID, customerID, checkindate, checkoutdate, contactnumber, bookingextras)
            VALUES (?,?,?,?,?,?)";

            $stmt = mysqli_prepare($DBC, $create_query);
            mysqli_stmt_bind_param($stmt, 'iissss', $room, $customerID, $checkinBooking, $checkoutBooking, $contactnumber, $bookingextras);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "<h3>Booking added successfully</h3>";
        } else {
            echo "<h3>$msg</h3>" .PHP_EOL;
        }

    }

    $query = 'SELECT roomID, roomname, roomtype, beds
    FROM room 
    ORDER BY roomID';
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

    $query1 = 'SELECT customerID, firstname, lastname
    FROM customer
    ORDER BY customerID';
    $result1 = mysqli_query($DBC, $query1);
    $rowcount1 = mysqli_num_rows($result1);

?>
<h1>Make a booking</h1>
<h2><a href="bookingList.php">[Return to bookings list]</a><a href="Index.php">[Return to the main page]</a></h2>
<form method="POST" action="createBooking.php">
    <div>
        <input type="hidden" name="customerID" value="<?php echo $_SESSION['userID']; ?>">
        <label for="rooms">Room (name, type, beds):</label>
        <select name="rooms" id="rooms">
            <?php
                if ($rowcount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = $row['roomID']; ?>

                        <option value="<?php echo $row['roomID']; ?>">
                            <?php echo $row['roomname'] . ' '
                                .$row['roomtype'] . ' '
                                .$row['beds']
                            ?>
                        </option>
                    <?php }
                } else {
                    echo "<option>No rooms found!</option>";
                    mysqli_free_result($result);
                }
            ?>
        </select>
    </div>
    <p>
        <label for="checkindate">Checkin date: </label>
        <input type="text" id="checkin" name="checkin" placeholder="dd-mm-yyyy" required>
    </p>
    <p>
        <label for="checkoutdate">Checkout date: </label>
        <input type="text" id="checkout" name="checkout" placeholder="dd-mm-yyyy" required>
    </p>
    <p>
        <label for="contactnumber">Contact number: </label>
        <input type="text" id="contactnumber" name="contactnumber" placeholder="(###) ### ####" minlength="9" maxlength="12" required>
    </p>
    <p>
        <label for="bookingextras">Booking extras: </label>
        <input type="text" id="bookingextras" name="bookingextras" size="100" minlength="5" maxlength="200">
    </p>
       
    <input type="submit" name="submit" value="Book"><a href="index.php">[cancel]</a>
</form>
<hr>
<div class="container">
    <h1>Search for room availability</h1>
    <form id="searchForm" method="get" name="searching">
        <input type="text" id="checkin1" name="checkin1" placeholder="Check-in date" required>
        <input type="text" id="checkout1" name="checkout1" placeholder="Check-out date" required>
        <input type="submit" value="Search">
    </form>
</div>
<br><br>
<div class="row">
    <table id="bookingsTable" border="1">
        <thead>
            <tr>
                <th>Room ID</th>
                <th>Room name</th>
                <th>Room type</th>
                <th>Beds</th>
            </tr>
        </thead>
        <tbody id="result"></tbody>
    </table>
</div>

<script>
$(document).ready(function(){
    $("#checkin1").datepicker({dateFormat:"yy-mm-dd"});
    $("#checkout1").datepicker({dateFormat:"yy-mm-dd"});

    $("#searchForm").submit(function(event) {
        event.preventDefault();
        var checkin = $("#checkin1").val();
        var checkout = $("#checkout1").val();

        if (checkin > checkout) {
            alert("Check-in date cannot be later than check-out date");
            return false;
        }

        searchRooms();
    });
});

function searchRooms(){
    var checkin = $("#checkin1").val();
    var checkout = $("#checkout1").val();

    $.ajax({
        url: "roomSearch.php",
        method: "GET",
        data: {checkin: checkin, checkout: checkout},
        success: function(response) {
            $("#result").html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
}
</script>

<?php
        mysqli_close($DBC);
?>
</body>
</html>