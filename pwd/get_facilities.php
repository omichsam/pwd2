
<?php
// include '../files/db_connect.php';

if (isset($_POST['county'])) {
    $county = mysqli_real_escape_string($conn, $_POST['county']);
    $query = "SELECT id, name FROM hospitals WHERE county = '$county' ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    echo '<option value="">-- Select Facility --</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
}
?>