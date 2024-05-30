<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

// Check if the user has administrative rights
$editable_users = array("Bosbes", "Thegamingempire");
if (!in_array($_SESSION['username'], $editable_users)) {
    header("Location: index.php");
    exit();
}

// Get data from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $selected_user = $_POST['selected_user'];
    $dq_points = $_POST['dq_points'];

    // Update DQ points in the database
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "main";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_update_dq_points = "UPDATE dq_points SET dq_points = '$dq_points' WHERE username = '$selected_user'";
    if ($conn->query($sql_update_dq_points) === TRUE) {
        echo "DQ points updated successfully";
    } else {
        echo "Error updating DQ points: " . $conn->error;
    }

    $conn->close();
}

