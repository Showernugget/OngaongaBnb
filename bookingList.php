<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking list</title>
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

        // Testing connection
        // echo "Connected via ".mysqli_get_host_info($DBC);
        // mysqli_close($DBC);

        // Retrieve data from multiple tables
        $query = 'SELECT booking.bookingID, room.roomname, booking.checkindate, booking.checkoutdate, customer.firstname, customer.lastname
        FROM booking, room, customer
        WHERE booking.roomID = room.roomID AND booking.customerID = customer.customerID
        ORDER BY bookingID';
        $result = mysqli_query($DBC,$query);
        $rowcount = mysqli_num_rows($result);

    ?>
    <h1>Booking list</h1>
    <h2>
        <a href="createBooking.php">[Make a booking]</a>
        <a href="Index.php">[Return to the main page]</a>
    </h2>
    <table border="1">
        <thead>
            <tr>
                <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>
        <?php
        // if the row count is more than 0, print results while there are rows
        if ($rowcount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['bookingID'];
                echo '<tr><td>' . $row['roomname'] . ', ' . $row['checkindate'] . ', ' . $row['checkoutdate'] . '</td>';
                echo '<td>' . $row['lastname'] . ', ' . $row['firstname'] . '</td>';
                echo '<td><a href="bookingDetail.php?id=' . $id . '">[view]</a>';
                echo '<a href="editBooking.php?id=' . $id . '">[edit]</a>';
                echo '<a href="editRoomReview.php?id=' . $id . '">[manage reviews]</a>';
                echo '<a href="deleteBooking.php?id=' . $id . '">[delete]</a></td>';
                echo '</tr>'.PHP_EOL;
            }
        }   else echo "<h2>No rooms found!</h2>";

        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </table>
</body>
</html>