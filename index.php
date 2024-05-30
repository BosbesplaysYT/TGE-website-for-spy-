<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

// Get username from session
$username = $_SESSION['username'];

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "main";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is allowed to edit data
$can_edit_data = false;
$editable_users = array("Bosbes", "Thegamingempire"); // Define usernames with editing permission

if (in_array($username, $editable_users)) {
    $can_edit_data = true;
}

// Query to retrieve DQ points for all users
$sql_dq_points_all_users = "SELECT username, dq_points FROM dq_points";
$result_dq_points_all_users = $conn->query($sql_dq_points_all_users);

// Check for errors
if (!$result_dq_points_all_users) {
    echo "Error retrieving DQ points: " . $conn->error . "<br>";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TGE-Dashboard</title>
    <link rel="stylesheet" href="static/css/main-styles.css">
</head>
<body>
    <div class="background-image">
        <div class="index-container">
            <h2>Welcome, <?php echo $username; ?></h2> <!-- Display username -->
            
            <!-- Section: Rules -->
            <div class="box rules">
                <h3 class="fold-btn" onclick="toggleFold('rules-content')">Rules</h3>
                <div class="fold-content" id="rules-content">
                    <p>These are the rules for the overal TGE network, there may be specific rules for other services:</p>
                    <p>1. Be respectful: This means no mean, rude, or harassing comments. Treat others the way you want to be treated.</p>
                    <p>2. No inappropriate language: Keep use of profanity to a reasonable minimum. Any derogatory language towards any user is prohibited. You can swear in casual channels only, while the other channels should be kept free of any profane language.</p>
                    <p>3. No spamming: Do not send a lot of small messages right after each other. These disrupt the chat and make it hard to scroll through the server. Please keep your messages at least 5 words long while chatting.</p>
                    
                </div>
            </div>

            <!-- Section: General Information -->
            <div class="box general-info">
                <h3>General Information</h3>
                <p>Some general information about the dashboard:</p>
                <ul>
                    <li>Info 1: This dashboard allows you to manage DQ points.</li>
                    <li>Info 2: You can edit DQ points if you have permission.</li>
                    <li>Info 3: Contact admin for any assistance.</li>
                </ul>
            </div>
            
            <?php if ($can_edit_data): ?>
                <!-- Display edit controls for users with editing permission -->
                <form action="edit_dq_points.php" method="post">
                    <div class="box dq-points">
                        <h3>Edit DQ Points</h3>
                        <label for="selected_user">Select User:</label>
                        <select id="selected_user" name="selected_user">
                            <?php
                            // Loop through each user and display in dropdown
                            if ($result_dq_points_all_users->num_rows > 0) {
                                while($row = $result_dq_points_all_users->fetch_assoc()) {
                                    echo "<option value='" . $row["username"] . "'>" . $row["username"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <label for="dq_points">Modify DQ Points:</label>
                        <input type="number" id="dq_points" name="dq_points" value="0">
                        <button type="submit">Save Changes</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Display non-editable content for users without editing permission -->
                <div class="box dq-points">
                    <h3>DQ Points Status</h3>
                    <?php 
                    // Display DQ points for current user
                    if ($result_dq_points_all_users->num_rows > 0) {
                        while($row = $result_dq_points_all_users->fetch_assoc()) {
                            if ($row["username"] === $username) {
                                echo "<p id='dq-points-content'>" . $row["dq_points"] . "</p>";
                                break;
                            }
                        }
                    } else {
                        echo "<p id='dq-points-content'>0</p>"; // Default value if no DQ points found
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function toggleFold(elementId) {
            var content = document.getElementById(elementId);
            content.style.display === "none" ? content.style.display = "block" : content.style.display = "none";
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            // Fade out the index page content
            document.querySelector('.index-container').style.opacity = 0;
            // Fade in the index page content smoothly
            setTimeout(function(){
                document.querySelector('.index-container').style.opacity = 1;
            }, 700); // Adjust the duration as needed
        });
    </script>
</body>
</html>
