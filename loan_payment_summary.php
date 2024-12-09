<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'interest';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available years from the database
$year_query = "SELECT DISTINCT YEAR(STR_TO_DATE(CONCAT(month, ' 01'), '%Y/%M %d')) AS year FROM monthly_details ORDER BY year DESC";
$year_result = $conn->query($year_query);

// Get the selected year from the dropdown or default to the current year
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Query to select each month, the previous month's capital_received, and the sum of amounts for the month
$sql = "SELECT 
    curr.month AS current_month,
    curr.capital_received AS capital_received, 
    IFNULL(prev.capital_received, 0) AS previous_month_capital_received,
    (SELECT IFNULL(SUM(b.amount), 0) 
     FROM borrowers AS b 
     WHERE DATE_FORMAT(STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d'), '%Y-%m') = DATE_FORMAT(b.lone_date, '%Y-%m')
    ) AS total_amount_for_month,
    curr.id AS monthly_details_id
FROM 
    monthly_details AS curr
LEFT JOIN 
    monthly_details AS prev 
ON 
    DATE_FORMAT(STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d') - INTERVAL 1 MONTH, '%Y/%M') = prev.month
WHERE 
    YEAR(STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d')) = ?
ORDER BY 
    STR_TO_DATE(CONCAT(curr.month, ' 01'), '%Y/%M %d') ASC";

// Prepare the statement for year filtering
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$result = $stmt->get_result();

// Initialize the total stocks variable and previous stock variable
$total_stocks = 0;
$previous_stock = 0;

// Get the current month and year
$current_month = date("Y/m");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Payment Summary</title>
    <link rel="stylesheet" href="./css/month_summary.css">
</head>
<body>

<h2>Loan Payment Summary</h2>

<form method="GET" action="">
    <label for="year">Select Year:</label>
    <select name="year" id="year" onchange="this.form.submit()">
        <?php
        if ($year_result->num_rows > 0) {
            while ($year_row = $year_result->fetch_assoc()) {
                $year = $year_row['year'];
                echo "<option value='$year'" . ($selected_year == $year ? " selected" : "") . ">$year</option>";
            }
        }
        ?>
    </select>
</form>

<div class="table-container">
    <table>
        <tr>
            <th>Month</th>
            <th>Capital Saving</th>
            <th>New Saving</th>
            <th>New Loan</th>
            <th>Stock Increase (%)</th>
            <th>Capital Outstanding</th>
        </tr>

        <?php
        while ($row = $result->fetch_assoc()) {
            $row_month_str = trim($row['current_month']);
            $row_month_date = DateTime::createFromFormat('Y/F', $row_month_str);
            if (!$row_month_date) {
                continue;
            }
            $row_month = $row_month_date->format('Y/F');

            $capital_received = (float)$row['capital_received'];
            $previous_capital_received = (float)$row['previous_month_capital_received'];
            $new_loan = (float)$row['total_amount_for_month'];

            // Capital saving is directly assigned as capital received
            $capital_saving = $capital_received;

            // Calculate new saving
            $new_saving = max(0, $new_loan - $capital_saving);

            // Calculate total stocks
            $total_stocks = $previous_stock  - $capital_saving + $new_loan;

            // Calculate stock increase percentage
            $stock_increes = ($new_loan > 0) ? ($total_stocks - $previous_stock) / $new_loan * 100 : 0;

            // Display the row
            ?>
            <tr>
                <td><?php echo $row_month; ?></td>
                <td><?php echo number_format($capital_saving, 2); ?></td>
                <td><?php echo number_format($new_saving, 2); ?></td>
                <td><?php echo number_format($new_loan, 2); ?></td>
                <td><?php echo number_format($stock_increes, 2); ?>%</td>
                <td><?php echo number_format($total_stocks, 2); ?></td>
            </tr>
            <?php

            // Insert or update data into the monthly_savings table
            $stmt_insert = $conn->prepare("INSERT INTO monthly_savings (month, capital_saving, new_saving, new_loan, stock_increase_percentage, total_stocks, monthly_details_id)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE
                                    capital_saving = VALUES(capital_saving), 
                                    new_saving = VALUES(new_saving),
                                    new_loan = VALUES(new_loan),
                                    stock_increase_percentage = VALUES(stock_increase_percentage),
                                    total_stocks = VALUES(total_stocks)");
            $stmt_insert->bind_param("ssssdds", $row['current_month'], $capital_saving, $new_saving, $new_loan, $stock_increes, $total_stocks, $row['monthly_details_id']);
            $stmt_insert->execute();
            $stmt_insert->close();

            $previous_stock = $total_stocks;
        }
        ?>

    </table>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>