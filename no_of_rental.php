<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch updated data to display in the table again
$query = "SELECT id, name, amount, total_arrears, lone_date, due_date,no_pay FROM borrowers ORDER BY id ASC";
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
            xhr.open("GET", "no_of_rental1.php", true);
            xhr.send();
        }

        // Run updateDaysPassed every 10 minutes
        setInterval(updateDaysPassed, 1);
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
            <th>Pay days</th>
        </tr>
        <?php
        // Display updated borrower information
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['amount']}</td>";
            echo "<td>{$row['total_arrears']}</td>";
            echo "<td>{$row['lone_date']}</td>";
            echo "<td>{$row['no_pay']}</td>";  // Displaying the count of days passed
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>


