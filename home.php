<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
//-----------------------------------------------------------------------------------------------------//
function fetch_total_payments($conn, $borrower_id) {
    $sl = "SELECT SUM(rental_amount) AS total_payment FROM payments WHERE borrower_id = ?";
    $stmt = $conn->prepare($sl);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $total_payment = $data['total_payment'] ?? 0;
    $stmt->close();
    return $total_payment;
}

date_default_timezone_set('Asia/Colombo'); // Set time zone

$borrowers_sql = "SELECT * FROM borrowers";
$borrowers_result = $conn->query($borrowers_sql);

if ($borrowers_result->num_rows > 0) {
    // Fetch total rental amounts up to yesterday for each borrower
    $payments_sql = "SELECT borrower_id, SUM(rental_amount) AS total_rental_paid
                     FROM payments WHERE du_date <= CURDATE() - INTERVAL 1 DAY GROUP BY borrower_id";
    $payments_result = $conn->query($payments_sql);
    $total_rental_paid_by_borrower = [];

    while ($row = $payments_result->fetch_assoc()) {
        $total_rental_paid_by_borrower[$row['borrower_id']] = $row['total_rental_paid'];
    }

    while ($borrower = $borrowers_result->fetch_assoc()) {
        $borrower_id = $borrower['id'];
        $bar_rent = $borrower['rental'];
        $total_py = fetch_total_payments($conn, $borrower_id);
        
        // Use total rental paid from the calculated array or default to 0
        $total_rental_paid = $total_rental_paid_by_borrower[$borrower_id] ?? 0;

        $loan_date = new DateTime($borrower['lone_date']); 
        //$loan_date->modify('+1 day'); 
        $due_date = new DateTime($borrower['due_date']);
        $today = new DateTime();
        $yesterday = clone $today;
        //$yesterday->modify('-1 day');
        $today->modify('+1 day');

        // Calculate days passed based on due date
        if ($due_date >= $today) {
            $end_date = clone $today;
            $days_passed = $loan_date->diff($today)->days;
        } elseif ($due_date >= $yesterday) {
            $end_date = $due_date;
            $days_passed = $loan_date->diff($end_date)->days; 
        } else {
            $due_date->modify('+1 day');
            $days_passed = $loan_date->diff($due_date)->days; 
        }

        // Expected total payment by today
        $expected_payment_by_today = $days_passed * $bar_rent;

        // Calculate arrears
        $arrears = max($expected_payment_by_today - $total_rental_paid, 0);
        $total_arrears = round($arrears, 2);

        // Update the arrears in the database
        $update_sql = "UPDATE borrowers SET total_arrears = ?, total_payments = ?, days_passed = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("dddi", $total_arrears, $total_py, $days_passed, $borrower_id);

        if (!$update_stmt->execute()) {
            echo "Error updating arrears for borrower ID $borrower_id: " . $update_stmt->error . "<br>";
        }
        $update_stmt->close();
    }

    // Run the rental query to update no_pay
    $rental_query = "UPDATE borrowers b
        LEFT JOIN (
            SELECT borrower_id, COUNT(du_date) AS no_pay
            FROM payments
            WHERE payment_date <= CURDATE()
            GROUP BY borrower_id
        ) p ON b.id = p.borrower_id
        SET b.no_pay = IFNULL(p.no_pay, 0)";

    if (!$conn->query($rental_query)) {
        echo "Error updating no_pay in borrowers: " . $conn->error;
    }
    
} else {
    echo "No borrowers found.";
}



//-----------------------------------------------------------------------------------------------------//

date_default_timezone_set('Asia/Colombo');// Set timezone

//update total_arrears cououm on borrowers table
$sl = "SELECT * FROM borrowers WHERE id=?";


// Initialize variables to store total values
$totalInvest = 0;
$agreeValue = 0;
$totalInterest = 0;
$dailyInterest = 0;
$allRental = 0;
$capital = 0;
$allpayed = 0;
$dyInterest = 0;
$allInvest = 0; // Initialize total investment
$totalAgreValu =0;
$total_arrears=0;
$old_arrears =0;
$all_interest=0;
$day_interest=0;
$dy_Interest=0;
// Fetch all borrowers from the database
$sql_borrowers = "SELECT * FROM borrowers";
$result_borrowers = $conn->query($sql_borrowers);


