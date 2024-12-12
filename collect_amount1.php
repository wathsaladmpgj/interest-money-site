<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'interest');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['borrower_ids'])) {
    $borrower_ids = $_POST['borrower_ids'];
    $payment_date = date('Y-m-d'); // Current date
    $du_date = date('Y-m-d');

    // Prepare the SQL for inserting into the payments table
    $insert_sql = "INSERT INTO payments (borrower_id, rental_amount, payment_date, du_date) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);

    if (!$insert_stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Loop through each selected borrower
    foreach ($borrower_ids as $borrower_id) {
        // Fetch borrower details
        $sql = "SELECT rental, due_date FROM borrowers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("i", $borrower_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $rental_amount = $row['rental'];
            $due_date = date('Y-m-d');

            // Insert payment into the payments table
            $insert_stmt->bind_param("idss", $borrower_id, $rental_amount, $payment_date, $due_date);
            if (!$insert_stmt->execute()) {
                echo "Error inserting payment for borrower ID $borrower_id: " . $insert_stmt->error;
            }
        }

        $stmt->close();
    }

    echo "<script>alert('Successfully updated!'); 
                window.location.href = 'amount_collect.php';
                </script>";
} else {
    echo "No borrowers selected!";
}

$conn->close();
?>
