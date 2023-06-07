<?php 
    require_once('pdo.php');
    require_once('utilities.php');
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

        /* Check if position data has been added and discard
        bad inputs */
        $msg = validatePos();
        if(is_string($msg)){
            $_SESSION['error'] = $msg;
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
         $profile_id = $pdo->lastInsertId();

        // Data is valid - time to insert into the database
        $rank = 1; 
        for ($i=1; $i <= 9; $i++) { 
            if(!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue; 
            $year = $_POST['year'.$i];
            $descripition = $_POST['desc'.$i]; 
            $stmt = $pdo->prepare('INSERT INTO positions(profile_id, year, description, rank) VALUES (:pid, :y, :de, :ra)');
            $stmt->execute(array(
                ":pid" => $profile_id,
                ":y" => $year,
                ":de" => $descripition, 
                ":ra" => $rank
            ));
            $rank++;
        }
        // Record inserted. Leaving add.php
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
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script type='text/javascript' src="script.js"></script>
    <?php require_once('utilities.php'); ?> 
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <?php flashMessage(); ?> 
        <form method="post" class="d-flex flex-column w-50 m-auto">
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
            <label for="addPos">Position: </label>
            <input class="btn btn-light m-2 w-50" type="submit" name="addPos" id="addPos" value="+">
            <input type="hidden" class="btn btn-light m-2 w-50" name="user_id" value="<?= $_SESSION['user_id']?>">
            <div id="position_fields" class="w-100 ">
                
            </div>
            
            <div class='d-flex flex-row position-relative m-auto w-100'>
                <input class="btn btn-success m-2 w-50" onclick="return validateEntry();" type="submit" name="add" value="Add">
                <input class="btn btn-light m-2 w-50" type="submit" name="cancel" value="Cancel">
            </div>
        </form>
        <script type="text/javascript">
            countPos = 0;
            $(document).ready(function() {
                // Look up the element with addPos as its id
                $('#addPos').click(function (event) {
                    // Always return false throught the code
                    event.preventDefault();
                    /* Global variable to keep of how many positions
                    have been inserted */
                    if (countPos >= 9) {
                        ("Maximum of nine position entries exceeded");
                        return;
                    }
                    // Increment countPos as new fields are added
                    countPos++;
                    // Display current position to the console
                    window.console && console.log("Adding postion "+countPos)
                    // Append a new position inside the position_fields div
                    $('#position_fields').append(
                        "<div id='position"+countPos+"' class='p-relative'> \
                        <p> Year: <input type='text' name='year"+countPos+"' value ='' /> \
                        <input type='button' value='-' \
                        onclick = '$(\"#position"+countPos+"\").remove(); return false;'></p> \
                        <textarea name='desc"+countPos+"' rows='8' cols='80'></textarea>\
                        </div>"); 
                });
            });
        </script>
    </div>
</body>
</html>