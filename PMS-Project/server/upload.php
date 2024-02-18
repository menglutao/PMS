<?php


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
error_reporting(E_ALL);


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_FILES['file'])){
        $file = $_FILES['file'];
        $name = $file['name'];
        $tmpName = $file['tmp_name'];
        $error = $file['error'];
        $size = $file['size'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); 
        $maxSize = 5 * 1024 * 1024; // 5 MB

        try {
            if (!isset($_FILES['file'])) {
                throw new Exception("No file uploaded.");
            }
        
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("There was an error uploading the file.");
            }
            // check if the file type is allowed
            if (!in_array($_FILES['file']['type'], $allowedTypes)) {
                throw new Exception("File type is not allowed.");
            }
        
            # Randomize the file name
            $filename = substr(str_shuffle(MD5(microtime())), 0, 10) . '.' . $ext;
            $uploadPath = __DIR__ . '/uploads/' . $filename; 
            // echo "Target path: " . $uploadPath . "<br>";
        
            # Move the file to a designated location
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                throw new Exception("Failed to move uploaded file.");
            }
        
            # File upload success
            echo json_encode([
                "error" => false,
                "message" => "File uploaded successfully",
            ]);
        
        } catch (Exception $e) {
            # Handle any exceptions/errors
            echo json_encode([
                "error" => true,
                "message" => $e->getMessage()
            ]);
        }
        
        
    }

}









?>