<?php
include 'database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

function userRegistration() {
    // Initialization
    $name = "";
    $email = "";
    $phone = "";
    $password = "";
    $cpassword = "";
    // Error messages
    $error = false;
    $nameError = "";
    $emailError = "";
    $phoneError = "";
    $passwordError = "";
    $confirmPasswordError = "";

    // Handle signup form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars($_POST['userName']);
        $email = htmlspecialchars($_POST['userEmail']);
        $phone = htmlspecialchars($_POST['userPhone']);
        $password = htmlspecialchars($_POST['password']);
        $cpassword = htmlspecialchars($_POST['confirmPassword']);

        // Validate name
        if (empty($name)) {
            $nameError = "Name is required";
            $error = true;
        }
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format";
            $error = true;
        }
        if (!checkUserEmail($email)) {
            $emailError = "Email is already used";
            $error = true;
        }
        // Validate phone number
        if (!preg_match("/^(\+60|0)(1[0-9]{1}|([2-9]{1}[0-9]{1}))?-?\d{7,8}$/", $phone)) {
            $phoneError = "Phone format is not valid";
            $error = true;
        }
        // Validate password
        if (strlen($password) < 8) {
            $passwordError = "Password must have at least 8 characters";
            $error = true;
        }
        // Validate confirm password
        if ($password !== $cpassword) {
            $confirmPasswordError = "Passwords do not match";
            $error = true;
        }

        if (!$error) {
            if (addDatabaseUser($name, $email, $phone, $password)) {
                $sessionData = getLoginSession($email, $password);
                if ($sessionData) {
                    // Clear the output buffer and send JSON response
                    ob_end_clean();
                    return json_encode([
                        'success' => true,
                        'message' => "Account registered and login successfully. User Name: $name, User Email: $email",
                        'session' => $sessionData
                    ]);
                } else {
                    // Handle login session error
                    ob_end_clean();
                    return json_encode([
                        'success' => false,
                        'message' => "Unfortunately, login failed. Please try to login manually."
                    ]);
                }
            }
        } else {
            ob_end_clean();
            return json_encode([
                'success' => false,
                'nameError' => $nameError,
                'emailError' => $emailError,
                'phoneError' => $phoneError,
                'passwordError' => $passwordError,
                'confirmPasswordError' => $confirmPasswordError
            ]);
        }
    }
}

function userLogin() {
    // Error messages
    $emailError = "";
    $passwordError = "";

    // Handle login form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = htmlspecialchars($_POST['userEmail']);
        $password = htmlspecialchars($_POST['password']);

        $sessionData = getLoginSession($email, $password);
        if ($sessionData) {
            // Clear the output buffer and send JSON response
            ob_end_clean();
            return json_encode([
                'success' => true,
                'message' => "Login successful."
            ]);
        } else {
            // Handle login session error
            ob_end_clean();
            return json_encode([
                'success' => false,
                'message' => "Login failed. Please try again or register an account."
            ]);
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form_id'] == 'register') {
        $response = userRegistration();
    } elseif ($_POST['form_id'] == 'login') {
        $response = userLogin();
    }
    echo $response; // Output the JSON response
}
