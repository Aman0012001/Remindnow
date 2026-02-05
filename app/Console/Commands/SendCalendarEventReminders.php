<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserGoogleToken;
use App\Models\UserDeviceToken;
use App\Services\GoogleCalendarService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class SendCalendarEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications for Google Calendar events 24 hours before they start';

    protected $googleService;
    protected $firebaseService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GoogleCalendarService $googleService, FirebaseService $firebaseService)
    {
        parent::__construct();
        $this->googleService = $googleService;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting calendar reminder check...');

        // Get all users who have connected Google Calendar
        $userTokens = UserGoogleToken::all();

        foreach ($userTokens as $userToken) {
            try {
                $user = User::find($userToken->user_id);
                if (!$user)
                    continue;

                $this->info("Checking events for user: {$user->email}");

                // Refresh token if expired
                if (Carbon::now()->greaterThanOrEqual($userToken->expires_at)) {
                    if ($userToken->refresh_token) {
                        $newToken = $this->googleService->refreshAccessToken($userToken->refresh_token);
                        $userToken->update([
                            'access_token' => $newToken['access_token'],
                            'expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
                        ]);
                    } else {
                        $this->warn("No refresh token for user {$user->email}. Skipping.");
                        continue;
                    }
                }

                $this->googleService->setAccessToken($userToken->access_token);

                // Fetch events for the next 48 hours to be safe
                $events = $this->googleService->listEvents(50);

                foreach ($events as $event) {
                    $startTimeStr = $event->start->dateTime ?? $event->start->date;
                    if (!$startTimeStr)
                        continue;

                    $startTime = Carbon::parse($startTimeStr);
                    $now = Carbon::now();

                    // Logic: Send reminder if event starts in exactly 24 hours (within a 15-min window)
                    // Window: (StartTime - 24h) is between (Now) and (Now + 15 mins)
                    $reminderTime = $startTime->copy()->subDay();

                    if ($reminderTime->greaterThan($now) && $reminderTime->lessThan($now->copy()->addMinutes(15))) {

                        $eventId = $event->getId();

                        // Check if already notified
                        $exists = DB::table('event_notifications')
                            ->where('user_id', $user->id)
                            ->where('event_id', $eventId)
                            ->where('reminder_type', '24h')
                            ->exists();

                        if (!$exists) {
                            $this->sendPushNotification($user, $event);

                            // Log in DB to prevent duplicates
                            DB::table('event_notifications')->insert([
                                'user_id' => $user->id,
                                'event_id' => $eventId,
                                'reminder_type' => '24h',
                                'notified_at' => Carbon::now(),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);

                            $this->info("Reminder sent for event: " . $event->getSummary());
                        }
                    }
                }

            } catch (Exception $e) {
                $this->error("Error processing user {$userToken->user_id}: " . $e->getMessage());
                Log::error("Calendar Reminder Command Error: " . $e->getMessage());
            }
        }

        $this->info('Calendar reminder check completed.');
        return 0;
    }

    /**
     * Send push notification using Firebase
     */
    protected function sendPushNotification($user, $event)
    {
        $deviceTokens = UserDeviceToken::where('user_id', $user->id)
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->pluck('device_token');

        if ($deviceTokens->isEmpty()) {
            $this->warn("No device tokens found for user {$user->email}");
            return;
        }

        $summary = $event->getSummary();
        $title = "⏰ Event Reminder";
        $body = "Tomorrow you have: {$summary}";
        $data = [
            'type' => 'calendar_reminder',
            'event_id' => $event->getId(),
            'start_time' => $event->start->dateTime ?? $event->start->date
        ];

        foreach ($deviceTokens as $token) {
            $this->firebaseService->sendNotification($token, $title, $body, $data);
        }
    }
}
