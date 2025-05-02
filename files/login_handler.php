<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if ($type === 'PWD') {
        $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
        $query = "SELECT * FROM users WHERE id_number = '$id_number'";
    } else {
        $license_id = mysqli_real_escape_string($conn, $_POST['license_id']);
        $query = "SELECT * FROM officials WHERE license_id = '$license_id' AND type = '$type'";
    }

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['type'] = $type;
            $_SESSION['user'] = $user;

            $redirect = ($type === 'PWD') ? 'pwd/pwd_dashboard.php' : 'medical/official_dashboard.php';

            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect password!'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found!'
        ]);
    }

    mysqli_close($conn);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
}
?>