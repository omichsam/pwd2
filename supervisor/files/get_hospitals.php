<?php
// include '../files/db_connect.php';

$sql = "SELECT id, name FROM hospitals ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$hospitals = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $hospitals[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($hospitals);
