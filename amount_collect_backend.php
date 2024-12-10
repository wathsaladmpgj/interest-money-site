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

// Insert payment into the payments table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $borrower_id = $_POST['borrower_id_display']; // Hidden field displaying the borrower ID
    $due_date = $_POST['du_date']; // Due date
    $payment_date = $_POST['payment_date']; // Payment date
    $payment_amount = $_POST['payment']; // Payment amount
        $stmt = $conn->prepare("INSERT INTO payments (borrower_id, du_date, payment_date, rental_amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('issd', $borrower_id, $due_date, $payment_date, $payment_amount);

        if ($stmt->execute()) {
            // Function to fetch total payments for a borrower
            
    
            echo "<script>alert('Successfully updated!'); 
                window.location.href = 'amount_collect.php';
                </script>";
            exit();
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }

        // Close the statement
        $stmt->close();
    
}

// Close the database connection
$conn->close();
?>
