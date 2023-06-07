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

function validatePos(){
    for ($i=0; $i <= 9; $i++) { 
        if (! isset($_POST['year'.$i])) continue;
        if(!isset($_POST['desc'.$i])) continue;
        
        $year = $_POST['year'.$i]; 
        $desc = $_POST['desc'.$i];
        
        if (strlen($desc) < 1 || strlen($year) < 1) {
            return "All fields are required";   
        }
        if (! is_numeric($year)) {
            return "Position year must be numeric"; 
        } 
        return true; 
    }
}