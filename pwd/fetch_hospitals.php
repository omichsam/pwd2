<?php
include '../files/db_connect.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_hospitals'])) {
    $county = mysqli_real_escape_string($conn, $_POST['county']);
    $sql = "SELECT id, name, subcounty FROM hospitals WHERE county = '$county'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">-- Select Facility --</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $name = htmlspecialchars($row['name']);
            $subcounty = htmlspecialchars($row['subcounty']);
            echo "<option value='$id'>$name ($subcounty)</option>";
        }
    } else {
        echo '<option value="">No hospitals found for this county</option>';
    }
}
?>
