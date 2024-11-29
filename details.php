<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Colombo'); // Change to your desired time zone

$borrower_id = $_GET['id'];

// Fetch borrower details
$sql = "SELECT * FROM borrowers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $borrower_id);
$stmt->execute();
$result = $stmt->get_result();
$borrower = $result->fetch_assoc();

$bar_rent = $borrower['rental'];
$dy_interest = $borrower['interest_day'];
$cap = $bar_rent - $dy_interest;


// Fetch payment details
function fetch_payments($conn, $borrower_id) {
    $sql = "SELECT du_date, rental_amount,payment_date FROM payments WHERE borrower_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paid_dates = [];
    $total_payment =0;
    $total_py =0;
    

    $today = new DateTime();
    $yesterday = clone $today;
    $yesterday->modify('-1 day');

    while ($row = $result->fetch_assoc()) {
        $paid_dates[] = [
            'du_date' => $row['du_date'], 
            'rental_amount' => $row['rental_amount'],
            'payment_date' => $row['payment_date']
        ];
    
        // Parse the 'du_date' as a DateTime object for comparison
        $payment_due_date = new DateTime($row['du_date']);
    
        if ($today >= $payment_due_date) {
            $total_payment += $row['rental_amount'];
        }

        // Convert payment_date string from the database to DateTime object
        
        $total_py += $row['rental_amount'];
        
       
    }
    $stmt->close();
    return [$paid_dates, $total_payment,$total_py];
}


list($paid_dates, $total_payment,$total_py) = fetch_payments($conn, $borrower_id);

// Get loan date and due date
$loan_date = new DateTime($borrower['lone_date']);
$due_date = new DateTime($borrower['due_date']);
$today = new DateTime();
$yesterday = clone $today;
$yesterday->modify('-1 day');

// Calculate the interval between the loan date and due date
$interval = $loan_date->diff($due_date);
$days_difference = $interval->days;

// Create an array of days starting from the day after the loan date
$calendar_dates = [];
for ($i = 0; $i <= $days_difference; $i++) {
    $loan_date_clone = clone $loan_date;
    $loan_date_clone->modify("+$i day");
    $calendar_dates[] = $loan_date_clone;
}

// Check if a payment was made on a specific date
function payment_made($date, $paid_dates) {
    return in_array($date->format('Y-m-d'), array_column($paid_dates, 'du_date'));
}


 // Initialize counter
 $row_number = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
    <link rel="stylesheet" href="./css/detail.css">
