# php-project1
First term PHP project.

# Description
The project will be about the management of the employees' expenses reports in a company. In general, the basic functionality of this project will be:
- A database that contains and stores information about the users and the expenses reports.
- A login and maybe a register in order to register new users into the database.
- A home page that will show the user their expenses.
- An option to add new reports, which will be stored into the database.
- An option to show the state in which the reports are (approved, pending, or denied).
- For the managers, an option to edit the states of the reports.
- For the normal employees, an option to delete denied reports.

# Project extensions
- About <a href="https://blog.sqlauthority.com/2023/10/20/sql-server-best-practices-for-securely-storing-passwords/#:~:text=Database%20Password%20Hashing%20in%20SQL%20Server&text=INSERT%20INTO%20Users(username%2C%20password_hash,being%20stored%20in%20the%20database.">password hashing</a>. This will come in handy later in the project, when getting into the Extensions of the project (E1).
- Search information about turning into PDF the HTML tables.
- Add option to send through email either as HTML or as a PDF file.
- Project extensions that are to be added:
    - E1. Encrypted user password (MUST):
        - Store the user password using an encryption method. See functions password_hash and password_verify.
    - E2. Deleting (MUST):
        - Allow normal users to delete pending approval requests.
    - E3. Expense report (MUST):
        - Expense managers will be able to generate a reprot of the expenses made by an employee in a given date interval. The report will consist of a table detailing the expenses, the total number of expenses, and the total amount.
    - E4. Report downloading (MUST):
        - Allow managers to download the generated reports in PDF format.
    - E5. Report sending by email (Maybe):
        - Allow managers to send the generated reports by email, either as HTML in the body or as a HTML/PDF attachment.
    - E6. Testing (Maybe):
        - Using Codeception, write acceptance tests for all the functionality.
    - E7. PHPDocumentor (MUST):
        - Write DocBlocks for your files and functions and generate the HTML documentation files.