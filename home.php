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

date_default_timezone_set('Asia/Colombo'); // Set timezone

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
// Fetch all borrowers from the database
$sql = "SELECT * FROM borrowers";
$result = $conn->query($sql);

// Check if there are any results
if ($result && $result->num_rows > 0) {
    while ($borrower = $result->fetch_assoc()) {
        // Compare due date with today's date
        $loan_date = new DateTime($borrower['lone_date']);
        $due_date = new DateTime($borrower['due_date']);
        $today = new DateTime();
        $yesterday = clone $today;
        $yesterday->modify('-1 day');

        $allInvest += $borrower['amount'];
        $totalAgreValu += $borrower['agree_value'];

        if ($due_date >= $today) {
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

                    // Calculate daily interest
                    $dy_interest = $borrower['interest'] / $borrower['no_rental'];
                    $capital += ($payment['rental_amount'] - $dy_interest);

                    // Check if payment is made today
                    if ($payment['payment_date'] == date('Y-m-d')) {
                        $dyInterest += ($payment['rental_amount'] - $borrower['rental'] + $borrower['interest_day']);
                    }
                }
            }
        } else {
            $old_arrears = $borrower['total_arrears'];
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
    // Fetch the result
    $row = $result_customer->fetch_assoc();
    $total_customer=$row['total_customers'];
} else {
    echo "No customers found.";
}

// Close the database connection
$conn->close();
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
                    <li><a href="./todaycollection.php">Today's Collection</a></li>
                    <li><a href="./interest_rate.php">Interest Rate</a></li>
                    <li><a href="./employee.php">Employee Details</a></li>
                </ul>
            </nav>
        </div>

        <!-- Create chart -->
        <div class="chart-temp">
            <div class="card1">
                <div class="card1-2">
                    <h4>CURRENT STOCK</h4>
                    <h3>Rs. <?php echo number_format($agreeValue-$allpayed+$old_arrears, 2); ?></h3> <!-- Agree value minus payments made -->
                </div>
                <div class="card1-2">
                    <h4>FUTURE CAPITAL</h4>
                    <h3>Rs. <?php echo number_format($totalInvest - $capital, 2); ?></h3> <!-- Total investment minus capital -->
                </div>
                <div class="card1-2">
                    <h4>FUTURE INTEREST</h4>
                    <h3>Rs. <?php echo number_format(($agreeValue - $allpayed) - ($totalInvest - $capital), 2); ?></h3> <!-- Total future interest -->
                </div>
                <div class="card1-2">
                    <h4>TOTAL ARIARS</h4>
                    <h3>Rs. <?php echo number_format($total_arrears, 2); ?></h3> <!-- Daily interest -->
                </div>
                <div class="card1-2">
                    <h4>DAILY INTEREST</h4>
                    <h3>Rs. <?php echo number_format($dy_interest, 2); ?></h3> <!-- Daily interest -->
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
            <h3>Total Payment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rs. <?php echo number_format($all_paid, 2); ?></h3>
            <hr>   
            <h3>Total Rental: Rs. <?php echo number_format($allRental, 2); ?></h3> <!-- Total rental -->
            <h3>Total Paid: Rs. <?php echo number_format($allpayed, 2); ?></h3> <!-- Total amount paid -->
            <h3>Capital: Rs. <?php echo number_format($capital, 2); ?></h3> <!-- Total accumulated capital -->
            <h3>Today's Interest: Rs. <?php echo number_format($dyInterest, 2); ?></h3> <!-- Capital from today's payments -->
        </div>
    </div>
</body>
</html>
