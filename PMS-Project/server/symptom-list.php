<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db-operations.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT symptom_types.* FROM symptom_types 
    join symptoms on symptom_types.symptom_id = symptoms.symptom_id
    Group BY symptom_id";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $symptoms = [];
    while ($row = $result->fetch_assoc()) {
        $symptoms[] = $row;
    }
    if(!empty($symptoms)){
        echo json_encode(['success' => true, 'symptoms' => $symptoms]);
    }else{
        echo json_encode(['success' => false,'message' => 'No symptom information found']);
    }
    



}
?>