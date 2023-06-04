<?php
    require_once('pdo.php');
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
            $_SESSION['success'] = "Record updated"; 
            header('Location: index.php');
            return; 
        } else {
            $_SESSION['error'] = "All fields are required";
            header('Location: edit.php?profile_id='.$_POST['profile_id']);
            return; 
        }
    }

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
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <?php 
            if (isset($_SESSION['error'])) {
                echo "<p class='bg-danger'>".$_SESSION['error']."</p>"; 
                unset($_SESSION['error']);
            }
        ?>
        <h1>New changes for <?= $row['first_name']." ".$row['last_name'] ?></h1>
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
            <textarea name="summary" id="sm" cols="30" rows="10"></textarea>
            <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
            <div class='d-flex flex-row position-relative m-auto w-100'>
                <input class="btn btn-success m-2 w-50" onclick="return validateEntry();" type="submit" name="Save" value="Save">
                <input class="btn btn-light m-2 w-50" type="submit" name="cancel" value="Cancel">
            </div>
        </form>
    </div>
    <script>
        function isEmpty(string) {
            // Replace the empty spaces    
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