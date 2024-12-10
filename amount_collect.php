<?php
// Database connection
$host = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "interest"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrower names
$borrowerNames = [];
if ($result = $conn->query("SELECT id, name FROM borrower_details")) {
    while ($row = $result->fetch_assoc()) {
        $borrowerNames[] = $row;
    }
    $result->free();
}

// Handle AJAX request for loan numbers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrower_id'])) {
    $borrower_id = $_POST['borrower_id'];
    $stmt = $conn->prepare("SELECT id, lone_number FROM borrowers WHERE borrower_details_id = ?");
    $stmt->bind_param('i', $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $loanNumbers = [];
    while ($row = $result->fetch_assoc()) {
        $loanNumbers[] = $row;
    }
    echo json_encode($loanNumbers);
    exit;
}

// Handle AJAX request to get borrower ID based on loan number
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];
    $stmt = $conn->prepare("SELECT id FROM borrowers WHERE id = ?");
    $stmt->bind_param('i', $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $loanData = $result->fetch_assoc();
    echo json_encode($loanData);
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Loan Selection</title>
    <link rel="stylesheet" href="./css/collectAmount.css">
    <!-- Include Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
        }
        select {
            width: 100%; /* Ensure Select2 dropdown matches the container width */
        }
    </style>
</head>
<body>
    <h1>Borrower and Loan Selection</h1>
    <form action="./amount_collect_backend.php" method="POST">
    <!-- Borrower Dropdown -->
    <label for="borrower">Select Borrower:</label>
    <select id="borrower" name="borrower">
        <option value="">-- Select Borrower --</option>
        <?php foreach ($borrowerNames as $borrower): ?>
            <option value="<?= $borrower['id'] ?>"><?= htmlspecialchars($borrower['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <!-- Loan Number Dropdown -->
    <label for="loan">Select Loan Number:</label>
    <select id="loan" name="loan">
        <option value="">-- Select Loan Number --</option>
    </select><br><br>

    <!-- Hidden Borrower ID Field -->
    <input type="hidden" id="borrower_id_display" name="borrower_id_display" value="">

    <label for="du_date">Due Date</label><br>
    <input type="date" id="du_date" name="du_date" required autocomplete="off"><br><br>

    <label for="payment_date">Payment Date</label><br>
    <input type="date" id="payment_date" name="payment_date" required autocomplete="off"><br><br>

    <label for="payment">Payment Amount</label><br>
    <input type="number" id="payment" name="payment" step="0.01" required autocomplete="off"><br><br>

    <input type="submit" value="Add Payment">
    <button type="button" onclick="window.location.href='home.php'">Back</button>
</form>


    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Apply Select2 to the dropdowns
            $('#borrower').select2({
                placeholder: '-- Select Borrower --',
                width: '100%'
            });

            $('#loan').select2({
                placeholder: '-- Select Loan Number --',
                width: '100%'
            });

            // Fetch loan numbers when a borrower is selected
            $('#borrower').on('change', function () {
                const borrowerId = $(this).val();
                $('#loan').empty().append('<option value="">-- Select Loan Number --</option>');

                if (borrowerId) {
                    $.ajax({
                        url: '', // Same file
                        method: 'POST',
                        data: { borrower_id: borrowerId },
                        success: function (response) {
                            const loans = JSON.parse(response);
                            loans.forEach(loan => {
                                $('#loan').append(
                                    `<option value="${loan.id}">${loan.lone_number}</option>`
                                );
                            });
                            $('#loan').trigger('change'); // Refresh Select2
                        }
                    });
                }
            });

           // Fetch borrower ID when a loan number is selected
$('#loan').on('change', function () {
    const loanId = $(this).val();

    if (loanId) {
        $.ajax({
            url: '', // Same file
            method: 'POST',
            data: { loan_id: loanId },
            success: function (response) {
                const loanData = JSON.parse(response);
                if (loanData && loanData.id) {
                    $('#borrower_id_display').val(loanData.id); // Display borrower ID in the hidden field
                } else {
                    $('#borrower_id_display').val('No ID found');
                }
            }
        });
    }
});

        });
    </script>
</body>
</html>

