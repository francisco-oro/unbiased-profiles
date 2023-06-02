<?php
    session_start();
    require_once('pdo.php'); 

    class profileTable 
    {
        public function ($this, $array){
            foreach ($variable as $key => $value) {
                # code...
            }
        }

        private function display(array $row){
            echo "<tr class='bg-white'><td>";
            echo(htmlentities($row['make']));
            echo "</td><td>";
            echo (htmlentities($row['model']));
            echo "</td><td>";
            echo (htmlentities($row['year']));
            echo "</td><td>";
            echo (htmlentities($row['mileage']));
            echo "</td><td>";
            echo "<a class='link-primary' href='edit.php?auto_id=".$row['auto_id']."'>Edit</a> / ";
            echo "<a class='link-primary' href='delete.php?auto_id=".$row['auto_id']."'>Delete</a>";
            echo "</td></tr>";
        }

        private function

        public function displayRecords(){
            require_once('pdo.php'); 
            $stmt = $pdo->query('SELECT auto_id, make, year, mileage, model FROM autos ORDER BY make');
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<table border='2' class='table'>"; 
                echo "<thead class='thead-dark'><tr><th scope='col'>Make</th><th scope='col'>Model</th><th scope='col'>Year</th><th scope='col'>Mileage</th><th scope='col'>Action</th></tr></thead>"; 
                echo "<tbody>";
                displayRow($row); 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    displayRow($row);
                }
                echo "</tbody>"; 
                echo  "</table>";
            } else {
                echo "<p>No rows found</p><br/>"; 
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
</head>
<body>
    <h1>Francisco Abimael Oro Estrada's Resume Registry</h1>
</body>
</html>