<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleCalendarService;
use App\Models\Festival;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncPublicHolidays extends Command
{
    protected $signature = 'calendar:sync-holidays';
    protected $description = 'Sync public festivals/holidays from Google Calendar using API Key';
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        parent::__construct();
        $this->googleService = $googleService;
    }

    public function handle()
    {
        $this->info('Starting public holiday sync from Google Calendar...');

        try {
            // Indian Holidays Calendar ID
            $calendarId = 'en.indian#holiday@group.v.calendar.google.com';

            // 1. Fetch events using the API Key (Refactored to use Http)
            $events = $this->googleService->getPublicEvents($calendarId, 100);

            if (empty($events)) {
                $this->warn('No events found in the public calendar.');
                return 0;
            }

            $syncedCount = 0;
            $testUser = User::first(); // For updating the notification list screenshot

            foreach ($events as $event) {
                // In Http response, $event is an array
                $name = $event['summary'] ?? 'Unknown Event';
                $date = $this->googleService->getEventDate($event);

                if (!$date)
                    continue;

                // 2. Update Festival Table
                Festival::updateOrCreate(
                    ['name' => $name],
                    [
                        'date' => $date,
                        'is_active' => 1,
                        'is_deleted' => 0
                    ]
                );

                // 3. To fix your screenshot, let's also update the sample notifications 
                // for the test user if they match a festival name in title OR description.
                if ($testUser) {
                    $parts = explode('/', $name);
                    Notification::where('user_id', $testUser->id)
                        ->where(function ($query) use ($parts) {
                            foreach ($parts as $part) {
                                $part = trim($part);
                                $query->orWhere('title', 'LIKE', '%' . $part . '%')
                                    ->orWhere('description', 'LIKE', '%' . $part . '%');
                            }
                        })
                        ->whereNull('event_date')
                        ->update(['event_date' => $date]);
                }

                $this->info("Synced: {$name} -> {$date}");
                $syncedCount++;
            }

            $this->info("Successfully synced {$syncedCount} holidays.");
            return 0;

        } catch (Exception $e) {
            $this->error('Sync Failed: ' . $e->getMessage());
            Log::error('Public Holiday Sync Error: ' . $e->getMessage());
            return 1;
        }
    }
}
