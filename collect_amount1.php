<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'interest');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request for inserting payment details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $borrower_id = $_POST['showName'];
    $du_date = $_POST['du_date'];
    $payment_date = $_POST['payment_date'];
    $payment_amount = $_POST['payment'];

    // Insert payment details into payments table
    $sql = "INSERT INTO payments (borrower_id, du_date, rental_amount, payment_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $borrower_id, $du_date, $payment_amount, $payment_date);

    if ($stmt->execute()) {
        // Redirect to the previous page after successful insertion
        echo "<script>
                alert('Payment added successfully!');
                window.location.href = 'collect_amount.php'; // Redirect after alert
              </script>";
        exit(); // Ensure no further code is executed after redirection
    } else {
        // Use alert box for errors
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
    }
    // Close the statement
    $stmt->close();
}

// Function to fetch total payments for a borrower
function fetch_total_payments($conn, $borrower_id) {
    $sql = "SELECT SUM(rental_amount) AS total_payment FROM payments WHERE borrower_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    $total_payment = $data['total_payment'] ?? 0; // Default to 0 if null
    $stmt->close();
    return $total_payment;
}

// Update total arrears for all borrowers
date_default_timezone_set('Asia/Colombo'); // Set time zone
$sql = "SELECT * FROM borrowers";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($borrower = $result->fetch_assoc()) {
        $borrower_id = $borrower['id'];
        $bar_rent = $borrower['rental'];
        
        // Calculate total payments for the borrower
        $total_payment = fetch_total_payments($conn, $borrower_id);

        // Days passed since the loan date
        $loan_date = new DateTime($borrower['lone_date']);
        $today = new DateTime();
        $yesterday = clone $today;
        $yesterday->modify('-1 day');
        $days_passed = $loan_date->diff($yesterday)->days;

        // Expected total payment by today
        $expected_payment_by_today = $days_passed * $bar_rent;

        // Calculate arrears
        $arrears = max($expected_payment_by_today - $total_payment, 0);
        $total_arrears = round($arrears, 2);

        // Debugging output to verify values
        echo "Borrower ID: $borrower_id, Days Passed: $days_passed, Expected Payment: $expected_payment_by_today, Total Payment: $total_payment, Arrears: $total_arrears<br>";

        // Update the arrears in the database
        $update_sql = "UPDATE borrowers SET total_arrears = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("di", $total_arrears, $borrower_id);

        if (!$update_stmt->execute()) {
            echo "Error updating arrears for borrower ID $borrower_id: " . $update_stmt->error . "<br>";
        }
        $update_stmt->close();
    }
    echo "Arrears updated for all borrowers successfully.";
} else {
    echo "No borrowers found.";
}

// Close the database connection
$conn->close();
?>
