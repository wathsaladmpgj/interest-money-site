

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Borrower</title>
    <link rel="stylesheet" href="./css/add_borrow.css">
</head>
<body>
    <h1>Add New Borrower</h1>


    <form action="add_borrower1.php" method="post">
    <h1>Add New Borrower</h1>

    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="nic">Enter NIC Number:</label>
        <input type="text" id="nic" name="nic" required>
    </div>

    <div class="form-group">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address">
    </div>

    <div class="form-group">
        <label for="amount">Loan Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="interestRate">Interest Rate (%):</label>
        <input type="number" id="interestRate" name="interestRate" min="0" max="100" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="date_lent">Loan Date:</label>
        <input type="date" id="date_lent" name="date_lent" required>
    </div>

    <div class="form-group">
        <label for="norental">Number of Rentals:</label>
        <input type="number" id="norental" name="norental" required>
    </div>

    <input type="submit" value="Submit">
</form>

      
</body>
</html>


