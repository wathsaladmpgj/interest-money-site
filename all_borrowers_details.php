<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Update this with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrowers and update status
$sql_update = "SELECT id, name, due_date, total_payments, agree_value FROM borrowers";
$result_update = $conn->query($sql_update);

if ($result_update->num_rows > 0) {
    while ($row = $result_update->fetch_assoc()) {
        $id = $row['id'];
        $due_date = $row['due_date'];
        $total_payments = $row['total_payments'];
        $agree_value = $row['agree_value'];

        // Determine status
        if ($total_payments >= $agree_value) {
            $status = 'yes'; // Settled
        } elseif ($due_date < date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'no'; // Arrears
        } elseif ($due_date >= date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'con'; // Currently paying
        }

        // Update status in the database
        $update_sql = "UPDATE borrowers SET status = '$status' WHERE id = $id";
        $conn->query($update_sql);
    }
}

// Fetch borrowers and update status
$sql = "SELECT id, name, lone_date, due_date, rental, amount, agree_value, no_rental, days_passed, total_payments,no_pay, total_arrears, status FROM borrowers";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers Details</title>
    <link rel="stylesheet" href="./css/all_borrowers_details.css">
</head>
<body>
    <h2>Borrowers Details</h2>
    <form method="post" action="./downlode_borrowers_details.php">
        <button type="submit">Download as PDF</button>
    </form>

    <?php
        // Query to fetch the counts
        $sql_details = "SELECT status, COUNT(*) AS count FROM borrowers GROUP BY status";
        $result_details = $conn->query($sql_details);

        // Display the results
        if ($result_details->num_rows > 0) {
            $total_borrowers = 0; // Initialize total borrowers counter

            echo "<table border='1' style='width: 30%; text-align: center;'>";
            echo "<tr><th>Status</th><th>Count</th></tr>";
            while ($row = $result_details->fetch_assoc()) {
                // Map status to descriptive labels and assign background colors
                $status_label = '';
                $row_color = ''; // Initialize the color

                if ($row['status'] == 'yes') {
                    $status_label = 'Settled Borrowers';
                    $row_color = 'background-color: yellow;'; // Yellow for settled
                } elseif ($row['status'] == 'no') {
                    $status_label = 'Arrears Borrowers';
                    $row_color = 'background-color: rgb(245, 181, 181);'; // Red for arrears
                } elseif ($row['status'] == 'con') {
                    $status_label = 'Active Borrowers';
                    $row_color = 'background-color: white;'; // White for current
                }

                // Add to total borrowers count
                $total_borrowers += $row['count'];

                // Display the row with inline background color
                echo "<tr style='$row_color'><td style='text-align: center;'>" . $status_label . "</td><td style='text-align: center;'>" . $row['count'] . "</td></tr>";
            }

            // Add the total row with a bold style
            echo "<tr style='font-weight: bold; background-color: lightgray;'><td style='text-align: center;'>Total Borrowers</td><td style='text-align: center;'>" . $total_borrowers . "</td></tr>";
            echo "</table>";
        } else {
            echo "No data found.";
        }
    ?>



    <table>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Loan Date</th>
            <th>Due Date</th>
            <th>Rental</th>
            <th>Loan Amount</th>
            <th>Agree Value</th>
            <th>No Rent</th>
            <th>Due Rent</th>
            <th>Arrears Rent</th>
            <th>Total Payment</th>
            <th>Arrears</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            $row_number = 1;
            while ($row = $result->fetch_assoc()) {
                $due_rent =  $row['days_passed'];
                $arrears_rent =$row['days_passed'] - $row['no_pay'];

                // Determine status class for the row
                $statusClass = ($row['status'] == 'yes') ? 'finished' :
                               (($row['status'] == 'no') ? 'not-finished' : 'in-progress');

                echo "<tr class='{$statusClass}'>";
                echo "<td>{$row_number}</td>";
                echo "<td><a href='details.php?id=" . $row['id'] . "'>{$row['name']}</a></td>";
                echo "<td>{$row['lone_date']}</td>";
                echo "<td>{$row['due_date']}</td>";
                echo "<td>{$row['rental']}</td>";
                echo "<td>" . number_format($row['amount'], 2) . "</td>";
                echo "<td>{$row['agree_value']}</td>";
                echo "<td>{$row['no_rental']}</td>";
                echo "<td>{$due_rent}</td>";
                echo "<td>{$arrears_rent}</td>";
                echo "<td>{$row['total_payments']}</td>";
                echo "<td>{$row['total_arrears']}</td>";
                echo "</tr>";

                $row_number++;
            }
        } else {
            echo "<tr><td colspan='12'>No borrowers found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
