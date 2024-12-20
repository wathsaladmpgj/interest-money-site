<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);
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

    <h2>Monthly Payment Summary for All Borrowers - Yearly View</h2>
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
            -- For the current month
            WHEN YEAR(months.date) = YEAR(CURDATE()) AND MONTH(months.date) = MONTH(CURDATE()) THEN 
                GREATEST(0, DATEDIFF(LEAST(CURDATE(), b.due_date), GREATEST(DATE_FORMAT(CURDATE(), '%Y-%m-01'), DATE_ADD(b.lone_date, INTERVAL 1 DAY))) + 1) * b.rental

            -- For the loan month (partial month from loan date)
            WHEN MONTH(months.date) = MONTH(b.lone_date) AND YEAR(months.date) = YEAR(b.lone_date) THEN 
                (DATEDIFF(LAST_DAY(b.lone_date), DATE_ADD(b.lone_date, INTERVAL 1 DAY)) + 1) * b.rental

            -- For the due month (partial month until due date)
            WHEN MONTH(months.date) = MONTH(b.due_date) AND YEAR(months.date) = YEAR(b.due_date) THEN 
                DAY(b.due_date) * b.rental

            -- For full months between the loan date and due date
            ELSE 
                DAY(LAST_DAY(months.date)) * b.rental
        END
    ) AS total_monthly_payment,

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
                ) AS total_interest_to_be_received

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
            <th>No of Pay</th>
            <th>Total Amount (All Borrowers)</th>
            <th>Total capital to be Received</th>
            <th>Total Interest to be Received</th>
            <th>Monthly Payment Sum</th>
            <th>capital Received</th>
            <th>Interest Received</th>
            <th>Arrears</th>
        </tr>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Retrieve payments for the specific month to calculate Interest Received
                    $monthly_payment_Interest = 0;
        
                    // SQL to get each payment with associated borrower's interest_day and rental_amount for the selected month
                    $payment_query = "SELECT p.rental_amount, p.payment_date, b.interest_day
                                    FROM payments p
                                    JOIN borrowers b ON p.borrower_id = b.id
                                    WHERE DATE_FORMAT(p.payment_date, '%Y/%M') = '" . $row['month'] . "'";
        
                    $payment_result = $conn->query($payment_query);

                    if ($payment_result && $payment_result->num_rows > 0) {
                        while ($payment = $payment_result->fetch_assoc()) {
                            // Calculate interest based on the given logic
                            if ($payment['interest_day'] <= $payment['rental_amount']) {
                                $monthly_payment_Interest += $payment['interest_day'];
                            } else {
                                $monthly_payment_Interest += $payment['rental_amount'];
                            }
                        }
                    }

                    // Check if the month already exists in the monthly_details table
                    $check_query = "SELECT id FROM monthly_details WHERE month = '" . $row['month'] . "'" ;
                    $check_result = $conn->query($check_query);

                    $arrears = $row['total_monthly_payment'] - $row['monthly_payment_sum'];
                    $capital_received =$row['monthly_payment_sum']-$monthly_payment_Interest;
                    $total_capital =$row['total_monthly_payment']-$row['total_interest_to_be_received'];

                    // If the month already exists, update the existing row
                    if ($check_result->num_rows > 0) {
                        $update_query = "UPDATE monthly_details 
                            SET payment_count = " . $row['payment_count'] . ", 
                                total_monthly_payment = " . $row['total_monthly_payment'] . ", 
                                total_interest_to_be_received = " . $row['total_interest_to_be_received'] . ", 
                                monthly_payment_sum = " . $row['monthly_payment_sum'] . ", 
                                interest_received = " . $monthly_payment_Interest . ", 
                                arrears = " . $arrears . " ,
                                total_month_capital =".$total_capital.",
                                capital_received =".$capital_received."
                                WHERE month = '" . $row['month'] . "'";
                        $conn->query($update_query);
                    } else {
                        // If the month does not exist, insert a new row
                        $insert_query = "INSERT INTO monthly_details (month, payment_count, total_monthly_payment,             total_interest_to_be_received, monthly_payment_sum, interest_received, arrears,total_month_capital,capital_received)
                                        VALUES ('" . $row['month'] . "', " . $row['payment_count'] . ", " . $row['total_monthly_payment'] . ", " . $row['total_interest_to_be_received'] . ", " . $row['monthly_payment_sum'] . ", " . $monthly_payment_Interest . ", " . $arrears . ",".$total_capital.",".$capital_received.")";
                        $conn->query($insert_query);
                    }

                    echo "<tr>";
                    echo "<td>" . $row['month'] . "</td>";
                    echo "<td>" . $row['payment_count'] . "</td>";
                    echo "<td>" . number_format($row['total_monthly_payment'], 2) . "</td>";
                    echo "<td>" . number_format($total_capital, 2) . "</td>";
                    echo "<td>" . number_format($row['total_interest_to_be_received'], 2) . "</td>";
                    echo "<td>" . number_format($row['monthly_payment_sum'], 2) . "</td>";
                    echo "<td>" . number_format($capital_received, 2) . "</td>";
                    echo "<td>" . number_format($monthly_payment_Interest, 2) . "</td>";
                    echo "<td>" . number_format($row['total_monthly_payment'] - $row['monthly_payment_sum'], 2) . "</td>"; 
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No data available for the selected year up to the current month</td></tr>";
            }
        ?>
    </table>

    <!--Interest rate enter------------------------------------------------------------->
    <br><br><br>
    <h2 class="from_hed">INTEREST RATE</h2>
    <form action="./interest_rate1.php" method="POST">
        <label for="interest1">Interest 1 (%):</label>
        <input type="number" name="interest1" id="interest1" step="0.01" required><br><br>

        <label for="interest2">Interest 2 (%):</label>
        <input type="number" name="interest2" id="interest2" step="0.01" required><br><br>

        <label for="interest3">Interest 3 (%):</label>
        <input type="number" name="interest3" id="interest3" step="0.01" required><br><br>

        <label for="interest4">Interest 4 (%):</label>
        <input type="number" name="interest4" id="interest4" step="0.01" required><br><br>

        <label for="updated_month">Effective Month (YYYY/MM):</label>
        <input type="text" name="updated_month" id="updated_month"  required><br><br>

        <button type="submit">Add Interest Rate</button>
    </form>

    <!--Interest rate table-->
    <br>
    <h2>Interest Calculate</h2>
    <form method="post">
        <?php
            $sql_years = "SELECT DISTINCT YEAR(STR_TO_DATE(CONCAT(month, '-01'), '%Y/%M-%d')) AS year FROM monthly_details ORDER BY year DESC";
            $result_years = $conn->query($sql_years);

            while ($row_year = $result_years->fetch_assoc()) {
                $selected = isset($_POST['year']) && $_POST['year'] == $row_year['year'] ? 'selected' : '';
            }
        ?>
    </form>

    <table>
        <tr>
            <th>Month</th>
            <th>Capital</th>
            <th>Interest 1</th>
            <th>Interest 2</th>
            <th>Interest 3</th>
            <th>Interest 4</th>
            <th>Total</th>
            <th>Total Interest</th>
        </tr>

        <?php
            // Fetch the current interest rate and updated month
            $sql_interestrate = "SELECT * FROM interestrate ORDER BY updated_month DESC LIMIT 1";
            $result_interest = $conn->query($sql_interestrate);

            if (!$result_interest) {
                die("Error in interest rate query: " . $conn->error);
            }

            $interest_rate = $result_interest->fetch_assoc();
            $updated_month = isset($interest_rate['updated_month']) ? $interest_rate['updated_month'] : null;
            echo "<!-- Debug: Updated Month: $updated_month -->";

            // Fetch all monthly data
            $sql_monthly = "SELECT * 
            FROM monthly_details 
            WHERE YEAR(STR_TO_DATE(CONCAT(month, '-01'), '%Y/%M-%d')) = $selected_year 
            ORDER BY STR_TO_DATE(CONCAT(month, '-01'), '%Y/%M-%d') DESC";
            $result_monthly = $conn->query($sql_monthly);

            if (!$result_monthly) {
                die("Error in monthly details query: " . $conn->error);
            }
            if ($result_monthly->num_rows > 0) {
                while ($calc_row = $result_monthly->fetch_assoc()) {
                    $month = $calc_row['month'];
    
                    list($year, $monthName) = explode('/', $month);
                    $monthNumber = date('m', strtotime($monthName)); // 'September' becomes '09'
            
                    // Format the month as 'yyyy/mm' (e.g., '2024/09')
                    $formattedMonth = $year . '/' . $monthNumber;
                            // Fetch applicable interest rates for the month
                            $rate_query = "SELECT * 
                                FROM interestrate 
                                WHERE updated_month <= '$formattedMonth' 
                                AND (end_month IS NULL OR end_month >= '$formattedMonth') 
                                ORDER BY updated_month DESC 
                                LIMIT 1";
                            $rate_result = $conn->query($rate_query);
    
    
                    if ($rate_result && $rate_result->num_rows > 0) {
                        $rates = $rate_result->fetch_assoc();
    
                    // Ensure interest values are not null, defaulting to 0 if null
                    $interest1 = isset($calc_row['interest_received']) ? $calc_row['interest_received'] * ($rates['interest1'] / 100) : 0;
                    $interest2 = isset($calc_row['interest_received']) ? $calc_row['interest_received'] * ($rates['interest2'] / 100) : 0;
                    $interest3 = isset($calc_row['interest_received']) ? $calc_row['interest_received'] * ($rates['interest3'] / 100) : 0;
                    $interest4 = isset($calc_row['interest_received']) ? $calc_row['interest_received'] * ($rates['interest4'] / 100) : 0;
    
                    // Ensure the capital received is not null
                    $capital_received = isset($calc_row['capital_received']) ? $calc_row['capital_received'] : 0;
                    $total_interest = $interest1 + $interest2 + $interest3 + $interest4;
                    $interest_sum = $total_interest + $capital_received;

                    $update_sql = "UPDATE monthly_details SET 
                            interest1 = {$interest1}, 
                            interest2 = {$interest2}, 
                            interest3 = {$interest3}, 
                            interest4 = {$interest4} 
                            WHERE id = {$calc_row['id']}";
                    if (!$conn->query($update_sql)) {
                        die("Error updating monthly details: " . $conn->error);
                    }else {
                        $interest1 = $calc_row['interest1'];
                        $interest2 = $calc_row['interest2'];
                        $interest3 = $calc_row['interest3'];
                        $interest4 = $calc_row['interest4'];
                        
                    }
                
                // Display fetched interest rates (for demonstration)
                echo "<tr>";
                echo "<td>" . htmlspecialchars($month) . "</td>";
                echo "<td>" . htmlspecialchars( $calc_row['interest_received']) . "</td>";
                echo "<td>" . ($interest1) . "</td>";
                echo "<td>" . ($interest2) . "</td>";
                echo "<td>" . ($interest3) . "</td>";
                echo "<td>" . ($interest4) . "</td>";
                echo "<td>" . ($interest_sum) . "</td>";
                echo "<td>" . ($total_interest) . "</td>";
                
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<tr><td colspan='8'>No interest rates found for $formattedMonth.</td></tr>";
                echo "</tr>";
            }
    
    
                    
                }
            } else {
                echo "<tr><td colspan='5'>No calculation data found.</td></tr>";
            }
            ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>


