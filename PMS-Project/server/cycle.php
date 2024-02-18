<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db-operations.php';
if ($_SERVER["REQUEST_METHOD"] === "GET") {
   $user_name = $_GET['user_name'];
    if (isset($_GET['user_name'])) { 

        $get_user_id_by_user_name = "SELECT user_id FROM userInformation WHERE user_name = ?";
        $stmt1 = $conn->prepare($get_user_id_by_user_name);
        $stmt1->bind_param("s", $user_name);
        $stmt1->execute();
        $result = $stmt1->get_result();
        $user_id = $result->fetch_assoc()['user_id'];

        // $user_id = 13;
        $sql_get_cycle_and_symptoms_by_user_id = "SELECT DISTINCT menstrual_cycle.*, symptoms.cycle_id,symptoms.intensity, symptom_types.*
            FROM menstrual_cycle
            JOIN symptoms ON menstrual_cycle.cycle_id = symptoms.cycle_id
            JOIN symptom_types ON symptoms.symptom_id = symptom_types.symptom_id
            WHERE menstrual_cycle.user_id = ?
            ORDER BY menstrual_cycle.start_date DESC";
        $stmt = $conn->prepare($sql_get_cycle_and_symptoms_by_user_id);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cycles = [];
        
        while ($row = $result->fetch_assoc()) {
            $cycles[] = $row;
        }

        // make predictions based on previous period start date
        if(!empty($cycles)){
            $interCycleIntervals = [];
            // Calculate inter-cycle intervals (the time between the start of consecutive cycles)
            for ($i = 1; $i < count($cycles); $i++) {
                $currentStartDate = new DateTime($cycles[$i]['start_date']);
                $previousStartDate = new DateTime($cycles[$i - 1]['start_date']);
                $interval = $previousStartDate->diff($currentStartDate);
                $interCycleIntervals[] = $interval->days;
            }

            // Calculate the average inter-cycle length
            if (!empty($interCycleIntervals)) {
                $averageInterCycleLength = array_sum($interCycleIntervals) / count($interCycleIntervals);
                $averageInterCycleLengthRounded = round($averageInterCycleLength);

                // Predict the next start date based on the most recent cycle
                $mostRecentCycleStartDate = new DateTime($cycles[0]['start_date']);
                $predictionDate = $mostRecentCycleStartDate->add(new DateInterval("P{$averageInterCycleLengthRounded}D"));
                $predictionMessage = "Based on your cycle history, your next period is predicted to start around " . $predictionDate->format('Y-m-d');
            } else {
                // Not enough data to predict the next cycle's start date
                $predictionMessage = "Not enough data to predict the next period start date.";
            }
            echo json_encode(['success' => true, 'cycles' => $cycles,'prediction' => $predictionMessage,'user_id' => $user_id]);
        }else {
            echo json_encode(['success' => false,'message' => 'No cycle found','user_id' => $user_id]);
            }
    }

}
?>