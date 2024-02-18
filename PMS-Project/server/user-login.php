<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 days
session_start();


//isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated']
// Check if user is already logged in
if (isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated']) {
    // User is already logged in, redirect or perform actions accordingly
    echo json_encode(["success" => true, "authenticated" => true, "message" => "User is already authenticated"]);
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once './db-operations.php';
require_once realpath(__DIR__ . '/../vendor/autoload.php'); // Load environment variables, need to change path level if excutable file is not in the same level as vendor folder
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


use Mailgun\Mailgun;

// generate a random code for the user for validation
function generateRandomCode() {
    return bin2hex(random_bytes(4));  
}


function sendEmailWithCode($email, $code) {
    // Use PHP's mail function or a library like PHPMailer to send the email
    $subject = "Your Login Verification Code";
    $message = "Your verification code is: " . $code;
   
    $API_KEY_MAILGUN = $_ENV['API_KEY_MAILGUN'];
    $DOMAIN_NAME = $_ENV['DOMAIN_NAME'];

    # Instantiate the client.
    // $mgClient = new Mailgun('$API_KEY_MAILGUN');
    $mgClient = Mailgun::create($API_KEY_MAILGUN);
    $domain = $DOMAIN_NAME;
    # Make the call to the client.
    $params = [
        'from'    => 'Excited User <mailgun@'. $DOMAIN_NAME . '>',
        'to'      => 'Baz <' . $email . '>',
        'subject' => 'Hello',
        'text'    => $code
    ];

    $result = $mgClient->messages()->send($domain, $params);
    // get the current time
    $timestamp = date('Y-m-d H:i:s');
    return $timestamp;

}


require_once './db-operations.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $input = json_decode(file_get_contents('php://input'), true); // get the  user data from the request body
    // check if input is empty
    $user_name =  $input['username'];
    $password = $input['password']; 
    
    //check username and password if are empty
    if(empty($username) || empty($password)){
        http_response_code(400);
        echo json_encode(["error" => "Please fill out all required fields"]);
        exit();
    }else { // check if username and password are correct
        // $sql_get_user_role_id_by_id = "SELECT role_id FROM user_roles WHERE id=?";
        $sql_get_userpassword_by_username = "SELECT user_password,user_id FROM userInformation WHERE user_name =?";
        $stmt = $conn->prepare($sql_get_userpassword_by_username);
        $stmt->bind_param("s",$user_name);
        $stmt->execute();
        $result = $stmt->get_result(); // return user's all information 

        if (!$result) {
            echo "The username or email doesn't exist: " . $conn->error;
            exit();
        }else{
            $user = $result->fetch_all(MYSQLI_ASSOC);
            $stored_password = $user[0]['user_password'];
            $user_id = $user[0]['user_id'];
            if(password_verify($password,$stored_password)){ 
                echo json_encode(["login" => true, "message" => "login success","user_id"=>$user_id]);
                $_SESSION['is_authenticated'] = true; // store user's authentication in session
                $_SESSION['username'] = $username;
                $code = generateRandomCode();
                //let's test with my personal email first  
                $recipientEmail = $_ENV['RECIPIENT_EMAIL']; 
                # send code try catch
                try {
                    sendEmailWithCode($recipientEmail, $code);
                } catch (Exception $e) {
                    echo json_encode(["success" => false, "message" => "An error occurred while sending the email"]);
                }
            }else{
            echo json_encode(["success" => false, "message" => "Username or password is incorrect"]);
        }
        }
    }
    $conn->close();
}


?>