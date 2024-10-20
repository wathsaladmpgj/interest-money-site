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

// Initialize variables to store total values
$totalInvest = 0;
$agreeValue = 0;
$totalInterest = 0;
$dailyInterest = 0;
$allRental = 0;
$capital = 0;
$allpayed = 0;
$dyInterest = 0; // Initialize variable to accumulate daily interest

// Fetch all borrowers whose due date is today or later
$sql = "SELECT * FROM borrowers WHERE due_date >= CURDATE()";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($borrower = $result->fetch_assoc()) {
        $totalInvest += $borrower['amount']; // Sum total investment
        $agreeValue += $borrower['agree_value']; // Sum agree value
        $totalInterest += $borrower['interest']; // Sum total interest
        $dailyInterest += $borrower['interest_day']; // Sum daily interest
        $allRental += $borrower['rental']; // Sum total rental

        // Fetch payments for the current borrower
        $sqlPayments = "SELECT * FROM payments WHERE borrower_id = " . $borrower['id'];
        $resultPayments = $conn->query($sqlPayments);

        if ($resultPayments && $resultPayments->num_rows > 0) {
            while ($payment = $resultPayments->fetch_assoc()) {
                $allpayed += $payment['rental_amount']; // Sum up the total payments made

                // Calculate daily interest
                $dy_interest = $borrower['interest'] / $borrower['no_rental']; // Per-rental interest
                $capital += ($borrower['rental'] - $dy_interest); // Capital is rental minus per-rental interest

                // Check if payment is made today
                if ($payment['payment_date'] == date('Y-m-d')) {
                    // Accumulate today's capital contribution
                    $dyInterest += ($payment['rental_amount'] - $borrower['rental']+$borrower['interest_day']);
                }
            }
        }
    }
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
    <link rel="stylesheet" href="./styles.css">
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
                </ul>
            </nav>
        </div>
        
        <!-- Create chart -->
        <div class="chart-temp">
            <div class="card1">
                <div class="card1-2">
                    <h4>TOTAL INVESTMENT</h4>
                    <h3>Rs. <?php echo number_format($totalInvest, 2); ?></h3> <!-- Total investment -->
                </div>
                <div class="card1-2">
                    <h4>TOTAL AGREE VALUE</h4>
                    <h3>Rs. <?php echo number_format($agreeValue, 2); ?></h3> <!-- Agree value minus payments made -->
                </div>
                <div class="card1-2">
                    <h4>TOTAL INTEREST</h4>
                    <h3>Rs. <?php echo number_format($agreeValue-$totalInvest, 2); ?></h3> <!-- Total interest -->
                </div>
                <div class="card1-2">
                    <h4>DAILY INTEREST</h4>
                    <h3>Rs. <?php echo number_format($dyInterest, 2); ?></h3> <!-- Daily interest -->
                </div>        
            </div>
        </div>

        <!-- Dashboard details -->
        <div class="rg-temp">
            <h2>Overview</h2>
            <h3>Total Rental: Rs. <?php echo number_format($allRental, 2); ?></h3> <!-- Total rental -->
            <h3>Total Paid: Rs. <?php echo number_format($allpayed, 2); ?></h3> <!-- Total amount paid -->
            <h3>Capital: Rs. <?php echo number_format($capital, 2); ?></h3> <!-- Total accumulated capital -->
            <h3>Today's interest: Rs. <?php echo number_format($dyInterest, 2); ?></h3> <!-- Capital from today's payments -->
        </div>
    </div>
</body>
</html>
