<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob']; // already in YYYY-MM-DD if using <input type="date">
    $gender = $_POST['gender'];
    $occupation = $_POST['occupation'];
    $mobileNumber = $_POST['mobileNumber'];
    $type = $_POST['type'];
    $maritalStatus = $_POST['maritalStatus'];
    $educationLevel = $_POST['educationLevel'];
    $nextOfKinName = $_POST['nextOfKinName'];
    $nextOfKinMobile = $_POST['nextOfKinMobile'];
    $nextOfKinRelationship = $_POST['nextOfKinRelationship'];
    // $id_number = $_POST['id_number'];
    $county = $_POST['county'];
    $subcounty = $_POST['subcounty'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['password-confirm'];

    // Validate password match
    if ($password !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Prepare payload
    $payload = [
        "name" => $name,
        "gender" => $gender,
        "dob" => $dob,
        "maritalStatus" => $maritalStatus,
        "id_number" => $id_number,
        "occupation" => $occupation,
        "mobileNumber" => $mobileNumber,
        "email" => $email,
        "type" => $type,
        "nextOfKin" => [
            "name" => $nextOfKinName,
            "mobileNumber" => $nextOfKinMobile,
            "relationship" => $nextOfKinRelationship
        ],
        "address" => [
            "county" => $county,
            "subcounty" => $subcounty
        ],
        "educationLevel" => $educationLevel,
        "password" => $password
    ];

    // Send data to external API using cURL
    $ch = curl_init('http://38.242.200.57:3000/api/pwd/register');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Handle different cases based on HTTP status
    if ($httpCode === 200 || $httpCode === 201) {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } elseif ($httpCode === 400) {
        echo json_encode(["status" => "error", "message" => "User already exists."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Please try again."]);
    }
}
?>