<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
error_reporting(E_ALL);



$user_file_path = './patients.json';
require_once './db-operations.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // $users = json_decode(file_get_contents($user_file_path));  

    if (isset($_GET['userName'])) { // the key 'userName' is defined in the frontend(url: userName = {} etc...) and needs to be the same here
        $user_name = $_GET['userName'];
        $firstname = "%$user_name%"; # % is a wildcard in SQL, so this will match any string that contains $user_name
        $lastname = "%$user_name%";
        $stmt = $conn->prepare($sql_get_user_by_name);
        $stmt->bind_param("ss", $firstname, $lastname);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result) {
            echo "Error fetching user: " . $conn->error;
            exit();
        }else{
            $user = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($user);
            $conn->close();
        }    
    }elseif(isset($_GET['user_id'])){
        $userId = $_GET['user_id'];
        # type check
        if(!is_numeric($userId)){
            echo json_encode(['message' => 'Error with user_id, user_id must be a number']);
            exit();
        }
        $stmt = $conn->prepare($sql_get_user_by_id);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            echo "Error fetching user: " . $conn->error;
            exit();
        }else{
            $user = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($user);
            $conn->close();
        }

    }else{
        $usersResult = $conn->query($sql_list_users);
        $users = $usersResult->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
        $conn->close();
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $input = json_decode(file_get_contents('php://input'), true); // get the new user data from the request body
    // $users = json_decode(file_get_contents($user_file_path));
    // check if input is empty
    if(empty($input['email']) || empty($input['password'])|| empty($input['lastName']) || empty($input['firstName'])){
        http_response_code(400);
        echo json_encode(["error" => "Please fill out all required fields"]);
        exit();
    }

    $stmt = $conn->prepare($sql_add_user);
    // get the values from the input array
    $firstName = $input['firstName'];
    $lastName = $input['lastName'];
    $email = $input['email'];
    $password = $input['password']; 
    $address = $input['address'];
    $gender = $input['gender'];
    $birthDate = $input['birthDate']; // Ensure the birthDate is in 'YYYY-MM-DD' format
    $phone = $input['phone'];
    // bind the parameters
    $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $password, $address, $gender, $birthDate, $phone);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'User added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to add user: ' . $conn->error]);
    }
    $stmt->close();

}elseif($_SERVER['REQUEST_METHOD'] === 'PUT'){
    $userId = $_GET['user_id'];
    $newUserData = json_decode(file_get_contents('php://input'), true); // get the new user data from the request body
    $stmt = $conn->prepare($sql_update_user_by_id);
    $email = $newUserData['email'];
    // sanitization check for email
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt->bind_param("ssssssssi", $newUserData['firstName'], $newUserData['lastName'], $email, $newUserData['password'], $newUserData['address'], $newUserData['gender'],$newUserData['birthDate'], $newUserData['phone'],$userId);
        if($stmt->execute()){
            echo json_encode(['message' => 'User updated successfully']);
            $stmt->close();
        }else{
            echo json_encode(['message' => 'Error in exectuing updating user sql statement: ' . $conn->error]);
        }
    } else {
        echo json_encode(['message' => 'Invalid email']);
    }

}elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    if(isset($_GET['user_id'])){
        $userId = $_GET['user_id'];
        # type check
        if(!is_numeric($userId)){
            echo json_encode(['message' => 'Error with user_id, user_id must be a number']);
            exit();
        }
        // preprare the delete statement
        $stmt1 = $conn->prepare($sql_delete_user_in_user_roles_table);
        $stmt1->bind_param("i", $userId);
        $stmt1->execute();

        $stmt2 = $conn->prepare($sql_delete_user_by_id);
        $stmt2->bind_param("i", $userId);
        // $result = $stmt->get_result(); // delete statement does not return a result thus this line is not needed
        if ($stmt2->execute()) {
            // Check if any row was actually deleted
            if ($stmt2->affected_rows > 0) {
                echo json_encode(['message' => 'User deleted successfully']);
                $stmt1->close();
                $stmt2->close();
            } else {
                echo json_encode(['message' => 'User not found, no user deleted']);
            }
        }else{
            echo json_encode(['message' => 'Error in exectuing deleting user sql statement: ' . $conn->error]);
        }
    }else{
        echo json_encode(['message' => 'Error with user_id, user_id must be a number']);
     }
    }



?>