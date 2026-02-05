<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Create Language if missing
if (!DB::table('languages')->where('lang_code', 'en')->exists()) {
    DB::table('languages')->insert([
        'title' => 'English',
        'lang_code' => 'en',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}

if (!User::where('email', 'test@example.com')->exists()) {
    $u = new User;
    $u->name = 'Test User';
    $u->email = 'test@example.com';
    $u->password = Hash::make('password');
    $u->user_role_id = 2; // Customer
    $u->save();
    echo 'Test user created! Email: test@example.com, Password: password' . PHP_EOL;
} else {
    echo 'Test user already exists.' . PHP_EOL;
}
