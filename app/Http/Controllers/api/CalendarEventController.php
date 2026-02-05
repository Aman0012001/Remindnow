<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\CalendarEventReminder;
use App\Models\UserGoogleToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use Exception;

class CalendarEventController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Create a Google Calendar event and setup reminders
     */
    public function createEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date_format:Y-m-d H:i:s',
            'end_datetime' => 'required|date_format:Y-m-d H:i:s|after:start_datetime',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = auth('api')->user();
            $userToken = UserGoogleToken::where('user_id', $user->id)->first();

            if (!$userToken) {
                return response()->json(['success' => false, 'message' => 'Google account not connected.'], 400);
            }

            // Refresh token if expired
            if (Carbon::now()->greaterThanOrEqual($userToken->expires_at)) {
                $newToken = $this->googleService->refreshAccessToken($userToken->refresh_token);
                $userToken->update([
                    'access_token' => $newToken['access_token'],
                    'expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
                ]);
            }

            $this->googleService->setAccessToken($userToken->access_token);

            // 1. Create event in Google Calendar
            $googleEvent = $this->googleService->createEvent([
                'title' => $request->title,
                'description' => $request->description,
                'start_datetime' => Carbon::parse($request->start_datetime)->toIso8601String(),
                'end_datetime' => Carbon::parse($request->end_datetime)->toIso8601String(),
            ]);

            $googleEventId = $googleEvent->getId();
            $eventDate = $this->googleService->getEventDate($googleEvent);

            // 2. Store reminder preferences
            $reminders = [];
            if ($request->has('reminder_days')) {
                foreach ($request->reminder_days as $days) {
                    $remindAt = Carbon::parse($request->start_datetime)->subDays($days);

                    // Only store if reminder time is in the future
                    if ($remindAt->isFuture()) {
                        $reminders[] = CalendarEventReminder::create([
                            'user_id' => $user->id,
                            'google_event_id' => $googleEventId,
                            'event_title' => $request->title,
                            'event_date' => $eventDate,
                            'reminder_days' => $days,
                            'remind_at' => $remindAt,
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Event created with reminders.',
                'google_event_id' => $googleEventId,
                'reminders' => $reminders
            ]);

        } catch (Exception $e) {
            Log::error('Event Creation API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create event.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List user reminders
     */
    public function listReminders()
    {
        $user = auth('api')->user();
        $reminders = CalendarEventReminder::where('user_id', $user->id)
            ->orderBy('remind_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ]);
    }

    /**
     * Bulk Sync Google Calendar Events to local notifications
     */
    public function syncGoogleCalendarEvents(Request $request)
    {
        try {
            $user = auth('api')->user();
            $userToken = UserGoogleToken::where('user_id', $user->id)->first();

            if (!$userToken) {
                return response()->json(['success' => false, 'message' => 'Google account not connected.'], 400);
            }

            // Refresh token
            if (Carbon::now()->greaterThanOrEqual($userToken->expires_at)) {
                $newToken = $this->googleService->refreshAccessToken($userToken->refresh_token);
                $userToken->update([
                    'access_token' => $newToken['access_token'],
                    'expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
                ]);
            }

            $this->googleService->setAccessToken($userToken->access_token);
            $events = $this->googleService->listEvents(50); // Fetch last 50 events

            $syncedCount = 0;
            foreach ($events as $event) {
                $eventDate = $this->googleService->getEventDate($event);

                // Consistency Rule: No event without a date
                if (!$eventDate)
                    continue;

                // Sync to Notifications Table (Avoid duplicates by checking google_event_id if we had one, 
                // but since notifications table doesn't have it, we use title and date)
                $exists = Notification::where('user_id', $user->id)
                    ->where('title', 'LIKE', '%' . $event->getSummary() . '%')
                    ->where('event_date', $eventDate)
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => "📅 Google Event: " . $event->getSummary(),
                        'description' => $event->getDescription() ?? 'No description provided.',
                        'event_date' => $eventDate,
                        'is_read' => 0
                    ]);
                    $syncedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Google Calendar sync complete.',
                'synced_count' => $syncedCount,
                'total_found' => count($events)
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Manually trigger reminder processing (for testing)
     */
    public function processRemindersManually()
    {
        try {
            // We can call the command logic here or just trigger the command
            \Illuminate\Support\Facades\Artisan::call('calendar:process-reminders');
            $output = \Illuminate\Support\Facades\Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Reminder processor triggered.',
                'output' => $output
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to trigger processor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
