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

$borrower_id = $_GET['id'];

// Fetch borrower details
$sql = "SELECT * FROM borrowers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $borrower_id);
$stmt->execute();
$result = $stmt->get_result();
$borrower = $result->fetch_assoc();

// Get loan date and due date
$loan_date = new DateTime($borrower['lone_date']);
$due_date = new DateTime($borrower['due_date']);
$today = new DateTime();  // Get today's date

// Calculate the interval between the loan date and due date
$interval = $loan_date->diff($due_date);
$days_difference = $interval->days;  // Number of days between loan date and due date

// Create an array of days starting from the day after the loan date
$calendar_dates = array();
for ($i = 1; $i <= $days_difference; $i++) {
    $loan_date_clone = clone $loan_date;  // Clone the loan date to avoid modifying the original
    $loan_date_clone->modify("+$i day");  // Add days to the loan date
    $calendar_dates[] = $loan_date_clone;  // Store each date as a DateTime object in the array
}

// Sample function to check if payment was made for a specific date
function payment_made($borrower_id, $date) {
    // Here you can check against a payments table in your database for actual payment data
    // For demonstration, we will assume random payments for some dates
    // You should replace this with a real database query.
    $random_paid_days = ['2024-09-18', '2024-09-20']; // Example paid dates (replace with real data)
    return in_array($date->format('Y-m-d'), $random_paid_days);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .paid {
            background-color: yellow;
        }
        .overdue {
            background-color: red;
            color: white;
        }
        
    </style>
</head>
<body id="body">
<h1>Details for <?php echo $borrower['name']; ?></h1>
    <p><strong>Lone Amount:</strong> Rs.<?php echo $borrower['amount']; ?></p>
    <p><strong>Rental:</strong> <?php echo $borrower['rental']; ?></p>
    <p><strong>Agree Value:</strong> Rs.<?php echo $borrower['agree_value']; ?></p>
    <p><strong>Interest</strong> Rs.<?php echo $borrower['interest']; ?></p>
    <p><strong>Interest for Day</strong> Rs.<?php echo $borrower['interest_day']; ?></p>
    <p><strong>Lone Date:</strong> <?php echo $borrower['lone_date']; ?></p>
    <p><strong>NO of Rental:</strong> <?php echo $borrower['no_rental']; ?></p>
    <p><strong>Due Date:</strong> <?php echo $borrower['due_date']; ?></p>
    <p><strong>Total Payment:</strong> Rs.</p>
    <p><strong>Arrears:</strong> Rs.</p>
    <p><strong>Closing Date:</strong></p>

    <h2>Add Details</h2>
    <form action="">
        <label for="">Payment Date</label><br>
        <input type="text"><br><br>

        <label for="">Rental</label><br>
        <input type="text"><br><br>
    </form>


    <h2>Payment Calendar from Day After Loan Date to Due Date</h2>

    <table>
        <tr>
            <th>Due Date</th>
            <th>Payment Date</th>
            <th>Status</th>
            <th>Rental</th>
            <th>Balance</th>
            <th>capital</th>
            <th>Interest</th>
        </tr>
        <?php foreach ($calendar_dates as $date): ?>
        <tr>
            <td class="
                <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'paid';  // Highlight in yellow if payment was made
                } elseif ($date < $today) {
                    echo 'overdue';  // Highlight in red if the date has passed and no payment
                } else {
                    echo 'future';  // Highlight in green for future dates
                }
                ?>">
                <?php echo $date->format('Y-m-d'); ?>
            </td>

            <td class="
            <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'paid';  // Highlight in yellow if payment was made
                } elseif ($date < $today) {
                    echo 'overdue';  // Highlight in red if the date has passed and no payment
                } else {
                    echo 'future';  // Highlight in green for future dates
                }
                ?>
            ">

            </td>

            <td class="
                <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'paid';  // Highlight in yellow if payment was made
                } elseif ($date < $today) {
                    echo 'overdue';  // Highlight in red if the date has passed and no payment
                } else {
                    echo 'future';  // For future dates
                }
                ?>">
                <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'Paid';  // Mark as paid
                } elseif ($date < $today) {
                    echo 'Overdue';  // Mark as overdue
                } else {
                    echo 'Not Due Yet';  // For future dates
                }
                ?>
            </td>

            <td class="
                <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'paid';  // Highlight in yellow if payment was made
                } elseif ($date < $today) {
                    echo 'overdue';  // Highlight in red if the date has passed and no payment
                } else {
                    echo 'future';  // For future dates
                }
                ?>">

            </td>

            <td class="
                <?php
                if (payment_made($borrower_id, $date)) {
                    echo 'paid';  // Highlight in yellow if payment was made
                } elseif ($date < $today) {
                    echo 'overdue';  // Highlight in red if the date has passed and no payment
                } else {
                    echo 'future';  // For future dates
                }
                ?>">

            </td>


            <td>
            <?php echo $borrower['amount']; ?>
            </td>


            <td>

            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        // JavaScript to dynamically change the background color of the page
        const rows = document.querySelectorAll("table tr td:nth-child(2)");

        rows.forEach(row => {
            const status = row.textContent.trim(); // Get status (Paid, Overdue, Not Due Yet)
            
            if (status === 'Paid') {
                document.body.style.backgroundColor = 'yellow'; // Yellow background for paid dates
            } else if (status === 'Overdue') {
                document.body.style.backgroundColor = 'red'; // Red background for overdue dates
            } else if (status === 'Not Due Yet') {
                document.body.style.backgroundColor = 'white'; // Green background for future dates
            }
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
