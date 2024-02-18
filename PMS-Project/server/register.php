<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db-operations.php';   
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents('php://input'), true); 
    // Validate and sanitize input data
    $name = trim($input['userName']);
    $email = trim($input['email']);
    $password = password_hash(trim($input['password']),PASSWORD_DEFAULT);
    $age = intval($input['age']); // Convert to integer
    $weight = trim($input['weight']);
    $height = trim($input['height']);

    // Check if any of the input data is empty
    if (empty($name) || empty($email) || empty($password) || empty($age) || empty($weight) || empty($height)) {
        echo json_encode(['success' => false, 'message' => 'All input fields are required.']);
        exit();
    }
    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM userInformation WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkEmailResult = $stmt->get_result();
    if ($checkEmailResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'A user with this email already exists.']);
    } else {
        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO userInformation (user_name, user_email, user_age, user_weight, user_height, user_password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $name, $email, $age, $weight, $height, $password);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User registered successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error registering user.']);
        }
    }
    
    $conn->close();
}
?>