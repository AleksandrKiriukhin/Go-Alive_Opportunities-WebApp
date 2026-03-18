<?php
// login.php
session_start();

// secure session cookie params (set before session_start if you set session cookie manually)
// session_set_cookie_params([...]);

// basic config
$adminFile = __DIR__ . '/admin.json';
$maxAttempts = 5;
$lockoutMinutes = 15;
$attemptsFile = __DIR__ . 'login_attempts.json';

// load admin
if (!file_exists($adminFile)) {
    http_response_code(500);
    exit('Admin data not found.');
}
$admin = json_decode(file_get_contents($adminFile), true);

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// simple rate-limit / lockout stored in a small JSON file
$attempts = file_exists($attemptsFile) ? json_decode(file_get_contents($attemptsFile), true) : [];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// clear old attempts
foreach ($attempts as $k => $entry) {
    if (isset($entry['last']) && (time() - $entry['last']) > ($lockoutMinutes * 60)) {
        unset($attempts[$k]);
    }
}

$key = $ip; // could also key by email+ip
$blocked = false;
if (isset($attempts[$key]) && $attempts[$key]['count'] >= $maxAttempts) {
    $blocked = true;
}

if ($blocked) {
    // too many attempts
    http_response_code(429);
    exit('Too many login attempts. Try again later.');
}

// verify email and password
if (hash_equals($admin['email'], $email) && password_verify($password, $admin['password_hash'])) {
    // success: reset attempts
    unset($attempts[$key]);
    file_put_contents($attemptsFile, json_encode($attempts));

    // hardening session
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'email' => $admin['email'],
        'logged_in_at' => time()
    ];

    // redirect to admin area
    header('Location: ../adminPages/admin-dashboard-page.php');
    exit;
} else {
    // failed: increment attempts
    if (!isset($attempts[$key])) {
        $attempts[$key] = ['count' => 1, 'last' => time()];
    } else {
        $attempts[$key]['count'] += 1;
        $attempts[$key]['last'] = time();
    }
    file_put_contents($attemptsFile, json_encode($attempts));
    http_response_code(401);
    exit('Invalid login.');
}

?>
