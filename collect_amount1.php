<?php
$conn = new mysqli('localhost', 'root', '', 'interest');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request for inserting payment details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrower_id = $_POST['showName'];
    $du_date = $_POST['du_date'];
    $payment_date = $_POST['payment_date'];
    $payment_amount = $_POST['payment'];

    // Insert payment details into payments table
    $sql = "INSERT INTO payments (borrower_id, du_date, rental_amount, payment_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $borrower_id, $du_date, $payment_amount, $payment_date);
    
    if ($stmt->execute()) {
        // Function to fetch total payments for a borrower
        

        echo "<script>alert('Successfully updated!'); 
            window.location.href = 'collect_amount.php';
            </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

$conn->close();
?>
