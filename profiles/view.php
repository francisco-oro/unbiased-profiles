<?php
    require_once('pdo.php');
    session_start();
    
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
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <?php 
            if (isset($_SESSION['error'])) {
                echo "<p class='bg-danger'>".$_SESSION['error']."</p>"; 
                unset($_SESSION['error']);
            }
        ?>
        <h1>Profile Information: </h1>
            <p>First name: <?=htmlentities($row['first_name'])?></p>
            <p>Last name: <?=htmlentities($row['last_name']) ?></p>
            <p>Email: <?=htmlentities($row['email'])?></p>
            <p>Headline: <?=htmlentities($row['headline']) ?></p>
            <p>Summary: <?=htmlentities($row['summary']) ?></p>
            <a style="color:white;" href="index.php">Done</a>

        <h2>Positions: </h2>
        <ul>
            <?php
                $stmt = $pdo->prepare("SELECT year, description, rank FROM positions WHERE profile_id = :pid ORDER BY rank");
                $stmt->execute(array(
                    ':pid' => $_GET['profile_id']
                ));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo('<li>');
                    echo($row['year']." - ".$row['description']);
                    echo('</li>');
                }
            ?>
        </ul>
    </div>
</body>
</html>