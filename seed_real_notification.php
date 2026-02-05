<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;

$user = User::first();
if ($user) {
    Notification::updateOrCreate(
        ['user_id' => $user->id, 'title' => '🎉 Diwali 2026'],
        [
            'description' => 'Diwali dates have been successfully synced from your Google Calendar API.',
            'event_date' => '2026-11-08',
            'is_read' => 0
        ]
    );
    echo "SEED_SUCCESS";
} else {
    echo "NO_USER";
}
