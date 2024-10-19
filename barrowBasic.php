<?php
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
</head>
<body>
    <h1>Borrowers</h1>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            // Output each name as a clickable link with the class 'borrower-name'
            while ($row = $result->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";
            }
        } else {
            echo "No borrowers found.";
        }
        ?>
    </ul>

    <!-- Custom Context Menu -->
    <div id="context-menu">
        <ul>
            <li id="delete-option">Delete Borrower</li>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>