// Check if there are any results
if ($result_borrowers && $result_borrowers->num_rows > 0) {
    while ($borrower = $result_borrowers->fetch_assoc()) {

        $id = $borrower['id'];
        $due_date = $borrower['due_date'];
        $total_payments = $borrower['total_payments'];
        $agree_value = $borrower['agree_value'];
        if ($total_payments == $agree_value) {
            $status = 'yes'; // Settled
        } elseif ($due_date < date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'no'; // Arrears
        } elseif ($due_date >= date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'con'; // Currently paying
        }
        $update_sql = "UPDATE borrowers SET status = '$status' WHERE id = $id";
        $conn->query($update_sql);

        // Compare due date with today's date
        $loan_date = new DateTime($borrower['lone_date']);
        $due_date = new DateTime($borrower['due_date']);
        $today = new DateTime();
        $yesterday = clone $today;
        $yesterday->modify('-1 day');

        $allInvest += $borrower['amount'];
        $totalAgreValu += $borrower['agree_value'];

        //if ($due_date >= $today) {
        $totalInvest += $borrower['amount'];
        $agreeValue += $borrower['agree_value'];
        $totalInterest += $borrower['interest'];
        $dailyInterest += $borrower['interest_day'];
        $allRental += $borrower['rental'];

        // Fetch payments for the current borrower
        $sqlPayments = "SELECT * FROM payments WHERE borrower_id = " . $borrower['id'];
        $resultPayments = $conn->query($sqlPayments);

        if ($resultPayments && $resultPayments->num_rows > 0) {
            while ($payment = $resultPayments->fetch_assoc()) {
                $allpayed += $payment['rental_amount'];
                $dy_interest = $borrower['interest_day'] ;
                $payment_day = $payment['payment_date'];
                    

                // Check if payment is made today
                    
                if($dy_interest<=$payment['rental_amount']){
                    $dyInterest += $dy_interest;
                }
                else{
                    $dyInterest += $payment['rental_amount'];
                }
                /*($payment['rental_amount'] - $borrower['rental'] + $borrower['interest_day'])*/
                    
                if($payment_day == $today->format('Y-m-d')){
                    if($dy_interest<=$payment['rental_amount']){
                        $dy_Interest += $dy_interest;
                    }
                    else{
                        $dy_Interest += $payment['rental_amount'];
                    }
                }       
            }
        }
        $total_arrears += $borrower['total_arrears'];   
    }
}
$sql_payment = "SELECT * FROM payments";
$result_payment = $conn->query($sql_payment);
$all_paid = 0;
while($pay = $result_payment->fetch_assoc()){
    $all_paid += $pay['rental_amount']; 
}

$sql_customer = "SELECT COUNT(*) AS total_customers FROM borrowers";
$result_customer = $conn->query($sql_customer);
if ($result_customer->num_rows > 0) {
    $row = $result_customer->fetch_assoc();
    $total_customer=$row['total_customers'];
} else{
    $total_customer =0;
}

$sql_borrowers_details = "SELECT COUNT(*) AS total_loan FROM borrower_details";
$result_borrowers_details = $conn->query($sql_borrowers_details);
if ($result_borrowers_details->num_rows > 0) {
    $rows= $result_borrowers_details->fetch_assoc();
    $total_loan=$rows['total_loan'];
} else{
    $total_loan =0;
}

$sql_employee = "SELECT * FROM employee_payment_details";
$result_employee = $conn->  query($sql_employee);
$all_payed_salary =0;
$all_payed_allowance =0;
$all_payed_privision =0;
$all_payed_profit =0;
while($emp = $result_employee->fetch_assoc()){
    $all_payed_salary += $emp['salary'];
    $all_payed_allowance += $emp['allownce'];
    $all_payed_privision += $emp['privision'];
}

