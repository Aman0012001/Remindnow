<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarEventReminder;
use App\Models\UserDeviceToken;
use App\Services\FirebaseService;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessCalendarReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:process-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch pending calendar reminders and send push notifications via FCM';

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting calendar reminder processor...');
        $now = Carbon::now();

        // 1. Fetch pending reminders where remind_at is now or past
        $reminders = CalendarEventReminder::where('status', 'pending')
            ->where('remind_at', '<=', $now)
            ->with('user')
            ->get();

        if ($reminders->isEmpty()) {
            $this->info('No pending reminders to process.');
            return 0;
        }

        $this->info('Processing ' . $reminders->count() . ' reminders.');

        foreach ($reminders as $reminder) {
            try {
                // Prevent duplicate processing if something went wrong
                if ($reminder->status === 'sent')
                    continue;

                $user = $reminder->user;
                if (!$user) {
                    $reminder->update(['status' => 'error']);
                    continue;
                }

                // 2. Prepare notification content
                $title = "⏰ Event Reminder";
                if ($reminder->reminder_days == 1) {
                    $body = "Your event is tomorrow: {$reminder->event_title}";
                } else {
                    $body = "Upcoming event in {$reminder->reminder_days} days: {$reminder->event_title}";
                }

                // 3. Send Push Notification to all user devices
                $deviceTokens = UserDeviceToken::where('user_id', $user->id)->pluck('device_token')->toArray();

                if (empty($deviceTokens)) {
                    $this->warn("No device tokens found for user: {$user->email}");
                } else {
                    foreach ($deviceTokens as $token) {
                        try {
                            $this->firebaseService->sendNotification($token, $title, $body, [
                                'type' => 'calendar_reminder',
                                'event_id' => $reminder->google_event_id,
                                'event_date' => $reminder->event_date,
                                'reminder_days' => $reminder->reminder_days
                            ]);
                        } catch (Exception $pushError) {
                            $this->error("Failed to send push to token for user {$user->id}: " . $pushError->getMessage());
                        }
                    }

                    // Create a notification record for history
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'description' => $body,
                        'event_date' => $reminder->event_date,
                        'is_read' => 0
                    ]);
                }

                // 4. Mark as sent (Idempotency)
                $reminder->update(['status' => 'sent']);
                $this->info("Reminder sent for user {$user->email}: {$reminder->event_title} ({$reminder->reminder_days}d)");

            } catch (Exception $e) {
                $this->error("Error processing reminder {$reminder->id}: " . $e->getMessage());
                Log::error("Reminder Command Error: " . $e->getMessage());
            }
        }

        $this->info('Calendar reminder processor completed.');
        return 0;
    }
}
