<?php
class User implements JsonSerializable
{
  private $id;
  private $full_name;
  private $email;
  private $profile_pic;
  private $phone_number;
  private $country;
  private $dob;
  private $password;

  public function __construct($id, $full_name, $email, $profile_pic, $phone_number, $country, $dob, $password)
  {
    $this->id = $id;
    $this->full_name = $full_name;
    $this->email = $email;
    $this->profile_pic = $profile_pic;
    $this->phone_number = $phone_number;
    $this->country = $country;
    $this->dob = $dob;
    $this->password = $password;
  }

  // Getter methods
  public function getId()
  {
    return $this->id;
  }

  public function getFullName()
  {
    return $this->full_name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getProfile()
  {
    return $this->profile_pic;
  }

  public function getContact()
  {
    return $this->phone_number;
  }

  public function getCountry()
  {
    return $this->country;
  }

  public function getDOB()
  {
    return $this->dob;
  }

  public function getPassword()
  {
    return $this->password;
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'full_name' => $this->full_name,
      'email' => $this->email,
      'profile_pic' => $this->profile_pic,
      'phone_number' => $this->phone_number,
      'country' => $this->country,
      'dob' => $this->dob,
      'password' => $this->password,
    ];
  }
}

?>