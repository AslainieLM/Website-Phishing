<?php
session_start();
require_once 'user.php';

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', 'admin','phishing_db');

// REGISTER USER
if (isset($_POST['register'])) {
  $userModel = new User();

  // receive all input values from the form
  $firstname = mysqli_real_escape_string($db, $_POST['fname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lname']);
  $username = mysqli_real_escape_string($db, $_POST['user']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['pass1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['pass2']);
  $contact = mysqli_real_escape_string($db, $_POST['phone']);

  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  $existingByUsername = $userModel->getRows(array(
    'where' => array('username' => $username),
    'return_type' => 'single'
  ));
  if ($existingByUsername) {
    array_push($errors, "Username already exists");
  }

  $existingByEmail = $userModel->getRows(array(
    'where' => array('email' => $email),
    'return_type' => 'single'
  ));
  if ($existingByEmail) {
    array_push($errors, "email already exists");
  }

  if (count($errors) == 0) {
    $memberData = array(
      'firstname' => $firstname,
      'lastname' => $lastname,
      'username' => $username,
      'email' => $email,
      'contact' => $contact,
      'password' => md5($password_1),
      'user_type' => 'user'
    );
    $userId = $userModel->insert($memberData);

    if ($userId) {
      $_SESSION['sessData'] = array(
        'status' => array(
          'type' => 'success',
          'msg' => 'Your account has been created. Please sign in.'
        )
      );
      header('location: login.php');
      exit;
    }

    array_push($errors, "Registration failed. Please try again.");
  }
}

function isAdmin()
{
  if (isset($_SESSION['username']) && $_SESSION['username']['user_type'] == 'admin' ) {
    return true;
  }else{
    return false;
  }
}

// USER LOGIN
if (isset($_POST['login'])) 
{
  // $passwordl=md5($_POST['pass']);
  $username = mysqli_real_escape_string($db, $_POST['user']);
  $password = md5(mysqli_real_escape_string($db, $_POST['pass'])); 

  if (count($errors) == 0) 
  {
    $query = "SELECT * FROM registration WHERE username='$username' AND password='$password'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) 
    {
      $logged_in_user = mysqli_fetch_assoc($results);
      if ($logged_in_user['user_type'] == 'admin') 
      {

        $_SESSION['username'] = $username;
        $_SESSION['success']  = "You are now logged in";
        $user_check= $_SESSION['user'];
        $sec_ql=mysqli_query($db, "SELECT username FROM registration WHERE username='$user_check'");
        $row=mysqli_fetch_assoc($sec_ql);
        $log_sec=$row['username']; 
        header('location: admin.php');    
      }
      else
      {
        $_SESSION['username'] = $username;
        $_SESSION['success']  = "You are now logged in";
        header('location: loggedin.php');
      }
    }
    else 
    {
      array_push($errors, "Wrong username/password combination");
    }
  }
}

function isLoggedIn()
{
  if (isset($_SESSION['username'])) {
    return true;
  }else{
    return false;
  }
}


if (isset($_POST['send'])) {
    // receive all input values from the form
  $rate = mysqli_real_escape_string($db, $_POST['experience']);
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $comment = mysqli_real_escape_string($db, $_POST['comments']);
  
    // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {

    $query = "INSERT INTO user_feedback (rate, name, email, comment) 
          VALUES('$rate', '$name','$email', '$comment')";
    mysqli_query($db, $query);
        echo"<script>alert('feedback has been sucessfully send')</script>";
}
}
?>

