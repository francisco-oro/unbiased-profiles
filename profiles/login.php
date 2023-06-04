<?php 
session_start();
require_once('pdo.php');
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

// Salt hash is combined along 
$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    // Ensure the user input is not empty
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "User name and password are required";
        header('Location: login.php');
        return; 

    } else {
        // Check if a valid email address was provided
        if(strpos($_POST['email'], "@") === false){ 
            $_SESSION['error'] = "Email must have an at-sign (@)";
            // Post - Redirect - Get implementation : Redirect the browser to login.php
            header('Location: login.php'); 
            return;
        }

        // Check if the provided email is in the users database 
        $stmt = $pdo->prepare('SELECT user_id, email, password FROM users WHERE email = :em');
        $stmt->execute(array(
            ":em" => $_POST['email']
        ));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $check = hash('md5', $salt.$_POST['pass']);

        if ( $check == $user['password'] ) {
            // The email is stored in $_SESSION and it will stay there until you explicitly remove it
            $_SESSION['user_id']=$user['user_id'];  
            $_SESSION['name'] = $user['name'];
            header('Location: index.php');
            return;
        } else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect email or password";
            header('Location: login.php');
            return; 
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Francisco Abimael Oro Estrada's Login Page </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="text-white d-flex justify content-center flex-column text-center w-100 p-5">
    <h1 class="h-100 p-2">Please Log In</h1>
    <?php
        if (isset($_SESSION['error'])) {
            echo('<p class= "bg-danger">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
        }
    ?> 
    <form method="POST">
            <label class="p-2" for="em">User Name</label><br/>
            <input class= "p-2" type="text" name="email" id="em"><br/>
            <label class="p-2" for="password">Password</label><br/>
            <input class="p-2" type="password" name="pass" id="ps"><br/>
        <input type="submit" onclick="return doValidate();" class="btn btn-success m-3" value="Log In">
        <input type="submit" class="btn btn-light m-3" name="cancel" value="Cancel">
    </form>
    <script>
        function doValidate() {
            console.log('Validating');
            try {
                pw = document.getElementById('ps').value;
                email =document.getElementById('em').value;
                console.log('Validating pw' + pw);
                console.log('Validating' + email);
                if (pw == null || email==null || pw == "" || email == "") {
                    alert("Both fields must be filled out");
                    return false;
                } 
                if (email.includes('@')) {
                    return true; 
                } else {
                    alert('Invalid email adress');
                }
            } catch (error) {
                console.log(error); 
                return false; 
            }
            return false; 
        }
    </script>
    <p>
        For a password hint, view source and find a password hint
        in the HTML comments.
        <!-- Hint: Password is php123-->
    </p>
</div>
</body>