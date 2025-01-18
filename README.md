# Interest Money Calculation 
# Web Application

A web application designed to efficiently manage borrower and employee details, payment tracking(daly,monthly), and interest calculations. Built with PHP, MySQL, and AJAX for real-time data updates.

## Features

+ Borrower Management
    + Add and view borrower details.
    + Add and view loan payment of borrower.
    + Track payment history and arrears in real-time(According to the data entered).

+ Payment Tracking
    + Monthly and Daly payment summaries, including interest received and pending.
    + Dynamic calculation of arrears and interest rates.
 

+ Dynamic Interest Calculations
    + Interest rates can be updated, affecting calculations from the updated month onward.
    + Real-time updates ensure accurate financial records.

+ Automated Updates
    + Days passed and total arrears update automatically without page refresh.
    + Flexible rental,agreed value,interest for Day,due Date and payment start date calculations.

## Application Pages
+ Home page
    + Auto calculate and disply details of CURRENT STOCK,FUTURE CAPITAL,FUTURE INTEREST,TOTAL ARIARS, Number of Customer,Number of Loans,Total AgreeValue,Total Investment,Total Interest,Total Collection,Collect capital,Collect Interest,All Salary,All Allowance,All Privision,All Profit,Payed Salary,Payed Allowance,Payed Privision,Payed Profit,Profit,FutureSalary,Future Allowance,Future Privision .
    + Display chart of Capital Saving, New Loan, and Stocks Over Time,Collection,Income,Stock Increase Percentage.

+ Add borrower page
    + Add new borrower(Rnter name, Nic and Address) and add new loan(Select borrower name,Enter loan amount,Enter Interest rate,Enter loan date,Number of rental) for borrowers.

+ Collect Amount
    + Disply to select the numbers related to the loan to be paid when selecting the borrower.
    + When your enter thr due date, payment date and payment amount for the selected loan, the payment will be added.
    + If necessary here, if the people who have to make payments related to today have paid, select them and click on the enter payment button, then the corresponding payment will be added to them.
 
+ Borrower Details page
    + Display total loan amount all borrowers
    + A table with data related to coloumns No,Name,NIC,Address,LoanCount,Total Loan Amount,Loan Settled,Loan in Arrears,Current Loan Balance is displayed
  
+ Loan details
    + Settled Loan,Arrears Loan,Active Loan will display the total of each loan.
    +  A table with data related to coloumns No,Name,No,Loan Date,Due Date,Rental,Loan Amount,Agree Value,No Rent,Due Rent,Total Payment,Arrears is displayed
    +  You can downlode a pdf containing a table related to that data
    +  In this table, settled loan is displayed in yellow color, arrears loan is displayed in brown color, and active loan is displayed in white color.
 
