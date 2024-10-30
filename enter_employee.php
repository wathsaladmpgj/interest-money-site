

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Employee Details</title>
    <link rel="stylesheet" href="./css/enter_employee.css">
</head>
<body>
    <nav>
        <li><a href="./employee.php">UPDATE EMPLOYEE</a></li>
    </nav>
    <h2>Enter New Employee Details</h2>
    <form action="enter_employee1.php" method="POST">
        <label for="name">Employee Name</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="nic">NIC</label>
        <input type="text" id="nic" name="nic" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
