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
        $position_status = validateFields('desc', 'year');
        if(is_string($position_status)){
            $_SESSION['error'] = $position_status;
            header('Location: add.php');
            return;
        }

        /* Check if education data has been added and discard
        bad inputs */
        $education_status = validateFields('edu_school', 'edu_year');
        if(is_string($education_status)){
            $_SESSION['error'] = $$education_status;
            header('Location: add.php');
            return;
        }
    
        // Data is valid - time to insert into the database
        
        // Profiles table
        $stmt = $pdo->prepare('INSERT INTO profiles(user_id, first_name, last_name, email, headline, summary) 
        VALUES (:uid, :fn, :ln, :em, :hd, :sm)'); 
        $stmt->execute(array (
             ':uid' => $_SESSION['user_id'],
             ':fn' => $_POST['first_name'],
             ':ln' => $_POST['last_name'],
             ':em' => $_POST['email'],
             ':hd' => $_POST['headline'],
             ':sm' => $_POST['summary']
         ));
         $profile_id = $pdo->lastInsertId();

        // Positions table
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

        
        // Education table
        $rank = 1; 
        for ($i=1; $i < 9; $i++) { 
            if(!isset($_POST['edu_year'.$i])) continue;
            if(!isset($_POST['edu_school'.$i])) continue; 
            /* Check if the institution already exists. Otherwise, insert the new name into the database and 
            retrieve the institution id from it */
            $institution_id = getSchool_id($_POST['edu_school'.$i], $pdo);
            $year = $_POST['edu_year'.$i];
            $stmt = $pdo->prepare('INSERT INTO education(institution_id, profile_id, rank, year) VALUES (:ins_id, :pid, :rank, :year)');
            $stmt->execute(array(
                ":ins_id" => $institution_id,
                ":pid" => $profile_id,
                ":rank" => $rank, 
                ":year" => $year
            ));
            $rank++;
        }
        // Record inserted. Leaving add.php
        $_SESSION['success'] = "Added";
         header('Location: index.php');
         return;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Francisco Abimael Oro Estrada's Profiles Database</title>
    <?php require_once('head.php'); ?> 
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
            <label for="addEdu">Education: </label>
            <input class="btn btn-light m-2 w-50" type="submit" name="addEdu" id="addEdu" value="+">
            <div class="w-100" id="education_fields">

            </div>
            <label for="addPos">Position: </label>
            <input class="btn btn-light m-2 w-50" type="submit" name="addPos" id="addPos" value="+">
            <div class="w-100" id="position_fields">

            </div>
            
            <div class='d-flex flex-row position-relative m-auto w-100'>
                <input class="btn btn-success m-2 w-50" onclick="return validateEntry();" type="submit" name="add" value="Add">
                <input class="btn btn-light m-2 w-50" type="submit" name="cancel" value="Cancel">
            </div>
        </form>

        <script type="text/javascript">
            $(document).ready(function() {
                countPos = 0;
                countEdu = 0;
                // Look up the element with addPos as its id
                $('#addPos').click(function (event) {
                    // Always return false throught the code
                    event.preventDefault();
                    /* Global variable to keep of how many positions
                    have been inserted */
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    // Increment countPos as new fields are added
                    countPos++;
                    // Display current position to the console
                    window.console && console.log("Adding postion "+countPos)
                    // Append a new position inside the position_fields div
                    var source = $('#pos-template').html();
                    $('#position_fields').append(soure.replace(/@COUNT@/, countPos)); 
                });

                $('#addEdu').click(function (event) {
                    event.preventDefault();

                    if (countEdu >=9) {
                        alert("Maximum of nine education entries exceeded");
                        return; 
                    }
                    countEdu++;
                    window.console && console.log('Adding education' + countEdu);
                    // Grab some HTML from the edu container template and insert it in the DOM 
                    var source = $('#edu-template').html();
                    $('#education_fields').append(source.replace(/@COUNT@/g, countEdu));

                    $('.school').autocomplete({source : 'school.php'}); 
                })
                $('.school').autocomplete({source : 'school.php'}); 
            });
        </script>
        <!-- HTML with substitution hotspots -->
        <script id='edu-template' type="text">
            <div id='edu@COUNT@'>
                <p>Year:
                    <input type="text" name='edu_year@COUNT@' value=''>
                    <input type="button" value='-' onclick='$("#edu@COUNT@").remove(); return false;'>
                </p>
                <p>School:
                    <input type="text" name="edu_school@COUNT@" class="school ui-autocomplete-input" size="80">
                </p>
            </div>
        </script>
        <script id='pos-template' type="text">
            <div id='position@COUNT@'>
                <p>Year:
                    <input type="text" name='year@COUNT@' value=''>
                    <input type="button" value='-' onclick='$("#position@COUNT@").remove(); return false;'>
                </p>
                <textarea name="desc@COUNT@" cols="80" rows="8"></textarea>
            </div>
        </script>
    </div>
</body>
</html>