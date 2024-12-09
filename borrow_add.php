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

    <!-- Include the Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="add_details">
        <!-- Form to add new borrower -->
        <form action="./borrow_add1.php" method="post">
            <h1>Add New Borrower</h1>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="nic">Enter NIC Number:</label>
                <input type="text" id="nic" name="nic" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address">
            </div>

            <input type="submit" value="Submit">
        </form>

        <!-- Form to add loan details -->
        <form action="./add_borrower1.php" method="post">
            <h1>Add Loan Details</h1>

            <!-- Dropdown to select borrower name -->
            <div class="form-group">
                <label for="borrower_name">Select Borrower:</label>
                <select id="borrower_id" name="borrower_id" class="form-control" style="width: 100%;" required>
                    <option value="">-- Select Borrower --</option>
                    <?php
                    // Populate the dropdown with borrower names
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No Borrowers Found</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Include the Select2 JavaScript -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <script>
                // Initialize Select2 on the borrower dropdown
                $(document).ready(function() {
                    $('#borrower_id').select2({
                        placeholder: '-- Select Borrower --',
                        allowClear: true
                    });
                });
            </script>

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
    </div>
</body>
</html>