$sql_employee_details = "SELECT * FROM monthly_details";
$result_monthly_emp_details = $conn-> query($sql_employee_details);
$all_allowance=0;
$all_salary=0;
$all_privision=0;
$all_payed_profit=0;
while($emp_details = $result_monthly_emp_details->fetch_assoc()){
    $all_allowance += $emp_details['interest2']; 
    $all_salary += $emp_details['interest1']; 
    $all_privision += $emp_details['interest3']; 
    $all_payed_profit += $emp_details['interest4'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header1">
        <h2>INTEREST MONEY SITE</h2>
    </div>
    <div class="temp">
        <div class="lf-temp">
            <nav>
                <ul>
                    <li><a href="./borrow_add.php">Add Borrower</a></li>
                    <li><a href="./collect_amount.php">Collect Amount</a></li>
                    <li><a href="./all_borrowers_details.php">Borrowers Details</a></li>
                    <li><a href="./todaycollection.php">Today's Collection</a></li>
                    <li><a href="./monthly_details.php">Monthly Details</a></li>
                    <li><a href="./loan_payment_summary.php">Summary</a></li>
                    <li><a href="./employee.php">Employee Details</a></li>
                </ul>
            </nav>
        </div>

        <!-- Details-->
        <div class="chart-temp">
            <div class="card1">
                <div class="card1-2">
                    <h4>CURRENT STOCK</h4>
                    <h3>Rs. <?php echo number_format($allInvest-($all_paid-$dyInterest)+($totalAgreValu-$allInvest) - ($dyInterest), 2); ?></h3> 
                </div>
                <div class="card1-2">
                    <h4>FUTURE CAPITAL</h4>
                    <h3>Rs. <?php echo number_format($allInvest-($all_paid-$dyInterest), 2); ?></h3> 
                </div>
                <div class="card1-2">
                    <h4>FUTURE INTEREST</h4>
                    <h3>Rs. <?php echo number_format(($totalAgreValu-$allInvest) - ($dyInterest), 2); ?></h3> 
                </div>
                <div class="card1-2">
                    <h4>TOTAL ARIARS</h4>
                    <h3>Rs. <?php echo number_format($total_arrears, 2); ?></h3> 
                </div>    
            </div>
            
            <!--Create chart-->

            <div style="width: 75%; margin:auto ;">
                <h2 style="text-align: center; font-family: Arial, sans-serif;">Capital Saving, New Loan, and Stocks Over Time</h2>
                <canvas id="myChart"></canvas>
            </div>

            <script>
                async function fetchMonthlyDetails() {
                    const response = await fetch('getMonthlyDetails.php'); 
                    const data = await response.json();
                    return data;
                }

                // Initialize the chart with empty data
                const ctx = document.getElementById('myChart').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [], // X-axis labels will be months
                        datasets: [
                            {
                                label: 'Capital Saving',
                                data: [],
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                                fill: false,
                                tension: 0,  // Set tension to 0 for straight lines
                                pointRadius: 5,
                                pointBackgroundColor: 'blue',
                                pointBorderColor: 'black',
                                pointStyle: 'circle',
                                borderWidth: 2,
                            },
                            {
                                label: 'New Loan',
                                data: [],
                                borderColor: 'magenta',
                                backgroundColor: 'rgba(255, 0, 255, 0.1)',
                                fill: false,
                                tension: 0,  // Set tension to 0 for straight lines
                                pointRadius: 5,
                                pointBackgroundColor: 'magenta',
                                pointBorderColor: 'black',
                                pointStyle: 'circle',
                                borderWidth: 2,
                            },
                            {
                                label: 'Stocks',
                                data: [],
                                borderColor: 'yellow',
                                backgroundColor: 'rgba(255, 255, 0, 0.1)',
                                fill: false,
                                tension: 0,  // Set tension to 0 for straight lines
                                pointRadius: 5,
                                pointBackgroundColor: 'yellow',
                                pointBorderColor: 'black',
                                pointStyle: 'circle',
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('en-US', { style: 'currency', currency: 'LKR' });
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw.toLocaleString('en-US', { style: 'currency', currency: 'LKR' });
                                    }   
                                }
                            }
                        }
                    }
                });

                // Function to update the chart with real data
                async function updateChart() {
                    const data = await fetchMonthlyDetails();

                    myChart.data.labels = data.map(item => item.month); // Set months on X-axis
                    myChart.data.datasets[0].data = data.map(item => item.capital_saving); // Capital Saving data
                    myChart.data.datasets[1].data = data.map(item => item.new_loan);       // New Loan data
                    myChart.data.datasets[2].data = data.map(item => item.total_stocks);   // Stocks data

                    myChart.update();
                }
                updateChart();
            </script>


            <!--collection and income chart-->
            <h2 style="text-align: center; font-family: Arial, sans-serif;">Collection,Income</h2>
            <div style="width: 75%; margin: auto;">
                <canvas id="minChart"></canvas>
            </div>

            <script>
                async function fetchMonthlyDetail() {
                    const response = await fetch('collection.php'); // PHP endpoint
                    const data = await response.json();
                    return data;
                }

        
                const ctxx = document.getElementById('minChart').getContext('2d');
                const minChart = new Chart(ctxx, {
                    type: 'line',
                    data: {
                        labels: [], // X-axis labels will be months
                        datasets: [
                            {
                            label: 'Collection',
                            data: [],
                            borderColor: 'blue',
                            backgroundColor: 'rgba(0, 0, 255, 0.1)',
                            fill: false,
                            tension: 0,
                            pointRadius: 5,
                            pointBackgroundColor: 'blue',
                            pointBorderColor: 'black',
                            pointStyle: 'circle',
                            borderWidth: 2,
                            },
                            {
                            label: 'Income',
                            data: [],
                            borderColor: 'magenta',
                            backgroundColor: 'rgba(0, 0, 255, 0.1)',
                            fill: false,
                            tension: 0,
                            pointRadius: 5,
                            pointBackgroundColor: 'magenta',
                            pointBorderColor: 'black',
                            pointStyle: 'circle',
                            borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('en-US', { style: 'currency', currency: 'LKR' });
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw.toLocaleString('en-US', { style: 'currency', currency: 'LKR' });
                                    }
                                }
                            }
                        }
                    }
                });

                async function updateChart(){
                    const data = await fetchMonthlyDetail();
                    minChart.data.labels = data.map(item => item.month).reverse(); // Set months on X-axis
                    minChart.data.datasets[0].data = data.map(item => item.monthly_payment_sum).reverse();
                    minChart.data.datasets[1].data = data.map(item => item.interest_received).reverse();
                    minChart.update();
                }
                updateChart();
                //setInterval(updateChart, 5000);
            </script>

            <!--Percentage chart-->
            
            <h2 style="text-align: center; font-family: Arial, sans-serif;">Stock Increase Percentage</h2>
            <div style="width: 75%;height:350px; margin: auto;">
                <canvas id="chart-container"></canvas>
            </div>

            <script>
                const chartCanvas = document.getElementById('chart-container');
                let chart;

                function fetchData() {
                    fetch('data_.php')
                    .then(response => response.json())
                    .then(data => {
                        if (!chart) {
                            initChart(data);
                        } else {
                            updateChart(data);
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
                }

                function initChart(data) {
                    chart = new Chart(chartCanvas, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.month),
                            datasets: [{
                                label: 'Stock Increase (%)',
                                data: data.map(item => item.stock_increase_percentage),
                                label: 'Collection',
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                                fill: false,
                                tension: 0,
                                pointRadius: 5,
                                pointBackgroundColor: 'blue',
                                pointBorderColor: 'black',
                                pointStyle: 'circle',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Percentage'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                }

                function updateChart(data) {
                    chart.data.labels = data.map(item => item.month);
                    chart.data.datasets[0].data = data.map(item => item.stock_increase_percentage);
                    chart.update();
                }
                fetchData();
                //setInterval(fetchData, 100); // Fetch data every 5 seconds
    
            </script>
 
        </div>

        <!-- Dashboard details -->
        <div class="rg-temp">
            <h3>Number of Customer: <?php echo number_format($total_loan); ?></h3>
            <h3>Number of Loans: <?php echo number_format($total_customer); ?></h3>
            <hr>
            <h3>Total AgreeValue:&nbsp; Rs. <?php echo number_format($totalAgreValu, 2); ?></h3>
            <h3>Total Investment:&nbsp; Rs. <?php echo number_format($allInvest, 2); ?></h3>
            <h3>Total Interest: Rs. <?php echo number_format($totalAgreValu-$allInvest, 2); ?></h3> 
            <hr>
            <h3>Total Collection&nbsp;: Rs. <?php echo number_format($all_paid, 2); ?></h3>
            <h3>Collect capital: Rs. <?php echo number_format($all_paid-$dyInterest, 2); ?></h3>
            <h3>Collect Interest: Rs. <?php echo number_format($dyInterest, 2); ?></h3>
            <hr>
            <h3>All Salary:&nbsp; Rs. <?php echo number_format($all_salary, 2); ?></h3>
            <h3>All Allowance:&nbsp; Rs. <?php echo number_format($all_allowance, 2); ?></h3>
            <h3>All Privision:&nbsp; Rs. <?php echo number_format($all_privision, 2); ?></h3>
            <h3>All Profit:&nbsp; Rs. <?php echo number_format($all_payed_profit, 2); ?></h3>
            <hr>
            <h3>Payed Salary:&nbsp; Rs. <?php echo number_format($all_payed_salary, 2); ?></h3>
            <h3>Payed Allowance:&nbsp; Rs. <?php echo number_format($all_payed_allowance, 2); ?></h3>
            <h3>Payed Privision:&nbsp; Rs. <?php echo number_format($all_payed_privision, 2); ?></h3>
            <h3>Payed Profit:&nbsp; Rs. <?php echo number_format($dyInterest-$all_salary-$all_allowance-$all_privision, 2); ?></h3>
            <hr>
            <h3>FutureSalary:&nbsp; Rs. <?php echo number_format($all_salary-$all_payed_salary, 2); ?></h3>
            <h3>Future Allowance:&nbsp; Rs. <?php echo number_format($all_allowance-$all_payed_allowance, 2); ?></h3>
            <h3>Future Privision:&nbsp; Rs. <?php echo number_format($all_privision-$all_payed_privision, 2); ?></h3>
            <h3>Payed Profit:&nbsp; Rs. <?php echo number_format($dyInterest-$all_salary-$all_allowance-$all_privision, 2); ?></h3>
            <hr>
        </div>

    </div>
</body>
</html>

<?php
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");
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
            $check_query = "SELECT id FROM monthly_details WHERE month = '" . $row['month'] . "'";
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
        }
?>

<!-------------------------------------------------------------------------------------------------------------------->
<?php
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

<?php
$conn->close();
?>