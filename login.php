<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "credentials";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve entered username and password from POST data
$usernameInput = $_POST['username'];
$passwordInput = $_POST['password'];

// SQL query to retrieve user data
$sql = "SELECT username, password FROM users WHERE username = '$usernameInput'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, check password
    $row = $result->fetch_assoc();
    $storedPassword = $row['password'];

    if ($passwordInput == $storedPassword) {
        // Passwords match, set session variable and redirect to index.php
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['username'] = $usernameInput; // Store username in session
        header("Location: index.php");
        exit();
    } else {
        // Passwords don't match, display error message
        echo "Incorrect username or password. Please try again.";
    }
} else {
    // User not found, display error message
    echo "User not found. Please try again.";
}

// Close connection
$conn->close();

