<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;

$user = User::first();
if ($user) {
    Notification::create([
        'user_id' => $user->id,
        'title' => 'Welcome!',
        'description' => 'Thank you for joining our app.',
        'is_read' => 0
    ]);
    Notification::create([
        'user_id' => $user->id,
        'title' => 'Upcoming Festival',
        'description' => 'Diwali is just around the corner.',
        'is_read' => 0
    ]);
    echo "SUCCESS: 2 notifications created for " . $user->email;
} else {
    echo "ERROR: No user found in database.";
}
