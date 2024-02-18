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
    $symptomId = intval($input['symptoms'][0]);
    $intensity = intval($input['intensity']);
    $startDate = new DateTime($input['startDate']);
    $endDate = new DateTime($input['endDate']);
    $interval = $startDate->diff($endDate);
    $cycle_length = $interval->days;

    $user_id = intval($input['user_id']);
    $sql = "INSERT INTO menstrual_cycle (user_id, start_date, end_date, cycle_length) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $user_id, $input['startDate'], $input['endDate'], $cycle_length);

    if($stmt->execute()){
        $cycle_id = $conn->insert_id;
        $sql = "INSERT INTO symptoms (cycle_id, symptom_id, intensity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $cycle_id,$symptomId, $intensity);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cycle and symptoms added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert symptom table']);
        }
       
    }else{
        echo json_encode(['success' => false,'message'=>'insert into menstrual_cycle table is failure']);
    }

    
}
?>