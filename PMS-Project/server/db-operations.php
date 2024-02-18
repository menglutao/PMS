<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Handle CORS
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once realpath(__DIR__ . '/../vendor/autoload.php'); // Load environment variables, need to change path level if excutable file is not in the same level as vendor folder
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME']; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


# list all users
$sql_list_users = "SELECT * FROM patients";

# get user by name use like to search
$sql_get_user_by_name = "SELECT * FROM patients WHERE firstName LIKE ? OR lastName LIKE ?";


# delete other realted tables before deleting user
$sql_delete_user_in_user_roles_table = "DELETE FROM user_roles WHERE id = ?";
# delete user by id
$sql_delete_user_by_id = "DELETE FROM patients WHERE id=?";

# insert new user
$sql_add_user = "INSERT INTO patients (firstName, lastName, email, password, address, gender,birthDate, phone) VALUES (?, ?, ?,?,?,?,?,?)";

# update user by id
$sql_update_user_by_id = "UPDATE patients SET firstName=?, lastName=?,  email=?, password=?, address=?, gender=?,birthDate=?,phone=? WHERE id=?";

# get user password by username
// $sql_get_user_password_id_by_username = "SELECT id,password FROM patients WHERE firstName=? or lastName=?";
$sql_get_user_information_by_username = "SELECT * FROM patients WHERE firstName=? or lastName=?";

# get user role by id 
$sql_get_user_role_id_by_id = "SELECT role_id FROM user_roles WHERE id=?";

# get user by id
$sql_get_user_by_id = "SELECT * FROM patients WHERE id=?";






/*
# add new user
$sql_add_user = "INSERT INTO patients (firstName, lastName, email, password, address, gender,phone) VALUES (?, ?, ?,?,?,?,?)";
$stmt = $conn->prepare($sql_add_user);
$stmt->bind_param("sssssss", $firstName, $lastName,$email, $password, $address, $gender,$phone);


# TODO: update user by id
$sql_update_user_by_id = "UPDATE FROM patients WHERE id = ?";

// Example 1
$firstName = "Alice";
$lastName = "Smith";
$email = "alice@example.com";
$password = "alice123";
$address = "123 Apple St";
$gender = "Female";
$phone = "555-0101";
$stmt->execute();

// Example 2
$firstName = "Bob";
$lastName = "Johnson";
$email = "bob@example.com";
$password = "bobj789";
$address = "456 Orange Ave";
$gender = "Male";
$phone = "555-0202";
$stmt->execute();

// Example 3
$firstName = "Carol";
$lastName = "Davis";
$email = "carol@example.com";
$password = "carol456";
$address = "789 Banana Blvd";
$gender = "Female";
$phone = "555-0303";
$stmt->execute();

echo "New records created successfully";


$stmt->close();
$conn->close();

*/

?>