<?php
    require_once('pdo.php');
    $stmt = $pdo->prepare('SELECT name FROM institution WHERE name like :prefix');
    $stmt->execute(array(
        ':prefix' => $_REQUEST['term']."%"
    ));
    // Store all the names inside the retval array
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $retval[] = $row['name'];
    }
    echo(json_encode($retval, JSON_PRETTY_PRINT)); 
?>