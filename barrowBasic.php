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
    <style>
        /* Custom context menu styling */
        #context-menu {
            display: none;
            position: absolute;
            z-index: 1000;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }
        #context-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #context-menu ul li {
            padding: 8px 12px;
            cursor: pointer;
        }
        #context-menu ul li:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const contextMenu = document.getElementById('context-menu');
            let currentBorrowerId = null;

            // Show custom context menu on right-click
            document.querySelectorAll('.borrower-name').forEach((element) => {
                element.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    currentBorrowerId = this.getAttribute('data-id');
                    contextMenu.style.display = 'block';
                    contextMenu.style.top = `${e.pageY}px`;
                    contextMenu.style.left = `${e.pageX}px`;
                });
            });

            // Hide the custom context menu when clicking elsewhere
            document.addEventListener('click', function(e) {
                contextMenu.style.display = 'none';
            });

            // Handle delete option click
            document.getElementById('delete-option').addEventListener('click', function() {
                if (confirm) {
                    // Send AJAX request to delete borrower from the database
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_borrower.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert(xhr.responseText);
                            location.reload(); // Reload page after deletion
                        }
                    };
                    xhr.send('id=' + currentBorrowerId);
                }
            });
        });
    </script>
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
