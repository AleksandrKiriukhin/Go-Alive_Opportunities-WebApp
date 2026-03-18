<?php
// create_admin.php (run once, then delete or protect)
if (php_sapi_name() !== 'cli') {
    // optional: restrict to CLI so it can't be hit from web
    http_response_code(403);
    exit('Forbidden');
}

$email = 'volunteers@goalive.eu';
$password = 'Volunteers2025@';

// Use password_hash (Bcrypt/Argon2 depending on PHP build)
$hash = password_hash($password, PASSWORD_DEFAULT); // PASSWORD_DEFAULT is safe

$data = [
    'email' => $email,
    'password_hash' => $hash,
    'created_at' => date('c')
];

// file_put_contents(__DIR__ . '/admin.json', json_encode(...));
file_put_contents(__DIR__ . '/admin.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Admin created. Password hash saved to data/admin.json\n";

?>