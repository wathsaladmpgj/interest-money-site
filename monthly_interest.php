<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected year from the form, or default to the current year
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

// Fetch distinct years for the dropdown
$year_query = "SELECT DISTINCT YEAR(lone_date) AS year FROM borrowers ORDER BY year DESC";
$year_result = $conn->query($year_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Payment Summary by Year</title>
    <link rel="stylesheet" href="./css/month_summary.css">
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: center; }
    </style>
</head>
<body>

<h2>Monthly Payment Summary for All Borrowers - Yearly View</h2>

<!-- Form to select year -->
<form method="GET" action="">
    <label for="year">Select Year:</label>
    <select name="year" id="year" onchange="this.form.submit()">
        <?php
        // Populate the year dropdown
        if ($year_result->num_rows > 0) {
            while ($year_row = $year_result->fetch_assoc()) {
                $year = $year_row['year'];
                echo "<option value='$year'" . ($selected_year == $year ? " selected" : "") . ">$year</option>";
            }
        }
        ?>
    </select>
</form>

<?php
// Define yesterday’s date for calculations in the current month
$current_date = date("Y-m-d");
$yesterday_date = date("Y-m-d", strtotime('-1 day'));
$current_month_start = date("Y-m-01");

// SQL query to calculate total monthly payment, monthly payment sum, and count payments per month
$sql = "SELECT 
    DATE_FORMAT(months.date, '%Y/%M') AS month,
    
    -- Calculate Total Monthly Payment as before
    SUM(
        CASE 
            WHEN YEAR(months.date) = YEAR(CURDATE()) AND MONTH(months.date) = MONTH(CURDATE())
                THEN 
                    GREATEST(0, DATEDIFF(LEAST(DATE_SUB(CURDATE(), INTERVAL 1 DAY), b.due_date), GREATEST(DATE_FORMAT(CURDATE(), '%Y-%m-01'), DATE_ADD(b.lone_date, INTERVAL 1 DAY))) + 1) * b.rental
            WHEN MONTH(months.date) = MONTH(b.lone_date) AND YEAR(months.date) = YEAR(b.lone_date)
                THEN (DATEDIFF(LAST_DAY(b.lone_date), DATE_ADD(b.lone_date, INTERVAL 1 DAY)) + 1) * b.rental
            WHEN MONTH(months.date) = MONTH(b.due_date) AND YEAR(months.date) = YEAR(b.due_date)
                THEN DAY(b.due_date) * b.rental
            ELSE DAY(LAST_DAY(months.date)) * b.rental
        END
    ) AS total_monthly_payment,

    -- Sum of payments made in the month
    COALESCE(p_data.monthly_payment_sum, 0) AS monthly_payment_sum,
    COALESCE(p_data.payment_count, 0) AS payment_count,

    -- Calculate Total Interest to be Received for the month
    SUM(
        CASE 
            WHEN YEAR(months.date) = YEAR(CURDATE()) AND MONTH(months.date) = MONTH(CURDATE())
                THEN GREATEST(0, DATEDIFF(LEAST(DATE_SUB(CURDATE(), INTERVAL 1 DAY), b.due_date), GREATEST(DATE_FORMAT(CURDATE(), '%Y-%m-01'), DATE_ADD(b.lone_date, INTERVAL 1 DAY))) + 1) * b.interest_day
            WHEN MONTH(months.date) = MONTH(b.lone_date) AND YEAR(months.date) = YEAR(b.lone_date)
                THEN (DATEDIFF(LAST_DAY(b.lone_date), DATE_ADD(b.lone_date, INTERVAL 1 DAY)) + 1) * b.interest_day
            WHEN MONTH(months.date) = MONTH(b.due_date) AND YEAR(months.date) = YEAR(b.due_date)
                THEN DAY(b.due_date) * b.interest_day
            ELSE DAY(LAST_DAY(months.date)) * b.interest_day
        END
    ) AS total_interest_to_be_received,

    -- Calculate Interest Received for the month
    (SUM(
        CASE 
            WHEN YEAR(months.date) = YEAR(CURDATE()) AND MONTH(months.date) = MONTH(CURDATE())
                THEN GREATEST(0, DATEDIFF(LEAST(DATE_SUB(CURDATE(), INTERVAL 1 DAY), b.due_date), GREATEST(DATE_FORMAT(CURDATE(), '%Y-%m-01'), DATE_ADD(b.lone_date, INTERVAL 1 DAY))) + 1) * b.interest_day
            WHEN MONTH(months.date) = MONTH(b.lone_date) AND YEAR(months.date) = YEAR(b.lone_date)
                THEN (DATEDIFF(LAST_DAY(b.lone_date), DATE_ADD(b.lone_date, INTERVAL 1 DAY)) + 1) * b.interest_day
            WHEN MONTH(months.date) = MONTH(b.due_date) AND YEAR(months.date) = YEAR(b.due_date)
                THEN DAY(b.due_date) * b.interest_day
            ELSE DAY(LAST_DAY(months.date)) * b.interest_day
        END
    ) - COALESCE(p_data.monthly_payment_sum, 0)) AS interest_received

FROM 
    borrowers b
CROSS JOIN (
        SELECT DATE_FORMAT(DATE_ADD('$selected_year-01-01', INTERVAL n MONTH), '%Y-%m-01') AS date
        FROM (
            SELECT @row := @row + 1 AS n
            FROM (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                  UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
                  UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11) AS x,
                 (SELECT @row := -1) AS r
        ) AS nums
    ) AS months
LEFT JOIN (
    SELECT 
        DATE_FORMAT(payment_date, '%Y/%M') AS payment_month,
        SUM(rental_amount) AS monthly_payment_sum,
        COUNT(rental_amount) AS payment_count
    FROM 
        payments
    WHERE 
        YEAR(payment_date) = $selected_year
    GROUP BY 
        DATE_FORMAT(payment_date, '%Y/%m')
) AS p_data ON DATE_FORMAT(months.date, '%Y/%M') = p_data.payment_month
WHERE 
    YEAR(months.date) = $selected_year
    AND months.date BETWEEN DATE_FORMAT(b.lone_date, '%Y-%m-01') AND DATE_FORMAT(b.due_date, '%Y-%m-01')
    AND months.date <= LAST_DAY(CURDATE())
GROUP BY month
ORDER BY months.date DESC;
";

$result = $conn->query($sql);
?>

<table>
    <tr>
        <th>Month</th>
        <th>Total Amount (All Borrowers)</th>
        <th>Monthly Payment Sum</th>
        <th>Arrears</th>
        <th>Number of Payments</th>
        <th>Total Interest to be Received</th>
        <th>Interest Received</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['month'] . "</td>";
            echo "<td>" . number_format($row['total_monthly_payment'], 2) . "</td>";
            echo "<td>" . number_format($row['monthly_payment_sum'], 2) . "</td>";
            echo "<td>" . number_format($row['total_monthly_payment']-$row['monthly_payment_sum'], 2) . "</td>";
            echo "<td>" . $row['payment_count'] . "</td>";
            echo "<td>" . number_format($row['total_interest_to_be_received'], 2) . "</td>";
            echo "<td>" . number_format($row['interest_received'], 2) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No data available for the selected year up to the current month</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
