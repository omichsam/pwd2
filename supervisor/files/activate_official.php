<?php
// include '../files/db_connect.php';

$response = [];

if (isset($_POST['id']) && isset($_POST['hospital_id'])) {
    $id = intval($_POST['id']);
    $hospital_id = intval($_POST['hospital_id']);

    $sql = "UPDATE officials SET hospital_id = '$hospital_id', active = 1 WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        $response = ['success' => true, 'message' => 'Official activated and hospital assigned.'];
    } else {
        $response = ['success' => false, 'message' => 'Database update failed: ' . mysqli_error($conn)];
    }
} else {
    $response = ['success' => false, 'message' => 'Missing parameters.'];
}

header('Content-Type: application/json');
echo json_encode($response);
