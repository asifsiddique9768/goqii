<?php

require_once './controllers/UserController.php';

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goqii";

// Set CORS headers
if (isset($_SERVER['HTTP_ORIGIN'])) {
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
      header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
  exit(0);
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Instantiate UserController with the database connection
$userController = new UserController($conn);

// Handle API request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['userId'])) {
    // Validate user ID
    $userId = filter_var($_GET['userId'], FILTER_VALIDATE_INT);

    if ($userId === false || $userId === null) {
      // Invalid user ID format
      echo json_encode(array('error' => 'Invalid user ID format'));
      exit; // Stop further execution
    }

    // User ID is valid, proceed to fetch user data
    $user = $userController->getUserById($userId);
    if ($user) {
      echo json_encode($user);
    } else {
      echo json_encode(array('error' => 'User not found'));
    }
  } else {
    $users = $userController->getAllUsers();
    echo json_encode($users);
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  $fullName = $data['full_name'];
  $email = $data['email'];
  $profile_pic = $data['profile_pic'];
  $phone_number = $data['phone_number'];
  $country = $data['country'];
  $password = $data['password'];
  $dob = $data['dob'];

  $newUser = $userController->createUser($fullName, $email, $profile_pic, $phone_number, $country, $dob, $password);
  echo json_encode($newUser);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);
  $userId = $_GET['userId'];
  $fullName = $data['full_name'];
  $email = $data['email'];
  $profile_pic = $data['profile_pic'];
  $phone_number = $data['phone_number'];
  $country = $data['country'];
  $password = $data['password'];
  $dob = $data['dob'];

  $updatedUser = $userController->updateUser($userId, $fullName, $email, $profile_pic, $phone_number, $country, $dob, $password);
  echo json_encode($updatedUser);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $userId = $_GET['userId'];
  $deleted = $userController->deleteUser($userId);
  if ($deleted) {
    echo json_encode(array('message' => 'User deleted successfully'));
  } else {
    echo json_encode(array('error' => 'User not found'));
  }
}

// Close connection
$conn->close();

?>