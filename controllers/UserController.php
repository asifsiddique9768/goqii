<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  // Retrieve all users
  public function getAllUsers()
  {
    $users = array();

    $sql = "SELECT * FROM users";
    $result = $this->conn->query($sql);

    if (!$result) {
      throw new Exception("Error retrieving users: " . $this->conn->error);
    }

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $users[] = new User($row['id'], $row['full_name'], $row['email'], $row['profile_pic'], $row['phone_number'], $row['country'], $row['dob'], $row['password']);
      }
    }

    return $users;
  }

  // Retrieve user data by ID
  public function getUserById($userId)
  {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
      throw new Exception("Error preparing statement: " . $this->conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
      throw new Exception("Error retrieving user: " . $this->conn->error);
    }

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return new User($row['id'], $row['full_name'], $row['email'], $row['profile_pic'], $row['phone_number'], $row['country'], $row['dob'], $row['password']);
    } else {
      return null;
    }
  }

  // Create a new user
  public function createUser($fullName, $email, $profile_pic, $phone_number, $country, $dob, $password)
  {
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (full_name, email, profile_pic, phone_number, country, dob, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    // print_r($sql);
    if (!$stmt) {
      throw new Exception("Error preparing statement: " . $this->conn->error);
    }
    
    $stmt->bind_param("sssssss", $fullName, $email, $profile_pic, $phone_number, $country, $dob, $password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
      $newUserId = $stmt->insert_id;
      return $this->getUserById($newUserId);
    } else {
      throw new Exception("Error creating user: " . $stmt->error);
    }
  }

  // Update user data by ID
  public function updateUser($userId, $fullName = null, $email = null, $profile_pic = null, $phone_number = null, $country = null, $dob = null, $password = null)
    {
        $setStatements = [];
        $types = '';
        $values = [];

        if ($fullName !== null) {
            $setStatements[] = "full_name = ?";
            $types .= 's';
            $values[] = $fullName;
        }
        if ($email !== null) {
            $setStatements[] = "email = ?";
            $types .= 's';
            $values[] = $email;
        }
        if ($profile_pic !== null) {
            $setStatements[] = "profile_pic = ?";
            $types .= 's';
            $values[] = $profile_pic;
        }
        if ($phone_number !== null) {
            $setStatements[] = "phone_number = ?";
            $types .= 's';
            $values[] = $phone_number;
        }
        if ($country !== null) {
            $setStatements[] = "country = ?";
            $types .= 's';
            $values[] = $country;
        }
        if ($dob !== null) {
            $setStatements[] = "dob = ?";
            $types .= 's';
            $values[] = $dob;
        }
        if ($password !== null) {
            $setStatements[] = "password = ?";
            $types .= 's';
            $values[] = $password;
        }

        if (empty($setStatements)) {
            return null;
        }

        $sql = "UPDATE users SET " . implode(", ", $setStatements) . " WHERE id = ?";
        $types .= 'i';
        $values[] = $userId;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return $this->getUserById($userId);
        } else {
            return null;
        }
    }



  // Delete user by ID
  public function deleteUser($userId)
  {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
      throw new Exception("Error preparing statement: " . $this->conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
      return true;
    } else {
      throw new Exception("Error deleting user: " . $stmt->error);
    }
  }
}

?>