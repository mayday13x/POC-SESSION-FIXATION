<?php
// Start a new session or resume existing
session_start();

// Function to regenerate session ID securely
function regenerateSession() {
    // Clear old session data
    $_SESSION = array();
    
    // Generate new session ID and delete old session
    session_regenerate_id(true);
    
    // Set secure cookie parameters
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        $params["lifetime"],
        $params["path"],
        $params["domain"],
        true,  // Secure flag - only send over HTTPS
        true   // HttpOnly flag - prevent JavaScript access
    );
}

// Check if this is a login action
$login_action = isset($_GET['action']) && $_GET['action'] === 'login';

if ($login_action) {
    // Simulate login
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'secure_user';
    
    // Critical: Regenerate session ID after login
    regenerateSession();
    
    echo "<p>Logged in securely! Session ID has been regenerated.</p>";
}

// Never accept session IDs from URLs
if (isset($_GET['PHPSESSID'])) {
    // Log potential attack attempt
    error_log("Potential session fixation attempt detected: " . $_SERVER['REMOTE_ADDR']);
    
    // Regenerate session to protect the user
    regenerateSession();
    
    echo "<p style='color:red'>Warning: Session ID in URL detected. This could be a session fixation attempt.</p>";
}

// Sanitize all outputs to prevent XSS
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Example - Session Fixation Prevention</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Secure Example - Session Fixation Prevention</h1>
        
        <?php if (!empty($message)): ?>
        <div class="message">
            <!-- Properly sanitized output -->
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div class="info">
            <h2>Security Features Implemented:</h2>
            <ul>
                <li>Session ID regeneration after login</li>
                <li>Rejection of session IDs from URL parameters</li>
                <li>Secure and HttpOnly cookie flags</li>
                <li>Output sanitization to prevent XSS</li>
                <li>Logging of potential session fixation attempts</li>
            </ul>
            
            <p>
                <a href="secure_example.php?action=login">Simulate Secure Login</a> 
                (Notice how the session ID changes after login)
            </p>
        </div>
        
        <div class="info">
            <h3>Current Session Information:</h3>
            <p>Session ID: <?php echo session_id(); ?></p>
            <p>Session Data:</p>
            <pre><?php print_r($_SESSION); ?></pre>
            
            <h3>Important Code Snippets:</h3>
            <pre style="background: #f8f9fa; padding: 10px;">
// Regenerate session ID after login
session_regenerate_id(true);

// Set secure cookie parameters
setcookie(
    session_name(),
    session_id(),
    $params["lifetime"],
    $params["path"],
    $params["domain"],
    true,  // Secure flag - only send over HTTPS
    true   // HttpOnly flag - prevent JavaScript access
);

// Never accept session IDs from URLs
if (isset($_GET['PHPSESSID'])) {
    // Log potential attack attempt
    error_log("Potential session fixation attempt detected");
    // Regenerate session
    regenerateSession();
}

// Sanitize all outputs
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
            </pre>
        </div>
    </div>
</body>
</html>