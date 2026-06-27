<?php
$url = 'https://valryze-production-bt8kkz.laravel.cloud/api';
// Login
$ch = curl_init("$url/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'siti.rahayu@example.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
echo "Login Response: " . print_r($data, true) . "\n";
if (isset($data['token'])) {
    $token = $data['token'];
    $ch = curl_init("$url/profile");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    $profile = curl_exec($ch);
    curl_close($ch);
    echo "Profile Response: " . print_r(json_decode($profile, true), true) . "\n";
}
