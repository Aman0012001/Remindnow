<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'test@example.com';
$user = User::where('email', $email)->first();

if ($user) {
    $tokenResult = $user->createToken('Reymend Personal Access Client');
    $token = $tokenResult->accessToken;
    file_put_contents('bearer_token.txt', $token);
    echo 'Token successfully written to bearer_token.txt' . PHP_EOL;
} else {
    echo 'User not found. Run create_user.php first.' . PHP_EOL;
}
