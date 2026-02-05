<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::first();
if ($user) {
    echo "USER_FOUND: " . $user->email . "\n";
    echo "USER_ID: " . $user->id . "\n";
    echo "IS_ACTIVE: " . $user->is_active . "\n";
    echo "IS_DELETED: " . $user->is_deleted . "\n";
    $token = $user->createToken('ManualAuthTest')->accessToken;
    echo "TOKEN: " . $token . "\n";
} else {
    echo "NO_USER_FOUND";
}
