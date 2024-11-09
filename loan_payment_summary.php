<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "interest";

// Create a connection to the MySQL database
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select each month, the previous month's capital_received, and the sum of amounts for the month
$sql = "SELECT 
        curr.month AS current_month,
        IFNULL(prev.capital_received, 0) AS previous_month_capital_received,
        (SELECT IFNULL(SUM(b.amount), 0) 
         FROM borrowers AS b 
         WHERE DATE_FORMAT(STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d'), '%Y-%m') = DATE_FORMAT(b.lone_date, '%Y-%m')
        ) AS total_amount_for_month
    FROM 
        monthly_details AS curr
    LEFT JOIN 
        monthly_details AS prev 
    ON 
        DATE_FORMAT(STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d') - INTERVAL 1 MONTH, '%Y/%M') = prev.month
    ORDER BY 
        STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d') ASC;
";

// Execute the query
$result = $conn->query($sql);
if (!$result) {
    die("Error in query: " . $conn->error);
}

// Initialize the total stocks variable and previous stock variable
$total_stocks = 0;
$previous_stock = 0; // Store previous month's stock

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Payment Summary</title>
    <link rel="stylesheet" href="./css/loan_payment_summary.css">
</head>
<body>

<h2>Loan Payment Summary</h2>

<!-- Table container -->
<div class="table-container">
    <table>
        <tr>
            <th>Month</th>
            <th>Capital Saving</th>
            <th>New Saving</th>
            <th>New Loan</th>
            <th>Stock Increase (%)</th>
            <th>Stocks</th>
        </tr>

        <?php
        // Loop through each row from the result
        while ($row = $result->fetch_assoc()) {
            // Previous month's capital received
            $previous_capital_received = (float)$row['previous_month_capital_received'];
            
            // Current month's total loan amount
            $new_loan = (float)$row['total_amount_for_month'];

            // Capital saving assignment
            $capital_saving = ($new_loan >= $previous_capital_received) ? $previous_capital_received : $new_loan;

            // Calculate new saving and stock increase percentage
            $new_saving = max(0, $new_loan - $capital_saving);
            //$stock_increes = ($new_loan > 0) ? ($new_saving / $new_loan) * 100 : 0;
            
            // Update total stocks based on new saving and previous capital received
            $total_stocks += ($new_saving > 0) ? $new_saving : -$previous_capital_received;

            $stock_increes =($new_loan > 0) ? ($total_stocks-$previous_stock)/$new_loan*100:0;
            
            // Display the row
            ?>
            <tr>
                <td><?php echo $row['current_month']; ?></td>
                <td><?php echo number_format($capital_saving, 2); ?></td>
                <td><?php echo number_format($new_saving, 2); ?></td>
                <td><?php echo number_format($new_loan, 2); ?></td>
                <td><?php echo number_format($stock_increes, 2); ?>%</td>
                <td><?php echo number_format($total_stocks, 2); ?></td>
            </tr>
            
            <?php
            // Update previous_stock with the current month's total_stocks for the next iteration
            $previous_stock = $total_stocks;
        }
        ?>

    </table>
</div>

<!-- JavaScript for auto-reloading the page every 30 seconds -->
<script>
    setTimeout(function() {
        location.reload();
    }, 30000);  // Refresh every 30 seconds
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
