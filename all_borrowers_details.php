<?php
// db_connection.php
$servername = "localhost";  // Replace with your database server
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "interest";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Information</title>
    <link rel="stylesheet" href="./css/all_borrowers_details.css">
    <style>
       


        
    </style>
</head>
<body>
    <h2>Borrower Information</h2>
    <!-- Download as PDF button -->
    <form method="post" action="./downlode_borrowers_details.php">
        <button type="submit">Download as PDF</button>
    </form>
    <table>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Due Date</th>
            <th>Rental</th>
            <th>Loan Amount</th>
            <th>Agree Value</th>
            <th>No rental</th>
            <th>Total Payment</th>
            <th>Arrears</th>
        </tr>
        <!-- PHP will insert rows here -->
        <?php
             $row_number =1;
            // Assuming connection to the database is already established
            $result = mysqli_query($conn, "SELECT name, amount, total_arrears,due_date,rental,agree_value,total_payments,no_rental, status FROM borrowers ORDER BY id ASC");

            while ($row = mysqli_fetch_assoc($result)) {
                // Determine the status class based on the database values
                $statusClass = ($row['status'] == 'yes') ? 'finished' : (($row['status'] == 'no') ? 'not-finished' : 'in-progress');
                
                // Output table rows
                echo "<tr class='{$statusClass}'>";
                echo "<td>{$row_number}</td>";
                $row_number++;
                echo "<td class='abc'>{$row['name']}</td>";
                echo "<td>{$row['due_date']}</td>";
                echo "<td>{$row['rental']}</td>";
                echo "<td>" . number_format($row['amount'], 2) . "</td>";
                echo "<td>{$row['agree_value']}</td>";
                echo "<td>{$row['no_rental']}</td>";
                echo "<td>{$row['total_payments']}</td>";
                echo "<td>{$row['total_arrears']}</td>";
                echo "</tr>";
            }
        ?>
    </table>

    
</body>
</html>
