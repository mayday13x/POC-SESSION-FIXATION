<?php
session_start();

// If already logged in, redirect to profile
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

// Define users array - in a real application, this would be in a database
$users = [
    'mayday' => [
        'id' => 1,
        'password' => 'mayday',  // In production, this would be hashed
        'role' => 'administrator'
    ],
    'user' => [
        'id' => 2,
        'password' => '1234',  // In production, this would be hashed
        'role' => 'regular'
    ]
];

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check if username exists and password matches
    if (array_key_exists($username, $users) && $users[$username]['password'] === $password) {
        // Store user data in session
        $_SESSION['user_id'] = $users[$username]['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
        

        // to prevent session fixation attacks
        // session_regenerate_id(true);
        
        // Redirect to profile page
        header('Location: profile.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Session Fixation POC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        
        <div class="nav">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="attacker.php">Attacker Panel</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
        
        <div class="info">
            <h3>Demo Credentials:</h3>
            <?php foreach ($users as $username => $data): ?>
                <p><b><?php echo ucfirst($data['role']); ?></b> - <?php echo htmlspecialchars($username); ?>:<?php echo htmlspecialchars($data['password']); ?></p>
            <?php endforeach; ?>
            
            <h3>Current Session Information:</h3>
            <p>Session ID: <?php echo session_id(); ?></p>
            <p>Session Data:</p>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    </div>
</body>
</html>