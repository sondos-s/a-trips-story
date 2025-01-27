<?php
session_start();
// Ensure the user is logged in
if(!isset($_SESSION['user_id'])){
    die('You must be logged in to change your password');
}

// Checking the request method to make sure the form was posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'UDB.php'; // Your Database connection file
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Fetch the current password from the database for the user
    $user_id = $_SESSION['user_id'];
    $stmt = $mysqli->prepare("SELECT passwordUser FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if(!password_verify($current_password, $password)){
        $_SESSION['error_message']  = 'Incorrect Current Password';
        header('Location: change_password.php');
        exit(); 
    }

    // Validate the new password length and contents
    if(strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)){
        $_SESSION['error_message']  = 'Password must be at least 8 characters long and contain both numbers and letters.';
        header('Location: change_password.php');
        exit(); 
    }

    // Check the new password and confirm password are the same
    if($new_password !== $confirm_password){
        $_SESSION['error_message'] = 'New Password and Confirmation must match!';
        header('Location: change_password.php');
        exit();
    }
    
    // Update the password in the database
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET passwordUser = ? WHERE id = ?");
    $stmt->bind_param("ss", $new_password_hash, $user_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = 'Password Changed Successfully';
    header('Location: Profile_FrontEnd.php');
    exit();
}
