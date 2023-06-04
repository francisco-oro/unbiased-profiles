<?php 
    require_once('pdo.php');
    session_start();

    if (!isset($_SESSION['user_id'])) {
        die('Not logged in'); 
    }

    if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return; 
    }

    if (isset($_POST['add'])) {
        // Redirect to add.php if any value is missing 
        if (!(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))) {
            $_SESSION['error'] = "All fields required";
            header('Location: add.php');
            return; 
        }

        // Redirect if the user input contains any empty string
        if (strlen($_POST['first_name']) < 2 || strlen($_POST['last_name'])<2 || strlen($_POST['email'])<2 || strlen($_POST['headline'])<2 || strlen($_POST['summary'])<2) {
            $_SESSION['error'] = "All fields are required";
            header('Location: add.php');
            return;
        }

        // Redirect if an invalid email adress has been provided
        if (!strpos($_POST['email'], '@')) {
            $_SESSION['error'] = "Invalid email";
            header('Location: add.php');
            return;
        }
    
        // Start the query from the database 
        echo($_POST['user_id']);
        $stmt = $pdo->prepare('INSERT INTO profiles(user_id, first_name, last_name, email, headline, summary) 
        VALUES (:uid, :fn, :ln, :em, :hd, :sm)'); 
        $stmt->execute(array (
             ':uid' => (int)($_POST['user_id']),
             ':fn' => $_POST['first_name'],
             ':ln' => $_POST['last_name'],
             ':em' => $_POST['email'],
             ':hd' => $_POST['headline'],
             ':sm' => $_POST['summary']
         ));

         $_SESSION['success'] = "Added";
         header('Location: index.php');
         return;
    }

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Francisco Abimael Oro Estrada's Profiles Database</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <form method="post" class="d-flex flex-column w-25 m-auto">
            <label for="fn">First Name: </label>
            <input type="text" name="first_name" id="fn">
            <label for="ln">Last name: </label>
            <input type="text" name="last_name" id="ln">
            <label for="em">Email: </label>
            <input type="text" name="email"  id="em">
            <label for="hd">Headline: </label>
            <input type="text" name="headline" id="hd">
            <label for="sm">Summary: </label>
            <textarea name="summary" id="sm" cols="30" rows="10"></textarea>
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>">
            <div class='d-flex flex-row position-relative m-auto w-100'>
                <input class="btn btn-success m-2 w-50" onclick="return validateEntry();" type="submit" name="add" value="Add">
                <input class="btn btn-light m-2 w-50" type="submit" name="cancel" value="Cancel">
            </div>
        </form>
    </div>
    <script>
        function isEmpty(string) {
            string = string.replace(/\s+/g, '');
            if (string == null || string == '') {
                return true;
            }
            return false; 
        }

        function validateEntry() {
            console.log('Validating');
            try {
                fn = document.getElementById('fn').value;
                ln = document.getElementById('ln').value;
                em = document.getElementById('em').value;
                hd = document.getElementById('hd').value;
                sm = document.getElementById('sm').value;
                if (isEmpty(fn) || isEmpty(ln) || isEmpty(em) || isEmpty(hd) || isEmpty(sm)) {
                    alert("All fields are required");
                    return false;
                } 
                if (em.includes('@')) {
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
</body>
</html>