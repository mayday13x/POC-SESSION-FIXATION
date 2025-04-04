<?php
// Check if a session ID was provided via URL parameter BEFORE starting the session
if (isset($_GET['PHPSESSID'])) {
    // Vulnerable code - allows session ID to be set from URL
    session_id($_GET['PHPSESSID']);
    
    // Add a flag to track how the session was set
    $session_source_flag = true;
}

// Start or resume a session
session_start();

// Set the session source flag if needed
if (isset($session_source_flag) && $session_source_flag) {
    $_SESSION['session_source'] = 'URL';
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Simple XSS vector - displays the message from URL parameter without sanitization
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Fixation POC</title>
    <!--link para a stylesheet local-->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Session Fixation POC</h1>
        
        <div class="nav">
            <a href="index.php">Home</a>
            <?php if (isLoggedIn()): ?>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="attacker.php">Attacker Panel</a>
        </div>
        
        <?php if (!empty($message)): ?>
        <div class="message">
            <!-- XSS Vulnerability - directly outputs user input without sanitization -->
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div>
            <h2>Welcome to the Session Fixation POC</h2>
            <p>This is a demonstration of a session fixation vulnerability.</p>
            
            <?php if (isLoggedIn()): ?>
                <p>You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                <?php if (isset($_SESSION['session_source'])): ?>
                    <p>Your session was set via: <strong><?php echo htmlspecialchars($_SESSION['session_source']); ?></strong></p>
                <?php endif; ?>
            <?php else: ?>
                <p>You are not logged in. <a href="login.php">Login here</a>.</p>
            <?php endif; ?>
        </div>
        
        <div class="info">
            <h3>Current Session Information:</h3>
            <p>Session ID: <?php echo session_id(); ?></p>
            <p>Session Data:</p>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    </div>
</body>
</html>