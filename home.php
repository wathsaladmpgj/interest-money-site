<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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
        $loan_date->modify('+1 day'); 
        $due_date = new DateTime($borrower['due_date']);
        $today = new DateTime();
        $yesterday = clone $today;
        $yesterday->modify('-1 day');

        // Calculate days passed based on due date
        if ($due_date >= $today) {
            $end_date = clone $today;
            $days_passed = $loan_date->diff($end_date)->days; 
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
$sql = "SELECT * FROM borrowers";
$result = $conn->query($sql);


// Check if there are any results
if ($result && $result->num_rows > 0) {
    while ($borrower = $result->fetch_assoc()) {

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
        //} 
        
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

$sql_employee = "SELECT * FROM employee_details";
$result_employee = $conn->  query($sql_employee);
$all_salary =0;
$all_allowance =0;
$all_privision =0;
while($emp = $result_employee->fetch_assoc()){
    $all_salary += $emp['salary'];
    $all_allowance += $emp['allownce'];
    $all_privision += $emp['privision'];
}



// Close the database connection


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <div class="header1">
        <h2>INTEREST MONEY SITE</h2>
    </div>
    <div class="temp">
        <div class="lf-temp">
            <nav>
                <ul>
                    <li><a href="./add_borrower.php">Add Borrower</a></li>
                    <li><a href="./barrowBasic.php">Borrower List</a></li>
                    <li><a href="./collect_amount.php">Collect Amount</a></li>
                    <li><a href="./all_borrowers_details.php">Borrowers Details</a></li>
                    <li><a href="./todaycollection.php">Today's Collection</a></li>
                    <li><a href="./monthly_details.php">Monthly Details</a></li>
                    <li><a href="./employee.php">Employee Details</a></li>
                </ul>
            </nav>
        </div>

        <!-- Create chart -->
        <div class="chart-temp">
            <div class="card1">
                <div class="card1-2">
                    <h4>CURRENT STOCK</h4>
                    <h3>Rs. <?php echo number_format($allInvest-($all_paid-$dyInterest)+($totalAgreValu-$allInvest) - ($dyInterest), 2); ?></h3> <!-- Agree value minus payments made -->
                </div>
                <div class="card1-2">
                    <h4>FUTURE CAPITAL</h4>
                    <h3>Rs. <?php echo number_format($allInvest-($all_paid-$dyInterest), 2); ?></h3> <!-- Total investment minus capital -->
                </div>
                <div class="card1-2">
                    <h4>FUTURE INTEREST</h4>
                    <h3>Rs. <?php echo number_format(($totalAgreValu-$allInvest) - ($dyInterest), 2); ?></h3> <!-- Total future interest -->
                </div>
                <div class="card1-2">
                    <h4>TOTAL ARIARS</h4>
                    <h3>Rs. <?php echo number_format($total_arrears, 2); ?></h3> <!-- Daily interest -->
                </div>
                <div class="card1-2">
                    <h4>DAILY INTEREST</h4>
                    <h3>Rs. <?php echo number_format($dy_Interest, 2); ?></h3> <!-- Daily interest -->
                </div>
            </div>
        </div>

        <!-- Dashboard details -->
        <div class="rg-temp">
            <h3>Number of Customer : <?php echo number_format($total_customer); ?></h3>
            <hr>
            <h3>Total AgreeValue:&nbsp; Rs. <?php echo number_format($totalAgreValu, 2); ?></h3>
            <h3>Total Investment:&nbsp; Rs. <?php echo number_format($allInvest, 2); ?></h3>
            <h3>Total Interest: Rs. <?php echo number_format($totalAgreValu-$allInvest, 2); ?></h3> 
            <hr>
            <h3>Total Collection&nbsp;: Rs. <?php echo number_format($all_paid, 2); ?></h3>
            <h3>Collect capital: Rs. <?php echo number_format($all_paid-$dyInterest, 2); ?></h3>
            <h3>Collect Interest: Rs. <?php echo number_format($dyInterest, 2); ?></h3>
            <hr>
            <h3>Salary:&nbsp; Rs. <?php echo number_format($all_salary, 2); ?></h3>
            <h3>Allowance:&nbsp; Rs. <?php echo number_format($all_allowance, 2); ?></h3>
            <h3>Privision:&nbsp; Rs. <?php echo number_format($all_privision, 2); ?></h3>
            <h3>Profit:&nbsp; Rs. <?php echo number_format($dyInterest-$all_salary-$all_allowance-$all_privision, 2); ?></h3>
            <hr>   
        </div>
    </div>

</body>
</html>
<?php
$conn->close();
?>