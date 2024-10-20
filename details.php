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
$dy_interest = $borrower['interest']/$borrower['no_rental'];
$cap = $bar_rent - $dy_interest;


// Insert payment details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $du_date = $_POST['du_date'];
    $payment_am = $_POST['payment'];
    $payment_date =$_POST['payment_date'];

    // Insert into payments table
    $insert_sql = "INSERT INTO payments (borrower_id, du_date, rental_amount, payment_date) VALUES (?, ?,?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("isss", $borrower_id, $du_date, $payment_am,$payment_date);

    if ($insert_stmt->execute()) {
        echo "<script>alert('Payment added successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $insert_stmt->close();
}

// Fetch payment details
function fetch_payments($conn, $borrower_id) {
    $sql = "SELECT du_date, rental_amount,payment_date FROM payments WHERE borrower_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paid_dates = [];
    $total_payment =0;

    while ($row = $result->fetch_assoc()) {
        $paid_dates[] = [
            'du_date' => $row['du_date'], 
            'rental_amount' => $row['rental_amount'],
            'payment_date' => $row['payment_date']
        ];
        // Add rental amount to total payment
        $total_payment += $row['rental_amount'];
    }

    

    $stmt->close();
    return [$paid_dates, $total_payment];
}

list($paid_dates, $total_payment) = fetch_payments($conn, $borrower_id);

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
for ($i = 1; $i <= $days_difference; $i++) {
    $loan_date_clone = clone $loan_date;
    $loan_date_clone->modify("+$i day");
    $calendar_dates[] = $loan_date_clone;
}

// Check if a payment was made on a specific date
function payment_made($date, $paid_dates) {
    return in_array($date->format('Y-m-d'), array_column($paid_dates, 'du_date'));
}

// Calculate arrears
$expected_payment_by_today = 0;
$arrears = 0;

foreach ($calendar_dates as $date) {
    if ($date <= $yesterday) {
        $expected_payment_by_today += $borrower['rental'];
    }
}

$arrears = $expected_payment_by_today-$total_payment;
$row_number = 1; // Initialize counter

//$day_interest = $borrower['interest_day'];
//$capital = $borrower['rental'] - $borrower['interest_day'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
    
    <link rel="stylesheet" href="./detail.css">
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
    <p><strong>Total Payment:</strong>Rs.<?php echo number_format($total_payment, 2); ?></p>
    <p><strong>Arrears:</strong> Rs.<?php echo number_format($arrears, 2); ?></p>
    <p><strong>Closing Date:</strong><!-- Closing date if applicable --></p>

    <h2>Add Payment Details</h2>
    <form action="" method="post">
        <label for="du_date">Due Date</label><br>
        <input type="date" id="du_date" name="du_date" required><br><br>

        <label for="payment_date">Payment Date</label><br>
        <input type="date" id="payment_date" name="payment_date" required><br><br>

        <label for="payment">payment</label><br>
        <input type="number" id="payment" name="payment" step="0.01" required><br><br>

        <input type="submit" value="Add Payment">
    </form>

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
        </tr>
        <?php foreach ($calendar_dates as $date): ?>
        <tr class="<?php
            if (payment_made($date, $paid_dates)) {
                echo 'paid';
            } elseif ($date <= $yesterday) {
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
            } elseif ($date <= $yesterday) {
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
                elseif($date<=$yesterday){
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
                elseif ($date <= $yesterday) {
                    $rental = '0.00';
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
                elseif ($date <= $yesterday) {
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
                        // Deduct the payment amount from the balance
                            $balance -= $paid['rental_amount'];
                        }
                    }
                } 
                elseif ($date <= $yesterday) {
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
                            if($cap<=$paid['rental_amount']){
                                $capital_C = round($cap,2);
                            }else{
                                $capital_C = round($paid['rental_amount'],2);
                            }
                            break;
                        }
                    }
                } 
                elseif ($date <= $yesterday) {
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
                        if($cap<=$paid['rental_amount']){
                            $interest = round($paid['rental_amount']-$cap,2);
                        }else{
                            $interest = "0.00";
                        }
                        break;
                    }
                    }
                } 
                elseif ($date <= $yesterday) {
                    $interest = '0.00';
                } 
                else {
                    $interest = '';
                }
                echo htmlspecialchars($interest);
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