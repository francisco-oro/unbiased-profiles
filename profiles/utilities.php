<?php
// Utilities for the unbiased profiles project
// Display error and success messages
function flashMessage() {
    if (isset($_SESSION['success'])) {
        echo('<p class="bg-success">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']); 
    }

    if (isset($_SESSION['error'])) {
        echo('<p class="bg-danger">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']); 
    }
}

/* validateFields retrieves input from the user in the form 
    str_tag1, str_tag2, ... , str_tagi
    numeric_tag1, numeric_tag2, ... , numeric_tagi
    and it will return an error message whenever invalid or empty inputs are found
*/
function validateFields($str_tag, $numeric_tag){
    for ($i=0; $i <= 9; $i++) { 
        if (! isset($_POST[$numeric_tag.$i])) continue;
        if(!isset($_POST[$str_tag.$i])) continue;
        
        $year = $_POST[$numeric_tag.$i]; 
        $desc = $_POST[$str_tag.$i];
        
        if (strlen($desc) < 1 || strlen($year) < 1) {
            return "All fields are required";   
        }
        if (! is_numeric($year)) {
            return "Position year must be numeric"; 
        } 
        return true; 
    }
}

/* Looks up a school in the institutions table. 
If it's found, returns its id
If it's not found, proceeds insert it as a new record and returns its id.
*/
function getSchool_id($school_name, $pdo){
    $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :in');
    $stmt->execute(array(
        'in' => $school_name
    ));
    // If the institution is not found in the database, add it as a new entry
    $institution = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($institution == false) {
        $stmt = $pdo->prepare('INSERT INTO institution(name) VALUES (:name)');
        $stmt->execute(array(
            ':name' => $school_name
        ));
        return $pdo->lastInsertId();
    }
    return $institution['institution_id'];
}

// Validate if the current user is the owner of the profile

function validateOwnership($user_id, $profile_id, $pdo){
    $stmt = $pdo->prepare('SELECT user_id FROM profiles WHERE profile_id = :pid');
    $stmt->execute(array(
        ':pid' => $profile_id
    )); 
    $retval = $stmt->fetch(PDO::FETCH_ASSOC);
    if($retval['user_id'] == $user_id){
        return true;
    }
    return "You're not allowed to edit this profile";
}

function getPositions($profile_id, $pdo){
    $stmt = $pdo->prepare('SELECT year, description, rank FROM positions WHERE profile_id = :pid ORDER BY RANK');
    $stmt->execute(array(
        ':pid' => $profile_id
    ));
    $retval = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $retval; 
}

function getEducation($profile_id, $pdo){
    $stmt = $pdo->prepare('SELECT year, name, rank FROM education JOIN institution 
                            ON education.institution_id = institution.institution_id
                            WHERE profile_id = :pid ORDER BY RANK'); 
    $stmt->execute(array(
        'pid' =>  $profile_id
    ));
    $_retval = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $_retval; 
}