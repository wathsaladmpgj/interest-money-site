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
      ![Image](https://github.com/user-attachments/assets/dc63ca01-6ab3-4aac-8aec-8d353c93ed79)
      
    + Display chart of Capital Saving, New Loan, and Stocks Over Time,Collection,Income,Stock Increase Percentage.
      ![Image](https://github.com/user-attachments/assets/c3965be7-b548-43dd-b058-c0e329a24fc4)

+ Add borrower page
    + Add new borrower(Rnter name, Nic and Address) and add new loan(Select borrower name,Enter loan amount,Enter Interest rate,Enter loan date,Number of rental) for borrowers.
      ![Image](https://github.com/user-attachments/assets/5b0b0923-5a82-4000-a39a-ac606269c12b)

+ Collect Amount
    + Disply to select the numbers related to the loan to be paid when selecting the borrower.
    + When your enter thr due date, payment date and payment amount for the selected loan, the payment will be added.
    + If necessary here, if the people who have to make payments related to today have paid, select them and click on the enter payment button, then the corresponding payment will be added to them.
     ![Image](https://github.com/user-attachments/assets/217cbcac-3628-4a63-bfd5-afa2256950b5)
 
+ Borrower Details page
    + Display total loan amount all borrowers
    + A table with data related to coloumns No,Name,NIC,Address,LoanCount,Total Loan Amount,Loan Settled,Loan in Arrears,Current Loan Balance is displayed
      ![Image](https://github.com/user-attachments/assets/5df1df6c-7180-4303-bb99-3ba509dafb12)
  
+ Loan details page
    + Settled Loan,Arrears Loan,Active Loan will display the total of each loan.
    +  A table with data related to coloumns No,Name,No,Loan Date,Due Date,Rental,Loan Amount,Agree Value,No Rent,Due Rent,Total Payment,Arrears is displayed
    +  You can downlode a pdf containing a table related to that data
    +  In this table, settled loan is displayed in yellow color, arrears loan is displayed in brown color, and active loan is displayed in white color.
      ![Image](https://github.com/user-attachments/assets/5974b588-9317-4131-be87-a2f7e631056c)

    + When you click on the borrower's name for each loan, the details of all the days related to that loan are included.
      ![Image](https://github.com/user-attachments/assets/6de5d1f4-710d-4865-8fdc-0ea19a1a7087)

+ Todays's collection page
    + When the date is selected, the borrowers who have to pay and have to pay related to that date will be displayed in a table.
      ![Image](https://github.com/user-attachments/assets/789ad0b1-1519-44e4-8180-49ed63b23c4f)
      
+ Monthly details
    + When the year is selected, the monthly payment details related to that year will be displayed. Here, in each month, the amount paid, the total amount to be received in that month, how much should be received from the capital, how much should be received from the interest, the total amount paid by the borrowers in that month, the amount related to the capita from the amount received, the interest from the amount received How much and arrears will be displayed in a table.
      ![Image](https://github.com/user-attachments/assets/ae9b7210-f157-435c-9c36-2ef0a608a7ae)

    + Also, the interest received in each month related to the selected year (Allowance, Privision, Salary, Profit) is calculated and displayed in a table. Here we can change the interest rate when needed and each interest is calculated according to the changed rate. Here, the interest rate we enter is valid only from the month of entry to the following months and the interest of the previous months is determined based on the previously entered interest rate values.
      ![Image](https://github.com/user-attachments/assets/f7b5032e-8680-4734-80b2-524a053d46ec)


+ Summary page
    + In relation to the selected year, each month's capital saving, new saving, loan amount given in that month, increase in stoke compared to the previous month and capital outstand are displayed.
      ![Image](https://github.com/user-attachments/assets/94070887-7fb2-436e-abb9-364c6be48e73)


+ Employee details page
    + An employee can enter here
      ![Image](https://github.com/user-attachments/assets/5c6a3cd2-ef58-4251-bd3f-5ec442816c0f)
      
    + Details related to each employee (salary, allowance, provisions, payment date, month of payment) can be updated.
      ![Image](https://github.com/user-attachments/assets/ba7e6b0d-d8ef-4ed6-9cb8-c5dfad3daa9f)
      
    + Details related to each employee and employee details in each month are displayed here.
      ![Image](https://github.com/user-attachments/assets/205e171c-49b7-4c26-8c18-2a19bcafff76)
