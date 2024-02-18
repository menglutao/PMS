<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 days
session_start();

//isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated']
if (true) {
    // User is already logged in, redirect or perform actions accordingly
    echo json_encode(["success" => true, "message" => "User is already validated"]);
    exit();
}


ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once './db-operations.php';
require_once realpath(__DIR__ . '/../vendor/autoload.php'); // Load environment variables, need to change path level if excutable file is not in the same level as vendor folder
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Mailgun\Mailgun;



if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $input = json_decode(file_get_contents('php://input'), true); // get the  user data from the request body
    $validationcode = $input['validationcode'];
    $true_code = $input['true_code'];
    // echo $validationcode;
    $validationcode = is_null($input['validationcode']) ? null : $input['validationcode'];
    $true_code = is_null($input['true_code']) ? null : $input['true_code'];
    // echo $validationcode;

    if ($validationcode === $true_code) {
        $_SESSION['is_validated'] = true;
        echo json_encode(["success" => true]);
    }else{
        $_SESSION['is_validated'] = false;
        echo json_encode(["success" => false]);
    }



}



?>