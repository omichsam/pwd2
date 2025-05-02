<?php
// include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = intval($data['id']);
    $name = mysqli_real_escape_string($conn, $data['name']);
    $county = mysqli_real_escape_string($conn, $data['county']);
    $subcounty = mysqli_real_escape_string($conn, $data['subcounty']);
    $address = mysqli_real_escape_string($conn, $data['address']);

    $update = "UPDATE hospitals SET name='$name', county='$county', subcounty='$subcounty', address='$address' WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        echo json_encode(['success' => true, 'message' => 'Hospital updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
}
?>