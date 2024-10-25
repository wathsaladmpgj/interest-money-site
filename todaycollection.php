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
    $sql = "SELECT payments.rental_amount, payments.payment_date,payments.du_date, borrowers.name,borrowers.total_arrears,borrowers.rental
            FROM payments
            INNER JOIN borrowers ON payments.borrower_id = borrowers.id
            WHERE payments.payment_date = '$selected_date'";
    
    $result = $conn->query($sql);

    // Query to get borrowers who haven't made payments on the selected date (those whose due date is today or in the future)
    $sq = "SELECT name, rental,total_arrears,total_payments 
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
    <link rel="stylesheet" href="./css/today.css">
</head>
<body>

    <h1>Select Payments by Date</h1>

    <?php if(isset($result) || isset($resul)) { ?>
    <form method="POST" action="generate_pdf.php">
        <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
        <button type="submit" name="download_pdf">Download as PDF</button>
    </form>
    <?php } ?>

    <!-- Form to select a date -->
    <form method="POST" action="">
        <label for="payment_date">Select Date:</label>
        <input type="date" id="payment_date" name="payment_date" required>
        <button type="submit" name="submit">Search</button>
    </form>

    <!-- Display the results if any -->
    <?php
    $row_number = 1;
    // Show payments made on the selected date
    if (isset($result) && $result->num_rows > 0) {
        echo "<h2>Payments on " . $selected_date . ":</h2>";
        echo "<table border='1'>
                <tr>
                    <th>No</th>
                    <th>Borrower Name</th>
                    <th>Due date</th>
                    <th>Payment Date</th>
                    <th>Rental</th>
                    <th>Payment Amount</th>
                    <th>Total Arrears</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row_number++."</td>
                    <td>" . $row['name'] . "</td>
                    <td>" .$row['du_date']."</td>
                    <td>" . $row['payment_date'] . "</td>
                    <td>" . $row['rental'] . "</td>
                    <td>" . $row['rental_amount'] . "</td>
                    <td>" . $row['total_arrears'] . "</td>
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
                    <th>No</th>
                    <th>Borrower Name</th>
                    <th>Rental</th>
                    <th>Total Payment</th>
                    <th>Total Arrears</th>
                </tr>";

        while($row = $resul->fetch_assoc()) {
            echo "<tr>
                    <td>".$row_number++."</td>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['rental'] . "</td>
                    <td>" . $row['total_payments'] . "</td>
                    <td>" . $row['total_arrears'] . "</td>
                </tr>";
        }
        echo "</table>";
    }
    ?>

    <!-- Form to trigger PDF generation -->
    

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
