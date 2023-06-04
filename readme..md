# Resume Registry   

This Resumes CRUD Database Application is a user-friendly and efficient tool designed to manage information about automobiles. It offers a comprehensive set of functionalities for creating, reading, updating, and deleting automobile records in a seamless and organized manner.

The application allows users to store and access detailed information about various automobiles, including their make, model, year, color, mileage, price, and other relevant attributes. Users can easily add new automobiles to the database, retrieve specific records based on search criteria, update existing information, or remove entries when necessary.  

## Technologies used
- PHP Version 8.0.10
- Bootstrap 4
- MySQL

## Set up

A PHP hosting environment such as MAMP or XAMPP are recommended for this task

First create a database, a user to connect the database and a password for that user as follows:

Set up the database from phpmyadmin:
```sql
    CREATE database <database_name>; 
    GRANT ALL ON <database_name>.* TO '<user_name>'@'localhost' IDENTIFIED BY '<passowrd>';
    GRANT ALL ON <database_name>.* TO '<user_name>'@'123.0.0.1' IDENTIFIED BY '<passowrd>';
```
Make a connection to the database as follows and save the file as pdo.php, inside the autosdb directory.

### For MAMP users
<default_port> is 8889
```php
    $pdo = new PDO('mysql:host=localhost; port=<default_port>'; dbname='<database_name>', '<user_name>', '<password>');
    $pdo->setAttribute(pdo::ATTR_ERRMODE, pdo::ERRMODE_EXCEPTION);
```
### For XAMPP users
<default_port> is 3306
```php
    $pdo = new PDO('mysql:host=localhost; port=<default_port>'; dbname='<database_name>', '<user_name>', '<password>');
    $pdo->setAttribute(pdo::ATTR_ERRMODE, pdo::ERRMODE_EXCEPTION);
```
## Screenshots

![Rejecting an incorrect password](img/login-1.png)
![Showing ACCESS DENIED when the user is not logged in](img/access-denied.png)
![Succesfully adding a new vehicle](img/entries.png)
![Rejecting an incorrect entry when adding a new auto](img/add-error.png)
![Mysql administration interface showing the current autos](img/mysql-database.png)
![Rejecting an incorrect entry when adding an existing auto](img/invalid-data.png)
![Succesfully removing an entry from the database](img/succesful-delete.png)

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)