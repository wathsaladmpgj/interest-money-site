<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $interest1 = $_POST['interest1'];
    $interest2 = $_POST['interest2'];
    $interest3 = $_POST['interest3'];
    $interest4 = $_POST['interest4'];
    $updated_month = $_POST['updated_month'];

    // Find the latest record and set its end_month to one month before the new updated_month
    $previous_record_query = "SELECT id, updated_month FROM interestrate ORDER BY updated_month DESC LIMIT 1";
    $result = $conn->query($previous_record_query);

    if ($result->num_rows > 0) {
        $previous_record = $result->fetch_assoc();
        $previous_id = $previous_record['id'];
        $previous_updated_month = $previous_record['updated_month'];

        // Calculate end_month as one month before updated_month
        $end_month_date = DateTime::createFromFormat('Y/m', $updated_month);
        $end_month_date->modify('-1 month');
        $end_month = $end_month_date->format('Y/m');

        // Update the previous record's end_month
        $update_end_month_query = "UPDATE interestrate SET end_month = '$end_month' WHERE id = $previous_id";
        $conn->query($update_end_month_query);
    }

    // Insert the new interest rate record
    $insert_query = "INSERT INTO interestrate (interest1, interest2, interest3, interest4, updated_month) 
                     VALUES ($interest1, $interest2, $interest3, $interest4, '$updated_month')";

    if ($conn->query($insert_query)) {
        echo "New interest rates added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch all records for display
$records_query = "SELECT * FROM interestrate ORDER BY updated_month ASC";
$records_result = $conn->query($records_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest Rate Management</title>
</head>
<body>
    <h2>Add Interest Rate</h2>
    <form method="POST">
        <label for="interest1">Interest 1 (%):</label>
        <input type="number" name="interest1" id="interest1" step="0.01" required><br><br>

        <label for="interest2">Interest 2 (%):</label>
        <input type="number" name="interest2" id="interest2" step="0.01" required><br><br>

        <label for="interest3">Interest 3 (%):</label>
        <input type="number" name="interest3" id="interest3" step="0.01" required><br><br>

        <label for="interest4">Interest 4 (%):</label>
        <input type="number" name="interest4" id="interest4" step="0.01" required><br><br>

        <label for="updated_month">Effective Month (YYYY/MM):</label>
        <input type="text" name="updated_month" id="updated_month" placeholder="2024/01" required><br><br>

        <button type="submit">Add Interest Rate</button>
    </form>

    <h2>Interest Rates</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Interest 1</th>
            <th>Interest 2</th>
            <th>Interest 3</th>
            <th>Interest 4</th>
            <th>Updated Month</th>
            <th>End Month</th>
        </tr>
        <?php if ($records_result->num_rows > 0): ?>
            <?php while ($row = $records_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['interest1']) ?>%</td>
                    <td><?= htmlspecialchars($row['interest2']) ?>%</td>
                    <td><?= htmlspecialchars($row['interest3']) ?>%</td>
                    <td><?= htmlspecialchars($row['interest4']) ?>%</td>
                    <td><?= htmlspecialchars($row['updated_month']) ?></td>
                    <td><?= htmlspecialchars($row['end_month'] ?? 'N/A') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <h2>Interest Calculations</h2>
    <table border="1">
        <tr>
            <th>Month</th>
            <th>Interest_received</th>
            <th>Interest 1</th>
            <th>Interest 2</th>
            <th>Interest 3</th>
            <th>Interest 4</th>
        </tr>
        <?php
        $calculation_query = "SELECT * FROM monthly_details ORDER BY month ASC";
        $calculation_result = $conn->query($calculation_query);

        if ($calculation_result->num_rows > 0) {
            while ($calc_row = $calculation_result->fetch_assoc()) {
                $month = $calc_row['month'];

                // Extract year and month name from the 'month' column
        list($year, $monthName) = explode('/', $month);  // Split into year and month name
        
        // Convert month name (e.g., 'September') to numeric month (e.g., '09')
        $monthNumber = date('m', strtotime($monthName)); // 'September' becomes '09'
        
        // Format the month as 'yyyy/mm' (e.g., '2024/09')
        $formattedMonth = $year . '/' . $monthNumber;
                // Fetch applicable interest rates for the month
                $rate_query = "SELECT * 
                       FROM interestrate 
                       WHERE updated_month <= '$formattedMonth' 
                       AND (end_month IS NULL OR end_month >= '$formattedMonth') 
                       ORDER BY updated_month DESC 
                       LIMIT 1";
        $rate_result = $conn->query($rate_query);


        if ($rate_result && $rate_result->num_rows > 0) {
            $rates = $rate_result->fetch_assoc();
            
            // Display fetched interest rates (for demonstration)
            echo "<tr>";
            echo "<td>" . htmlspecialchars($month) . "</td>";
            echo "<td>" . htmlspecialchars( $calc_row['interest_received']) . "</td>";
            echo "<td>" . ($calc_row['interest_received'] * ($rates['interest1'] / 100)) . "</td>";
            echo "<td>" . ($calc_row['interest_received'] * ($rates['interest2'] / 100)) . "</td>";
            echo "<td>" . ($calc_row['interest_received'] * ($rates['interest3'] / 100)) . "</td>";
            echo "<td>" . ($calc_row['interest_received'] * ($rates['interest4'] / 100)) . "</td>";
            echo "</tr>";
        } else {
            echo "No interest rates found for $formattedMonth.<br><br>";
        }


                
            }
        } else {
            echo "<tr><td colspan='5'>No calculation data found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
