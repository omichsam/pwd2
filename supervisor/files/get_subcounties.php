<?php
// require 'db_connection.php'; //

if (isset($_POST['county_id'])) {
    $county_id = intval($_POST['county_id']);

    $stmt = $conn->prepare("SELECT id, sub_county FROM sub_county WHERE county_id = ? ORDER BY sub_county ASC");
    $stmt->bind_param("i", $county_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Select Sub County --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['sub_county'] . '">' . htmlspecialchars($row['sub_county']) . '</option>';
    }
}
?>
