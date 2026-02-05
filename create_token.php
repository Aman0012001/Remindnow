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

$email = 'test@example.com';
$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User;
    $user->name = 'Test User';
    $user->email = $email;
    $user->password = Hash::make('password');
    $user->user_role_id = 2; // Customer
    $user->save();
    echo 'Test user created!' . PHP_EOL;
}

// Generate Passport Token
$tokenResult = $user->createToken('Reymend Personal Access Client');
$token = $tokenResult->accessToken;

echo PHP_EOL . '========================================' . PHP_EOL;
echo 'BEARER TOKEN FOR POSTMAN:' . PHP_EOL;
echo '========================================' . PHP_EOL;
echo $token . PHP_EOL;
echo '========================================' . PHP_EOL;
echo '1. Copy this token.' . PHP_EOL;
echo '2. Go to Postman.' . PHP_EOL;
echo '3. Go to Authorization tab.' . PHP_EOL;
echo '4. Select "Bearer Token".' . PHP_EOL;
echo '5. Paste the token.' . PHP_EOL;
echo '6. Send the request again.' . PHP_EOL;
echo '========================================' . PHP_EOL;
