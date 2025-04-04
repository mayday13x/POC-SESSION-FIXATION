<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Session Fixation POC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        
        <div class="nav">
            <a href="index.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
            <a href="attacker.php">Attacker Panel</a>
        </div>
        
        <div class="profile">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Role: <?php echo htmlspecialchars($role); ?></p>
            <p>User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            
            <?php if ($role === 'administrator'): ?>
                <div style="margin-top: 20px; padding: 15px; background-color: #ffeeba; border-radius: 5px;">
                    <h3>Admin Panel</h3>
                    <p>This is sensitive information only visible to administrators.</p>
                    <p>System settings access granted.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="info">
            <h3>Current Session Information:</h3>
            <p>Session ID: <?php echo session_id(); ?></p>
            <p>Session Data:</p>
            <pre><?php print_r($_SESSION); ?></pre>
            
          <!--  <?php if (isset($_SESSION['session_source'])): ?>
                <div style="margin-top: 15px; color: red;">
                    <strong>Warning:</strong> Your session was initialized via 
                    <?php echo htmlspecialchars($_SESSION['session_source']); ?>, 
                    which may indicate a session fixation attack.
                </div>
            <?php endif; ?>-->
        </div>
    </div>
</body>
</html>