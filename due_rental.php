<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Colombo');
// Fetch updated data to display in the table again
$query = "SELECT id, name, amount, total_arrears, lone_date, due_date, days_passed FROM borrowers ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Days Passed Information</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
    <script>
        function updateDaysPassed() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "due_rental1.php", true);
            xhr.send();
        }

        // Run updateDaysPassed every 10 minutes
        setInterval(updateDaysPassed, 10 * 60 * 1000);
    </script>
</head>
<body onload="updateDaysPassed()">
    <h2>Borrower Days Passed Information</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Loan Amount</th>
            <th>Arrears</th>
            <th>Loan Taken Date</th>
            <th>Days Passed (Expected)</th>
        </tr>
        <?php
        // Display updated borrower information
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['amount']}</td>";
            echo "<td>{$row['total_arrears']}</td>";
            echo "<td>{$row['lone_date']}</td>";
            echo "<td>{$row['days_passed']}</td>";  // Displaying the count of days passed
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
