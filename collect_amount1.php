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
        function fetch_total_payments($conn, $borrower_id) {
            $sl = "SELECT SUM(rental_amount) AS total_payment FROM payments WHERE borrower_id = ?";
            $stmt = $conn->prepare($sl);
            $stmt->bind_param("i", $borrower_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
    
            $total_payment = $data['total_payment'] ?? 0;
            $total_py = $data['total_payment'] ?? 0; // Set to total for total_payments
    
            $stmt->close();
            return [$total_payment, $total_py];
        }

        date_default_timezone_set('Asia/Colombo'); // Set time zone

        $sq = "SELECT * FROM borrowers";
        $result = $conn->query($sq);

        if ($result->num_rows > 0) {
            while ($borrower = $result->fetch_assoc()) {
                $borrower_id = $borrower['id'];
                $bar_rent = $borrower['rental'];

                // Calculate total payments for the borrower
                list($total_payment, $total_py) = fetch_total_payments($conn, $borrower_id);

                $loan_date = new DateTime($borrower['lone_date']); 
                $loan_date->modify('+1 day'); 
                $due_date = new DateTime($borrower['due_date']);
                $today = new DateTime();
                $yesterday = clone $today;
                $yesterday->modify('-1 day');

                // Calculate days passed based on due date
                if ($due_date >= $today) {
                    $end_date = clone $today;
                    $days_passed = $loan_date->diff($end_date)->days; 
                } elseif ($due_date >= $yesterday) {
                    $end_date = $due_date;
                    $days_passed = $loan_date->diff($end_date)->days; 
                } else {
                    $due_date->modify('+1 day');
                    $days_passed = $loan_date->diff($due_date)->days; 
                }

                // Expected total payment by today
                $expected_payment_by_today = $days_passed * $bar_rent;

                // Calculate arrears
                $arrears = max($expected_payment_by_today - $total_payment, 0);
                $total_arrears = round($arrears, 2);

                // Update the arrears in the database
                $update_sql = "UPDATE borrowers SET total_arrears = ?, total_payments = ?, days_passed = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("dddi", $total_arrears, $total_py, $days_passed, $borrower_id);

                if (!$update_stmt->execute()) {
                    echo "Error updating arrears for borrower ID $borrower_id: " . $update_stmt->error . "<br>";
                }
                $update_stmt->close();
            }

            
            // Run the rental query to update no_pay
            $rental_query = "UPDATE borrowers b
                LEFT JOIN (
                    SELECT borrower_id, COUNT(du_date) AS no_pay
                    FROM payments
                    WHERE payment_date <= CURDATE()
                    GROUP BY borrower_id
                ) p ON b.id = p.borrower_id
                SET b.no_pay = IFNULL(p.no_pay, 0)";

            if (!$conn->query($rental_query)) {
                echo "Error updating no_pay in borrowers: " . $conn->error;
            } else {
                echo "<script>alert('Successfully updated!'); 
                window.location.href = 'collect_amount.php';
                </script>";
            }
        } else {
            echo "No borrowers found.";
        }
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close(); // Close the statement
}

$conn->close();
?>
