<?php
// db_connection.php
$servername = "localhost";  // Replace with your database server
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "interest";       // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include FPDF library
require('fpdf186/fpdf186/fpdf.php');

// Create an instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set title for the PDF
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Borrower Information', 0, 1, 'C');
$pdf->Ln(10); // Line break

// Set table headers (Name, Loan Amount, Arrears)
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(7,6,117); 
$pdf->SetTextColor(255, 255, 255); // Grey background for headers

// Define column widths
$columnWidths = [6,42,20,15,15,18,15,15,15,19,18];

// Table header
$pdf->Cell($columnWidths[0], 7, 'NO', 1, 0, 'C', true);
$pdf->Cell($columnWidths[1], 7, 'Name', 1, 0, 'C', true);
$pdf->Cell($columnWidths[2], 7, 'Due date', 1, 0, 'C', true);
$pdf->Cell($columnWidths[3], 7, 'Rental', 1, 0, 'C', true);
$pdf->Cell($columnWidths[4], 7, 'Loan amo', 1, 0, 'C', true);
$pdf->Cell($columnWidths[5], 7, 'Agree val', 1, 0, 'C', true);
$pdf->Cell($columnWidths[6], 7, 'No rental', 1, 0, 'C', true);
$pdf->Cell($columnWidths[7], 7, 'passed dy', 1, 0, 'C', true);
$pdf->Cell($columnWidths[8], 7, 'Due rent', 1, 0, 'C', true);
$pdf->Cell($columnWidths[9], 7, 'Total pay', 1, 0, 'C', true);
$pdf->Cell($columnWidths[10], 7, 'Arrears', 1, 1, 'C', true); // '1, 1' moves to the next row

// Fetch data from the database
$result = mysqli_query($conn, "SELECT name, amount, total_arrears,due_date,rental,agree_value,no_rental,total_payments,no_pay,days_passed, status FROM borrowers ORDER BY id ASC");

// Set font for the table data
$pdf->SetFont('Arial', '', 6);
$pdf->SetTextColor(0,0,0);

$row_number=1;
// Output each row of data
while ($row = mysqli_fetch_assoc($result)) {
    $due_rental =$row['days_passed']- $row['no_pay'];
    // Check the status and set fill color accordingly
    if ($row['status'] == 'yes') {
        $pdf->SetFillColor(255, 255, 0); // Yellow for 'yes' status
    } elseif($row['status']=='no'){
        $pdf->SetFillColor(245, 181, 181);
    }else {
        $pdf->SetFillColor(255, 255, 255); // White for other statuses
    }
    
    // Output the cells for each borrower
    $pdf->Cell($columnWidths[0], 5, $row_number++, 1, 0, 'C', true);
    $pdf->Cell($columnWidths[1], 5, $row['name'], 1, 0, 'C', true);
    $pdf->Cell($columnWidths[2], 5, $row['due_date'], 1, 0, 'C', true);
    $pdf->Cell($columnWidths[3], 5, $row['rental'], 1, 0, 'C', true);
    $pdf->Cell($columnWidths[4], 5, number_format($row['amount'], 2), 1, 0, 'C', true);
    $pdf->Cell($columnWidths[5], 5, number_format($row['agree_value'], 2), 1, 0, 'C', true);
    $pdf->Cell($columnWidths[6], 5, $row['no_rental'], 1, 0, 'C', true);
    $pdf->Cell($columnWidths[7], 5, $row['days_passed'], 1, 0, 'C', true);
    $pdf->Cell($columnWidths[8], 5, $due_rental, 1, 0, 'C', true);
    $pdf->Cell($columnWidths[9], 5, number_format($row['total_payments'], 2), 1, 0, 'C', true); // Format loan amount
    $pdf->Cell($columnWidths[10], 5, number_format($row['total_arrears'], 2), 1, 1, 'C', true); // Format total arrears
}

// Close the database connection
$conn->close();

// Output the PDF (D for download)
$pdf->Output('borrower_info.pdf', 'D');
?>
