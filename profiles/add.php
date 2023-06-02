<?php 
    require_once('pdo.php');
    session_start();

    if (!isset($_SESSION['user_id'])) {
        die('Not logged in'); 
    }

    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO profile(user_id, first_name, last_name, email, headline, summary) 
        VALUes (:uid, :fn, :ln, :em, :hd, :sm'); 
        $stmt->execute(array (
             ':uid' => $_POST['user_id'],
             ':fn' => $_POST['first_name'],
             ':ln' => $_POST['last_name'],
             ':em' => $_POST['email'],
             ':hd' => $_POST['headline'],
             ':sm' => $_POST['summary']
         ));
    }

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Francisco Abimael Oro Estrada's Profiles Database</title>
</head>
<body>
    <div>
        <form method="post">
            <label for="fn">First Name: </label>
            <input type="text" name="first_name" id="fn">
            <label for="ln">Last name: </label>
            <input type="text" name="last_name" id="ln">
            <label for="em">Email: </label>
            <input type="text" name="email"  id="em">
            <label for="hd">Headline: </label>
            <input type="text" name="headline" id="hd">
            <label for="sm">Summary: </label>
            <input type="textfield" name="summary" id="sm">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>">
            <input type="submit" value="Add profile" name="add">
        </form>
    </div>
</body>
</html>