</head>
<body id="body">
    <h1>Details for <?php echo htmlspecialchars($borrower['name']); ?></h1>
    <p><strong>Loan Amount:</strong> Rs.<?php echo htmlspecialchars($borrower['amount']); ?></p>
    <p><strong>Rental:</strong> <?php echo htmlspecialchars($borrower['rental']); ?></p>
    <p><strong>Agreed Value:</strong> Rs.<?php echo htmlspecialchars($borrower['agree_value']); ?></p>
    <p><strong>Interest:</strong> Rs.<?php echo htmlspecialchars($borrower['interest']); ?></p>
    <p><strong>Interest for Day:</strong> Rs.<?php echo htmlspecialchars($borrower['interest_day']); ?></p>
    <p><strong>Loan Date:</strong> <?php echo htmlspecialchars($borrower['lone_date']); ?></p>
    <p><strong>No of Rentals:</strong> <?php echo htmlspecialchars($borrower['no_rental']); ?></p>
    <p><strong>Due Date:</strong> <?php echo htmlspecialchars($borrower['due_date']); ?></p>
    <p><strong>Total Payment:</strong>Rs.<?php echo number_format($total_py, 2); ?></p>
    <p><strong>Arrears:</strong> Rs.<?php echo number_format($borrower['total_arrears'], 2); ?></p>
    <p><strong>Closing Date:</strong><!-- Closing date if applicable --></p>

   

    <h2>Payment Calendar from Day After Loan Date to Due Date</h2>

    <table>
        <tr>
            <th>NO</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Payment Date</th>      
            <th>Rental</th>
            <th>Payment</th>
            <th>Balance</th>
            <th>Capital</th>
            <th>Interest</th>
            <th>Arrears</th>
            <th>Total Arrears</th>
        </tr>
        <?php foreach ($calendar_dates as $date): ?>
        <tr class="<?php
            if (payment_made($date, $paid_dates)) {
                echo 'paid';
            } elseif ($date <= $today) {
                echo 'overdue';
            } else {
                echo 'future';
            }
            ?>">
            <td><?php echo $row_number++; // Display and increment row number ?></td>

            <td><?php echo $date->format('Y-m-d'); ?></td>

            <td><?php
            if (payment_made($date, $paid_dates)) {
                echo 'Paid';
            } elseif ($date <= $today) {
                echo 'Overdue';
            } else {
                echo 'Not Due Yet';
            }
            ?></td>
            
            <!--PAYMENT Date-->
            <td>
            <?php
                $payment_date = '';
                if(payment_made($date,$paid_dates)){
                    foreach ($paid_dates as $paid) {
                        if ($paid['du_date'] == $date->format('Y-m-d')) {
                            $payment_date = $paid['payment_date'];
                            break;
                        }
                    }
                }
                elseif($date<=$today){
                    $payment_date = '-';
                }
                echo htmlspecialchars($payment_date); 
            ?>
            </td>

            <!--Rental coloum-->
            <td>
            <?php
               $rental = '';
                if (payment_made($date, $paid_dates)) {
                    foreach ($paid_dates as $paid) {
                        if ($paid['du_date'] == $date->format('Y-m-d')) {
                            $rental = $borrower['rental'];
                        }
                    }
                } 
                elseif ($date <= $today) {
                    $rental = $borrower['rental'];
                } 
                else {
                    $rental = '';
                }
                echo htmlspecialchars($rental);
            ?>
            </td>
            <!--payment coloum-->
            <td>
            <?php
               $payment = '';
                if (payment_made($date, $paid_dates)) {
                    foreach ($paid_dates as $paid) {
                        if ($paid['du_date'] == $date->format('Y-m-d')) {
                            $payment = $paid['rental_amount'];
                        }
                    }
                } 
                elseif ($date <= $today) {
                    $payment = '0.00';
                } 
                else {
                    $payment = '';
                }
                echo htmlspecialchars($payment);
            ?>
            </td>

            <!-- Balance, Capital, Interest calculations here -->
            <td>
            <?php
            // Start with the agreed value as the initial balance
            static $balance = null;

            // If this is the first due date, initialize the balance to the agreed value
                if (is_null($balance)) {
                    $balance = $borrower['agree_value'];
                }

            // Check if payment is made on the current date
                if (payment_made($date, $paid_dates)) {
                    foreach ($paid_dates as $paid) {
                        if ($paid['du_date'] == $date->format('Y-m-d')) {
                            $balance -= $paid['rental_amount'];
                        }
                    }
                } 
                elseif ($date <= $today) {
                // For past due dates where no payment has been made, the balance remains as it is
                    $balance = $balance;
                } 
                else {
                // For future dates, we don't show the balance yet
                    $balance = '';
                }

            // Display the current balance for the given date
                echo htmlspecialchars($balance);
            ?>
            </td>

            <td>
            <?php
                $capital_C = '';
                if (payment_made($date, $paid_dates)) {
                    foreach ($paid_dates as $paid) {
                        if ($paid['du_date'] == $date->format('Y-m-d')) {
                            if($dy_interest<=$paid['rental_amount']){
                                $capital_C = round($paid['rental_amount']-$dy_interest,2);
                            }else{
                                $capital_C = 0;
                            }
                            break;
                        }
                    }
                } 
                elseif ($date <= $today) {
                    $capital_C = '0.00';
                } 
                else {
                    $capital_C = '';
                }
                echo htmlspecialchars($capital_C);
            ?>
            </td>

            <!--Interest for DAY-->
            <td>
            <?php
                $interest = '';
                if (payment_made($date, $paid_dates)) {
                    foreach ($paid_dates as $paid){
                    if ($paid['du_date'] == $date->format('Y-m-d')) {
                        if($dy_interest<=$paid['rental_amount']){
                            $interest = round($dy_interest,2);
                        }else{
                            $interest = round($paid['rental_amount'],2);
                        }
                        break;
                    }
                    }
                } 
                elseif ($date <= $today) {
                    $interest = '0.00';
                } 
                else {
                    $interest = '';
                }
                echo htmlspecialchars($interest);
            ?>
            </td>

            <td>
            <?php
                $arrears_per_day = '';
                if ($date <= $today) {
                // Calculate expected payment by this date (assuming rental due daily or some interval)
                $expected_payment = $borrower['rental'];

                // Calculate the arrears for this day
                    if (payment_made($date, $paid_dates)) {
                        foreach ($paid_dates as $paid) {
                            if ($paid['du_date'] == $date->format('Y-m-d')) {
                                $arrears_per_day = $expected_payment - $paid['rental_amount'];
                                break;
                            }
                        }
                    } else {
                        $arrears_per_day = $expected_payment;
                    }
                } else {
                    // No arrears for future dates
                     $arrears_per_day = '';
                }

                echo htmlspecialchars($arrears_per_day, 2);
            ?>
            </td>

            <td>
            <?php
                static $total_arrears = 0; // Initialize static variable to keep running total of arrears

                // Calculate total arrears by summing up arrears for each overdue day
                if ($date <= $today) {
                    if ($arrears_per_day !== '') {
                        $total_arrears += $arrears_per_day;
                    }
                } else {
                    $total_arrears = ''; // No total arrears for future dates
                }

                echo htmlspecialchars($total_arrears, 2);
            ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>

<?php
$conn->close(); // Close connection at the end
?> 