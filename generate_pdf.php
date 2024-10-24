<?php
require('fpdf186/fpdf186/fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['download_pdf'])) {
    $selected_date = $_POST['selected_date'];

    // Fetch payment details for PDF
    $sql = "SELECT payments.rental_amount, payments.payment_date,payments.du_date, borrowers.name 
            FROM payments
            INNER JOIN borrowers ON payments.borrower_id = borrowers.id
            WHERE payments.payment_date = '$selected_date'";
    $result = $conn->query($sql);

    $sq = "SELECT name, rental 
           FROM borrowers 
           WHERE id NOT IN (SELECT borrower_id FROM payments WHERE payment_date = '$selected_date')
           AND due_date >= CURDATE()";
    $resul = $conn->query($sq);

    // Create a new PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Title
    $pdf->Cell(0, 10, 'Payments on ' . $selected_date, 0, 1, 'C');
    $row_number = 1;
    // Payments table
    if ($result->num_rows > 0) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 10, 'NO', 1);
        $pdf->Cell(60, 10, 'Borrower Name', 1);
        $pdf->Cell(34, 10, 'Rental Amount', 1);
        $pdf->Cell(30, 10, 'Due Date', 1);
        $pdf->Cell(30, 10, 'Payment Date', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10, 10, $row_number++, 1);
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(34, 10, $row['rental_amount'], 1);
            $pdf->Cell(30, 10, $row['du_date'], 1);
            $pdf->Cell(30, 10, $row['payment_date'], 1);
            $pdf->Ln();
        }
    }

    // Borrowers with no payments table
    if ($resul->num_rows > 0) {
        $pdf->Ln();
        $pdf->Cell(0, 10, 'Borrowers with no payments on ' . $selected_date, 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Borrower Name', 1);
        $pdf->Cell(80, 10, 'Rental', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        while ($row = $resul->fetch_assoc()) {
            $pdf->Cell(80, 10, $row['name'], 1);
            $pdf->Cell(80, 10, $row['rental'], 1);
            $pdf->Ln();
        }
    }

    // Output PDF
    $pdf->Output('D', 'payments_report_' . $selected_date . '.pdf');
}

// Close the database connection
$conn->close();
?>
