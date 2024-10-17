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

$borrower_id = $_GET['id']; // Borrower ID from GET request

// Fetch borrower details
$sql = "SELECT * FROM borrowers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $borrower_id);
$stmt->execute();
$result = $stmt->get_result();
$borrower = $result->fetch_assoc();
$stmt->close();

// Fetch payments details
function fetch_payments($conn, $borrower_id) {
    $sql = "SELECT payment_date, rental_amount FROM payments WHERE borrower_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[$row['payment_date']] = $row['rental_amount'];
    }
    $stmt->close();
    return $payments;
}

$payments = fetch_payments($conn, $borrower_id);

// Set the loan details
$loan_date = new DateTime($borrower['lone_date']);
$due_date = new DateTime($borrower['due_date']);
$days_difference = $loan_date->diff($due_date)->days;
$today = new DateTime();

// Generate payment schedule
$calendar_dates = [];
for ($i = 1; $i <= $days_difference; $i++) {
    $due_date_clone = clone $loan_date;
    $due_date_clone->modify("+$i day");
    $calendar_dates[] = $due_date_clone;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Payment Schedule</title>
    <link rel="stylesheet" href="./details.css">
</head>
<body id="body">
    <h1>Payment Schedule for <?php echo $borrower['name']; ?></h1>

    <table border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>NO</th>
            <th>Due Date</th>
            <th>Payment Date (Today)</th>
            <th>Status</th>
            <th>Rental</th>
            <th>Balance</th>
            <th>Capital</th>
            <th>Interest</th>
        </tr>
        <?php 
        $balance = $borrower['amount']; // Initial loan amount
        $rental = $borrower['rental'];
        $capital = 385; // Example capital value, adjust accordingly
        $interest = 115; // Example interest value, adjust accordingly
        $row_number = 1;

        foreach ($calendar_dates as $due_date): 
            $formatted_due_date = $due_date->format('Y-m-d');
            $is_today = $due_date->format('Y-m-d') == $today->format('Y-m-d');
        ?>
        <tr>
            <!-- Row Number -->
            <td><?php echo $row_number++; ?></td>

            <!-- Due Date -->
            <td><?php echo $formatted_due_date; ?></td>

            <!-- Payment Date (Today) -->
            <td><?php echo $is_today ? $today->format('Y-m-d') : ''; ?></td>

            <!-- Status -->
            <td>
                <?php
                if (isset($payments[$formatted_due_date])) {
                    echo "Paid";
                } elseif ($due_date < $today) {
                    echo "Overdue";
                } else {
                    echo "Pending";
                }
                ?>
            </td>

            <!-- Rental -->
            <td><?php echo $rental; ?></td>

            <!-- Balance -->
            <td><?php echo $balance; ?></td>

            <!-- Capital -->
            <td><?php echo $capital; ?></td>

            <!-- Interest -->
            <td><?php echo $interest; ?></td>
        </tr>
        <?php 
            $balance -= $capital; // Update balance
        endforeach; 
        ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>
