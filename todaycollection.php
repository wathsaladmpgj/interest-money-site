<?php
// Establish a connection to your MySQL database
$servername = "localhost";
$username = "root"; // adjust if needed
$password = ""; // adjust if needed
$dbname = "interest"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $selected_date = $_POST['payment_date'];

    // Query to get payments on the selected date and join it with borrowers to get the name
    $sql = "SELECT payments.rental_amount, payments.payment_date, borrowers.name 
            FROM payments
            INNER JOIN borrowers ON payments.borrower_id = borrowers.id
            WHERE payments.payment_date = '$selected_date'";
    
    $result = $conn->query($sql);
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
    if (isset($result) && $result->num_rows > 0) {
        echo "<h2>Payments on " . $selected_date . ":</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Borrower Name</th>
                    <th>Rental Amount</th>
                    <th>Payment Date</th>
                </tr>";

        // Fetch and display each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['rental_amount'] . "</td>
                    <td>" . $row['payment_date'] . "</td>
                </tr>";
        }
        echo "</table>";
    } elseif (isset($_POST['submit'])) {
        echo "<p>No payments found for the selected date.</p>";
    }
    ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
