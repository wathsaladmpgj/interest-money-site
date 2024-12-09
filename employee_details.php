<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the selected year for the monthly payment summary
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Default to current year

// Fetch available years for the year dropdown
$year_sql = "SELECT DISTINCT YEAR(payment_date) AS year FROM employee_payment_details ORDER BY year DESC";
$year_result = $conn->query($year_sql);

// Fetch data for the selected year for Monthly Payment Summary
$monthly_sql = "SELECT 
                    p.payment_month,
                    SUM(p.salary) AS total_monthly_salary,
                    SUM(p.allownce) AS total_monthly_allowance,
                    SUM(p.privision) AS total_monthly_provision
                FROM 
                    employee_payment_details p
                WHERE 
                    YEAR(p.payment_date) = ?
                GROUP BY 
                    p.payment_month
                ORDER BY 
                    p.payment_month";
$stmt_monthly = $conn->prepare($monthly_sql);
$stmt_monthly->bind_param("s", $selected_year);
$stmt_monthly->execute();
$monthly_result = $stmt_monthly->get_result();

// Fetch data for the Employee Payment Summary
$employee_sql = "SELECT 
                    e.name AS employee_name, 
                    SUM(p.salary) AS total_salary, 
                    SUM(p.allownce) AS total_allowance, 
                    SUM(p.privision) AS total_provision
                FROM 
                    employee_details e
                JOIN 
                    employee_payment_details p
                ON 
                    e.id = p.employee_id
                GROUP BY 
                    e.id";
$employee_result = $conn->query($employee_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary</title>
    <link rel="stylesheet" href="./css/month_summary.css">
    <link rel="stylesheet" href="./css/enter_employee.css">
</head>
<body>
    <nav>
        <li><a href="./employee.php">UPDATE EMPLOYEE</a></li>
        <li><a href="./employee_details.php">EMPLOYEE DETAILS</a></li>
    </nav>
    <h2>Employee Payment Summary</h2>

    <!-- Employee Payment Summary Table -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Total Salary</th>
                <th>Total Allowance</th>
                <th>Total Provision</th>
                <th>Total Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($employee_result->num_rows > 0) {
                while ($row = $employee_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['employee_name'] . "</td>";
                    echo "<td>" . number_format($row['total_salary'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_allowance'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_provision'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_provision']+$row['total_allowance']+$row['total_salary'], 2) . "</td>";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br><br>
    <!-------------------------------------------------------------------------------------------->

    <h2>Monthly Payment Summary</h2>

    <!-- Year Selection Form -->
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

    <!-- Monthly Payment Summary Table -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Month</th>
                <th>Month Name</th>
                <th>Total Monthly Salary</th>
                <th>Total Monthly Allowance</th>
                <th>Total Monthly Provision</th>
                <th>Total Monthly Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($monthly_result->num_rows > 0) {
                while ($row = $monthly_result->fetch_assoc()) {
                    // Convert payment_month (YYYY-MM) to a DateTime object
                    $month = DateTime::createFromFormat('Y-m', $row['payment_month']);
                    $monthName = $month->format('F'); // Get the full month name (e.g., "January", "February")

                    echo "<tr>";
                    echo "<td>" . $row['payment_month'] . "</td>"; // Display the payment_month (e.g., 2024-01)
                    echo "<td>" . $monthName . "</td>"; // Display the full month name
                    echo "<td>" . number_format($row['total_monthly_salary'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_monthly_allowance'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_monthly_provision'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_monthly_provision']+$row['total_monthly_allowance']+$row['total_monthly_salary'], 2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No data found for the selected year</td></tr>";
            }
            ?>
        </tbody>
    </table>

        <!-------------------------------------------------------------------------->
    <br><br>
    

</body>
</html>

<?php
$stmt_monthly->close();
$conn->close();
?>
