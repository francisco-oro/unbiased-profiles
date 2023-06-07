<?php
    session_start();
    // Render the database content to the web application 
    class profilesTable 
    {
        // The $admin variable is to tell the function wether the user has logged in or not
        // The 'Action' column will be displayed only for registered users
        private function rowHeadings(bool $admin){
            echo("<thead class='thead-dark'>
                    <tr>
                        <th scope='col'>Name</th>
                        <th scope='col'>Headline</th>");
            if ($admin) {
                echo "<th scope='col'>Action</th></tr>"; 
            }
            echo("</thead>");
        }
        private function displayRow(array $row, bool $admin){
            echo "<tr class='bg-white'><td>";
            echo '<a href="view.php?profile_id='.$row['profile_id'].'">';
            echo(htmlentities($row['first_name'])." ".htmlentities($row['last_name']));
            echo '</a>'; 
            echo "</td><td>";
            echo (htmlentities($row['headline']));
            if ($admin) {
                echo "</td><td>";
                echo "<a class='link-primary' href='edit.php?profile_id=".$row['profile_id']."'>Edit</a> / ";
                echo "<a class='link-primary' href='delete.php?profile_id=".$row['profile_id']."'>Delete</a>";
            }
            echo "</td></tr>";
        }
        public function displayRecords(bool $admin){
            require_once('pdo.php'); 
            $stmt = $pdo->query('SELECT profile_id, first_name, last_name, headline FROM profiles ORDER BY first_name');
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<table border='2' class='table table-hover'>"; 
                $this->rowHeadings($admin);
                echo "<tbody>";
                $this->displayRow($row, $admin);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->displayRow($row, $admin);
                }
                echo "</tbody>"; 
                echo  "</table>";
            } else {
                echo "<p>No rows found</p><br/>"; 
            }

            if($admin){
                echo '<a style="color:blue;" href="add.php">Add New Entry</a> <br/>';
                echo '<a style="color:blue;" href="logout.php">Logout</a>';
            } else {
                echo "<a style='color:blue;' href='login.php'>Please log in</a>";
            }
        }
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
    <?php require_once('utilities.php'); ?> 
</head>
<body>
    <div class="text-white text-center w-100 p-5">
        <h1>Francisco Oro's Resume Registry</h1>
        <?php
            flashMessage();
            $table = new profilesTable;
            $table->displayRecords(isset($_SESSION['user_id']));
        ?>
    </div>
</body>
</html>