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
            $stmt->close();
            return $total_payment;
        }

        date_default_timezone_set('Asia/Colombo'); // Set time zone

        $borrowers_sql = "SELECT * FROM borrowers";
        $borrowers_result = $conn->query($borrowers_sql);

        if ($borrowers_result->num_rows > 0) {
            // Fetch total rental amounts up to yesterday for each borrower
            $payments_sql = "SELECT borrower_id, SUM(rental_amount) AS total_rental_paid
                             FROM payments WHERE du_date <= CURDATE() - INTERVAL 1 DAY GROUP BY borrower_id";
            $payments_result = $conn->query($payments_sql);
            $total_rental_paid_by_borrower = [];

            while ($row = $payments_result->fetch_assoc()) {
                $total_rental_paid_by_borrower[$row['borrower_id']] = $row['total_rental_paid'];
            }

            while ($borrower = $borrowers_result->fetch_assoc()) {
                $borrower_id = $borrower['id'];
                $bar_rent = $borrower['rental'];
                $total_py = fetch_total_payments($conn, $borrower_id);
                
                // Use total rental paid from the calculated array or default to 0
                $total_rental_paid = $total_rental_paid_by_borrower[$borrower_id] ?? 0;

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
                $arrears = max($expected_payment_by_today - $total_rental_paid, 0);
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
            }
            
        } else {
            echo "No borrowers found.";
        }

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
