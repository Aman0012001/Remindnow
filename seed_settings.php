<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\NotificationSetting;

$user = User::first();
if ($user) {
    NotificationSetting::updateOrCreate(
        ['user_id' => $user->id],
        ['daily_panchang' => 1, 'festival_notification' => 1]
    );
    echo "SUCCESS: Default notification settings created for " . $user->email;
} else {
    echo "ERROR: No user found in database.";
}
