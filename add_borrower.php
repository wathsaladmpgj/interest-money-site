<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "interest");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrower names from the borrower_details table
$query = "SELECT id, name FROM borrower_details";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Borrower</title>
    <link rel="stylesheet" href="./css/add_borrow.css">
</head>
<body>
    <a href="./borrow_add.php">add</a>
    <h1>Add Loan Details</h1>

    <form action="./add_borrower1.php" method="post">
        <h1>Add Loan Details</h1>

        <!-- Dropdown to select borrower name -->
        <div class="form-group">
            <label for="borrower_name">Select Borrower:</label>
            <select id="borrower_id" name="borrower_id" required>
                <option value="">-- Select Borrower --</option>
                <?php
                // Populate the dropdown with borrower names
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No Borrowers Found</option>";
                }
                ?>
            </select>
        </div>

        <!-- Other loan details -->
        <div class="form-group">
            <label for="amount">Loan Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="interestRate">Interest Rate (%):</label>
            <input type="number" id="interestRate" name="interestRate" min="0" max="100" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="date_lent">Loan Date:</label>
            <input type="date" id="date_lent" name="date_lent" required>
        </div>

        <div class="form-group">
            <label for="norental">Number of Rentals:</label>
            <input type="number" id="norental" name="norental" required>
        </div>

        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
$conn->close();
?>
