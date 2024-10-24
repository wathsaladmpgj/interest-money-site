<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrowers
$sql = "SELECT id, name FROM borrowers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List</title>
    <link rel="stylesheet" href="./css/borrowBasic.css">
</head>
<body>
    <h1>Borrowers</h1>
    
    <!-- Section for new borrowers -->
    <h2>NEW BORROWERS</h2>
    <ul>
        <?php
        // Fetch borrowers along with their due_date
        $sql = "SELECT id, name, due_date,total_payments,agree_value FROM borrowers";
        $result = $conn->query($sql);

        // Flags to track if there are new or old borrowers
        $new_borrowers_exist = false;
        $old_borrowers_exist = false;

        if ($result->num_rows > 0) {
            // Display new borrowers (with due_date today or later)
            while ($row = $result->fetch_assoc()) {
                $due_date = $row['due_date'];
                $total_payment = $row['total_payments'];
                $agree_value = $row['agree_value'];

                if($total_payment<$agree_value){
                    if ($due_date >= date('Y-m-d')) {
                        if (!$new_borrowers_exist) {
                            $new_borrowers_exist = true;
                        }
                        echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";
                    }
                }   
            }
        } else {
            echo "<li>No borrowers found.</li>";
        }
        ?>
    </ul>

    <!-- Section for borrowers in arrears -->
    <h2>ARREARS BORROWERS</h2>
    <ul>
        <?php
        // Reset result pointer to fetch rows again for old borrowers
        $result->data_seek(0);

        // Display old borrowers (with due_date before today)
        while ($row = $result->fetch_assoc()) {
            $due_date = $row['due_date'];
            $total_payment = $row['total_payments'];
            $agree_value = $row['agree_value'];

            // Check if due_date is before today
            if ($due_date < date('Y-m-d')) {
                if (!$old_borrowers_exist) {
                    $old_borrowers_exist = true;
                }

                if($total_payment<$agree_value){
                    echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";
                }   
            }
        }

        if (!$old_borrowers_exist) {
            echo "<li>No arrears borrowers found.</li>";
        }
        ?>
    </ul>

    <h2>SETTEL BORROWERS</h2>
    <ul>
        <?php
        // Reset result pointer to fetch rows again for old borrowers
        $result->data_seek(0);

        // Display old borrowers (with due_date before today)
        while ($row = $result->fetch_assoc()) {
            $due_date = $row['due_date'];
            $total_payment = $row['total_payments'];
            $agree_value = $row['agree_value'];

            // Check if due_date is before today
            
            if (!$old_borrowers_exist) {
                $old_borrowers_exist = true;
            }

            if($total_payment==$agree_value){
                echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";
            }   
            
        }

        if (!$old_borrowers_exist) {
            echo "<li>No arrears borrowers found.</li>";
        }
        ?>
    </ul>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
