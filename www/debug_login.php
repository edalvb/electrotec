<?php
require __DIR__ . '/bootstrap.php';

use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

$username = $_GET['username'] ?? '';
$password = $_GET['password'] ?? '';

echo "<h1>Debug Login</h1>";
echo "<form method='GET'>";
echo "Username (RUC): <input type='text' name='username' value='" . htmlspecialchars($username) . "'><br>";
echo "Password: <input type='text' name='password' value='" . htmlspecialchars($password) . "'><br>";
echo "<button type='submit'>Check</button>";
echo "</form>";

if (!$username) {
    exit;
}

$config = new Config();
$pdoFactory = new PdoFactory($config);
$pdo = $pdoFactory->create();

echo "<h2>Results</h2>";

// 1. Find User
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red'>User '$username' NOT FOUND in 'users' table.</p>";
} else {
    echo "<p style='color:green'>User found: ID=" . $user['id'] . ", Tipo=" . $user['tipo'] . "</p>";
    
    // 2. Check Password
    if ($password) {
        if (password_verify($password, $user['password_hash'])) {
            echo "<p style='color:green'>Password '$password' is CORRECT.</p>";
        } else {
            echo "<p style='color:red'>Password '$password' is INCORRECT.</p>";
            
            // 3. Check if password should be RUC
            if (password_verify($username, $user['password_hash'])) {
                echo "<p style='color:blue'>Info: The stored password IS the RUC/Username ($username).</p>"; 
            } else {
                 echo "<p style='color:orange'>Info: The stored password is NOT the RUC/Username.</p>"; 
                 
                 // Check if it's default 'abc123'
                 if (password_verify('abc123', $user['password_hash'])) {
                     echo "<p style='color:purple'>Info: The stored password is the default 'abc123'. This suggests it is seeded data.</p>";
                 }
            }
        }
    }
}

// 4. Check Client
if ($user) {
    $stmt = $pdo->prepare('SELECT * FROM clients WHERE user_id = :uid');
    $stmt->execute(['uid' => $user['id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        echo "<p>Client Profile Found: Name=" . htmlspecialchars($client['nombre']) . ", RUC=" . htmlspecialchars($client['ruc']) . "</p>";
        if ($client['ruc'] !== $user['username']) {
             echo "<p style='color:red'>WARNING: Client RUC (" . $client['ruc'] . ") does NOT match User Username (" . $user['username'] . ").</p>";
        }
    } else {
         echo "<p>No Client profile found for this user.</p>";
    }
}
