<?php
    $pdo = new PDO('mysql:host=localhost; port=3306; dbname=misc', 'francisco', 'anita101');
    $pdo->setAttribute(pdo::ATTR_ERRMODE, pdo::ERRMODE_EXCEPTION);
?>