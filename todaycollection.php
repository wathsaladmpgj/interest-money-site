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

if (isset($_POST['submit'])) {
    $selected_date = $_POST['payment_date'];

    // Query to get payments made on the selected date and their borrower names
    $sql = "SELECT payments.rental_amount, payments.payment_date, borrowers.name 
            FROM payments
            INNER JOIN borrowers ON payments.borrower_id = borrowers.id
            WHERE payments.payment_date = '$selected_date'";
    
    $result = $conn->query($sql);

    // Query to get borrowers who haven't made payments on the selected date (those whose due date is today or in the future)
    $sq = "SELECT name, rental 
           FROM borrowers 
           WHERE id NOT IN (SELECT borrower_id FROM payments WHERE payment_date = '$selected_date')
           AND due_date >= CURDATE()";
    $resul = $conn->query($sq);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payments by Date</title>
    <link rel="stylesheet" href="./today.css">
</head>
<body>

    <h1>Select Payments by Date</h1>

    <!-- Form to select a date -->
    <form method="POST" action="">
        <label for="payment_date">Select Date:</label>
        <input type="date" id="payment_date" name="payment_date" required>
        <button type="submit" name="submit">Search</button>
    </form>

    <!-- Display the results if any -->
    <?php
    // Show payments made on the selected date
    if (isset($result) && $result->num_rows > 0) {
        echo "<h2>Payments on " . $selected_date . ":</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Borrower Name</th>
                    <th>Rental Amount</th>
                    <th>Payment Date</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['rental_amount'] . "</td>
                    <td>" . $row['payment_date'] . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No payments found for the selected date.</p>";
    }

    // Show borrowers who have not made payments on the selected date
    if (isset($resul) && $resul->num_rows > 0) {
        echo "<h2>Borrowers with no payments on " . $selected_date . ":</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Borrower Name</th>
                    <th>Rental</th>
                </tr>";

        while($row = $resul->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['rental'] . "</td>
                </tr>";
        }
        echo "</table>";
    }
    ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
