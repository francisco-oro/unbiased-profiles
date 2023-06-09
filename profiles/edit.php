<?php
    require_once('pdo.php');
    require_once('utilities.php');
    session_start(); 
    if (! isset($_SESSION['user_id'])) {
        die('ACCES DENIED');
    }
    if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return; 
    }

    if (isset($_POST['Save'])) {
        if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

            /* Check if position data has been added and discard
            bad inputs */
            $position_status = validateFields('desc', 'year');
            if(is_string($position_status)){
                $_SESSION['error'] = $position_status;
                header('Location: edit.php');
                return;
            }

            /* Check if education data has been added and discard
            bad inputs */
            $education_status = validateFields('edu_school', 'edu_year');
            if(is_string($$education_status)){
                $_SESSION['error'] = $$education_status;
                header('Location: edit.php');
                return;
            }

            // Validate if the current user is the owner of the profile
            // $ownership_status = validateOwnership($_POST['user_id'], $_SESSION['user_id'], $pdo);
            // if (is_string($ownership_status)) {
            //     $_SESSION['error'] = $ownership_status;
            //     header('index.php');
            //     return; 
            // }

            // Update the basic info in the profiles table
            $stmt = $pdo->prepare('UPDATE profiles 
            SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary  WHERE profile_id = :id'); 
            $stmt->execute(array(
                ':id' => $_POST['profile_id'],
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary']
            ));

            // Remove the old education records from this profile
            $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id = :pid');
            $stmt->execute(array(
                ':pid' => $_POST['profile_id']
            ));

            // Remove the old positions from this profile
            $stmt = $pdo->prepare("DELETE FROM positions WHERE profile_id = :pid");
            $stmt->execute(array(
                ':pid' => $_POST['profile_id']
            ));

            // Send back the new education records to the database
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

            // Send back the new positions to the database
            $rank = 1; 
            for ($i=1; $i <= 9; $i++) { 
                if(!isset($_POST['year'.$i])) continue;
                if(!isset($_POST['desc'.$i])) continue; 
                $year = $_POST['year'.$i];
                $descripition = $_POST['desc'.$i]; 
                $stmt = $pdo->prepare('INSERT INTO positions(profile_id, year, description, rank) VALUES (:pid, :y, :de, :ra)');
                $stmt->execute(array(
                    ":pid" => $_POST['profile_id'],
                    ":y" => $year,
                    ":de" => $descripition, 
                    ":ra" => $rank
                ));
                $rank++;
            }

            // Head back to index.php
            $_SESSION['success'] = "Record updated"; 
            header('Location: index.php');
            return; 
        } else {
            $_SESSION['error'] = "All fields are required";
            header('Location: edit.php?profile_id='.$_POST['profile_id']);
            return; 
        }
    }

    // Load personal info
    $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name, email, headline, summary FROM profiles WHERE profile_id = :id');
    $stmt->execute(array(
        ":id" => $_GET['profile_id']
    )); 
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
        $_SESSION['error'] = 'Bad profile id';
        header('Location: index.php');
        return;
    }
    // Load education info
    $education_records = getEducation($_GET['profile_id'], $pdo);
    // Load Positions info
    $position_records = getPositions($_GET['profile_id'], $pdo); 
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Francisco Abimael Oro Estrada's Resume Registry</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script type='text/javascript' src="script.js"></script>
    <?php require_once('utilities.php'); ?> 
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <h1>New changes for <?= $row['first_name']." ".$row['last_name'] ?></h1>
        <?php flashMessage();?>
        <form method="post" class="d-flex flex-column w-25 m-auto">
            <label for="fn">First name: </label>
            <input type="text" name="first_name" id="fn" value="<?= $row['first_name'] ?>"><br/>
            <label for="ln">Last name: </label>
            <input type="text" name="last_name" id="ln" value="<?= $row['last_name'] ?>"><br/>
            <label for="em">Email: </label>
            <input type="text" name="email" id="em" value="<?= $row['email'] ?>"><br/>
            <label for="hd">Headline: </label>
            <input type="text" name="headline" id="hd" value="<?= $row['headline'] ?>"><br/>
            <label for="sm">Summary: </label>
            <textarea name="summary" id="sm" cols="30" rows="10"><?=$row['summary']?></textarea>
            <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">

            <?php
            // Retrieve old records from the education table
                // Set up a control variable to keep track on the components inserted 
                $i = 0;
                echo('<label for="addEdu">Education: </label>
                <input class="btn btn-light m-2 w-50" type="submit" name="addEdu" id="addEdu" value="+">
                <div class="w-100" id="education_fields">');
                // If old records are found, retrieve and display them
                if (count($education_records) > 0) {
                    foreach ($education_records as $school) {
                        echo("            
                        <div id='edu'".$i.">
                            <p>Year:
                                <input type='text' name='edu_year".$i."' value='".htmlentities($school['year'])."'>
                                <input type='button' value='-' onclick='$('#edu".$i."').remove(); return false;'>
                            </p>
                            <p>School:
                                <input type='text' name='edu_school".$i."' class='school' size='80' value='".htmlentities($school['name'])."'>
                            </p>
                        </div>");
                        $i++; 
                    }
                }
                echo('</div>'); 

            // Retrieve old records from the positions table
                // Set up a control variable to keep track on the components inserted 
                $i = 0;
                echo('<label for="addPos">Position: </label>');
                echo('<input class="btn btn-light m-2 w-50" type="submit" name="addPos" id="addPos" value="+">');
                echo('<div class="w-100" id="position_fields">');
                // If old records are found, retrieve and display them
                if (count($position_records) > 0) {
                    foreach ($position_records as $position) {
                        echo("            
                        <div id='position".$i."'>
                            <p>Year:
                                <input type='text' name='year".$i."' value='".htmlentities($position['year'])."'>
                                <input type='button' value='-' onclick='$('#position".$i."').remove(); return false;'>
                            </p>
                            <textarea name='desc".$i."' cols='80' rows='8'>".htmlentities($position['description'])."</textarea>
                        </div>");
                        $i++; 
                    }
                }
                echo('</div>'); 
            ?>
            <input type="hidden" class="btn btn-light m-2 w-50" name="user_id" value="<?= $_SESSION['user_id']?>">

            <div class='d-flex flex-row position-relative m-auto w-100'>
                <input class="btn btn-success m-2 w-50" onclick="return validateEntry();" type="submit" name="Save" value="Save">
                <input class="btn btn-light m-2 w-50" type="submit" name="cancel" value="Cancel">
            </div>
        </form>
        <!-- JQuery functions -->

        <script type="text/javascript">
            $(document).ready(function() {
                countPos = <?= count($education_records);?>;
                countEdu = <?= count($position_records);?>;
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
                    $('#position_fields').append(source.replace(/@COUNT@/, countPos)); 
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
                    // Autocomplete field
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
                    <input type="text" name="edu_school@COUNT@" class="school" size="80">
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