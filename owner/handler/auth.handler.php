<?php 
session_start();
require_once("../db/config.php");
require_once("../utils/helpers.php");

// Handle REGISTERATION
if(isset($_POST['signup'])) {
  if(!$_POST['email']) {
    setAlert("Please enter your email address");
    redirect($_SERVER['HTTP_REFERER'] ?? "../signup");
  }

  if(!$_POST['username']) {
    setAlert("Please enter your username");
    redirect($_SERVER['HTTP_REFERER'] ?? "../signup");
  }

  if(!$_POST['password']) {
    setAlert("Please enter your password");
    redirect($_SERVER['HTTP_REFERER'] ?? "../signup");
  }

  // SANITIZE VALUES
  $email = sanitizer(trim($_POST['email']));
  $username = sanitizer(trim($_POST['username']));
  $password = password_hash(sanitizer($_POST['password']), PASSWORD_BCRYPT);
  $id = uniqid("ADM_");

  try {
    // Check if email exists
    $query = "SELECT * FROM admin WHERE email = ?";
    $result = $connect->prepare($query);
    $result->execute([$email]);

    if($result->rowCount()) throw new Exception("Email already exists");

    $query = "INSERT INTO admin(id, email, username, password) VALUES (:id, :email, :username, :password)";
    $result = $connect->prepare($query);
    $result->execute([
      "id" => $id,
      "email" => $email,
      "username" => $username,
      "password" => $password,
    ]);
  
    if(!$result->rowCount()) throw new Exception("Failed to create account");

    setAlert("Account Created!!", "success");
    redirect("../");
  }
  catch(Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../signup");
  }
}

// Handle LOGIN
elseif (isset($_POST['login'])) {
  if(!$_POST['email']) {
    setAlert("Please enter your email");
    redirect($_SERVER['HTTP_REFERER'] ?? "../");
  }

  if(!$_POST['password']) {
    setAlert("Please enter your password");
    redirect($_SERVER['HTTP_REFERER'] ?? "../");
  }

  $email = sanitizer($_POST['email']);
  $password = sanitizer($_POST['password']);

  try {
    // Check if there's user
    $query = "SELECT * FROM admin WHERE email = ?";
    $result = $connect->prepare($query);
    $result->execute([$email]);

    if(!$result->rowCount()) throw new Exception("Incorrect Credentials");

    // Check if password match
    $user = $result->fetch(); 

    if(!password_verify($password, $user['password'])) throw new Exception("Incorrect Credentials");

    // Set ADMIN SESSION
    $_SESSION['ADMIN_SESSION'] = json_encode($user);

    setAlert("Logged !n!", "success");
    redirect("../dashboard");
  }
  catch(Exception $e) {
    setAlert($e->getMessage());
    redirect($_SERVER['HTTP_REFERER'] ?? "../");
  }
}

// Handle LOGOUT
elseif (isset($_POST['logout'])) {
  session_destroy();
  redirect("../");
}

// Handle BAD REQUEST
else {
  redirect($_SERVER['HTTP_REFERER'] ?? "../");
}