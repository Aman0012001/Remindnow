<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/health', [App\Http\Controllers\api\HealthController::class, 'check']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['GuestApi'])->group(function () {
    Route::match(['get'], 'notification-list-public', [App\Http\Controllers\api\UsersController::class, 'notificationsListPublic']);
    Route::match(['get'], 'get-notifications-public', [App\Http\Controllers\api\UsersController::class, 'getManageNotificationPublic']);
    Route::match(['get'], 'cms/{slug}', [App\Http\Controllers\api\UsersController::class, 'cms']);
    Route::match(['get'], 'faqs', [App\Http\Controllers\api\UsersController::class, 'faqs']);
    Route::match(['get'], 'state', [App\Http\Controllers\api\UsersController::class, 'statesList']);

    Route::match(['get', 'post'], 'signup', [App\Http\Controllers\api\UsersController::class, 'signup']);
    Route::match(['get', 'post'], 'check-verification-code/{validate_string}', [App\Http\Controllers\api\UsersController::class, 'check_verification_token']);
    Route::match(['get', 'post'], 'login', [App\Http\Controllers\api\UsersController::class, 'login']);
    Route::match(['get'], 'temples', [App\Http\Controllers\api\UsersController::class, 'temples']);


    Route::match(['get'], 'festival-detail/{id}', [App\Http\Controllers\api\UsersController::class, 'festivalDetail']);

    Route::match(['get'], 'tiptap-list', [App\Http\Controllers\api\UsersController::class, 'tiptapIndex']);

    Route::post('/panchang', [App\Http\Controllers\api\UsersController::class, 'getPanchang']);

    // Facebook Authentication (Public)
    Route::get('facebook/redirect', [App\Http\Controllers\api\FacebookAuthController::class, 'redirectToFacebook']);
    Route::get('facebook/callback', [App\Http\Controllers\api\FacebookAuthController::class, 'handleFacebookCallback']);
    Route::post('facebook/login-token', [App\Http\Controllers\api\FacebookAuthController::class, 'loginWithToken']);
});


Route::middleware(['AuthApi'])->group(function () {
    Route::match(['get'], 'festivals', [App\Http\Controllers\api\UsersController::class, 'festivals']);
    Route::match(['get'], 'festivalstab', [App\Http\Controllers\api\UsersController::class, 'festivalstab']);
    Route::match(['post'], 'update-profile', [App\Http\Controllers\api\UsersController::class, 'updateProfile']);
    Route::match(['post'], 'delete-account', [App\Http\Controllers\api\UsersController::class, 'deleteAccount']);
    Route::match(['post'], 'create-reminders', [App\Http\Controllers\api\UsersController::class, 'createReminder']);
    Route::match(['get'], 'get-reminders', [App\Http\Controllers\api\UsersController::class, 'getReminder']);
    Route::match(['get'], 'delete-reminders/{id}', [App\Http\Controllers\api\UsersController::class, 'deleteReminder']);

    Route::match(['get'], 'get-notifications', [App\Http\Controllers\api\UsersController::class, 'getManageNotification']);
    Route::match(['post'], 'manage-notifications', [App\Http\Controllers\api\UsersController::class, 'updateManageNotification']);
    Route::match(['get'], 'notification-list', [App\Http\Controllers\api\UsersController::class, 'notificationsList']);

    // ===== NEW FESTIVAL API ROUTES =====
    // Festival Management
    Route::get('festivals/all', [App\Http\Controllers\api\FestivalController::class, 'index']);
    Route::get('festivals/upcoming', [App\Http\Controllers\api\FestivalController::class, 'upcoming']);
    Route::get('festivals/{id}', [App\Http\Controllers\api\FestivalController::class, 'show']);

    // Google Calendar Integration
    Route::post('festivals/sync-to-calendar', [App\Http\Controllers\api\FestivalController::class, 'syncToGoogleCalendar']);
    Route::post('festivals/sync-multiple', [App\Http\Controllers\api\FestivalController::class, 'syncMultipleFestivals']);
    Route::post('festivals/remove-from-calendar', [App\Http\Controllers\api\FestivalController::class, 'removeFromGoogleCalendar']);
    Route::get('festivals/synced/list', [App\Http\Controllers\api\FestivalController::class, 'getSyncedFestivals']);

    // Push Notifications
    Route::post('festivals/send-notification', [App\Http\Controllers\api\FestivalController::class, 'sendFestivalNotification']);
    Route::post('festivals/schedule-notification', [App\Http\Controllers\api\FestivalController::class, 'scheduleFestivalNotification']);

    // Google Calendar Event Management (NEW)
    Route::post('calendar/create-event', [App\Http\Controllers\api\CalendarEventController::class, 'createEvent']);
    Route::post('calendar/sync-events', [App\Http\Controllers\api\CalendarEventController::class, 'syncGoogleCalendarEvents']);
    Route::get('calendar/reminders', [App\Http\Controllers\api\CalendarEventController::class, 'listReminders']);
    Route::post('calendar/process-reminders', [App\Http\Controllers\api\CalendarEventController::class, 'processRemindersManually']);

    // Google Calendar Authentication
    Route::get('google/auth-url', [App\Http\Controllers\api\GoogleAuthController::class, 'getAuthUrl']);
    Route::post('google/callback', [App\Http\Controllers\api\GoogleAuthController::class, 'handleCallback']);
    Route::post('google/refresh-token', [App\Http\Controllers\api\GoogleAuthController::class, 'refreshToken']);
    Route::post('google/disconnect', [App\Http\Controllers\api\GoogleAuthController::class, 'disconnect']);
    Route::get('google/events', [App\Http\Controllers\api\GoogleAuthController::class, 'listEvents']);

    Route::get('google/status', [App\Http\Controllers\api\GoogleAuthController::class, 'getStatus']);

});
