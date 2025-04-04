<?php
// Include configurations
include "config.php";
$DBC = mysqli_connect(DBHOST,DBUSER,DBPASSWORD,DBDATABASE);

if(mysqli_connect_errno())
    {
        // Print error if unable to connect to the database
        echo "Error: unable to connect to MySQL" .mysqli_connect_error();
        exit;
    }

$query = 'SELECT roomID, roomname, roomtype, beds
FROM room
WHERE roomID NOT IN (
    SELECT roomID 
    FROM booking
    WHERE checkindate >= ? AND checkoutdate <= ?)';

$checkin = $_GET['checkin'];
$checkout = $_GET['checkout'];
$stmt = mysqli_prepare($DBC, $query);
mysqli_stmt_bind_param($stmt, "ss", $checkin, $checkout);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;

            echo "<tr>";
            echo "<td>" . $row['roomID'] . "</td>";
            echo "<td>" . $row['roomname'] . "</td>";
            echo "<td>" . $row['roomtype'] . "</td>";
            echo "<td>" . $row['beds'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "No rooms available during the selected time period";
    }
} else {
    echo "Error executing the query" . $DBC->error;
}

mysqli_stmt_close($stmt);
mysqli_close($DBC);

?>