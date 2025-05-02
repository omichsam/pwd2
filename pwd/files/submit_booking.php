<?php
// include 'files/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_id = intval($_POST['hospital_id']);
    $date = $_POST['assessment_date'];
    $time = $_POST['assessment_time'];
    $status = 'Pending'; // default
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $sql = "INSERT INTO assessment (hospital_id, assessment_date, assessment_time, status, note)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'issss', $hospital_id, $date, $time, $status, $note);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Booking successful!'); window.location='book_assessment.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
