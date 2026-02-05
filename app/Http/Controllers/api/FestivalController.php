<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Festival;
use App\Models\FestivalDescription;
use App\Models\Reminder;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\NotificationSetting;
use App\Models\Notification;
use App\Models\FestivalCalendarSync;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

class FestivalController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get all festivals with optional filters
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Festival::with(['festivalDesc', 'faqs'])
                ->where('is_active', 1);

            // Filter by state
            if ($request->has('state')) {
                $query->whereJsonContains('states', $request->state);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            $results = $query->get();
            $allFestivals = collect();

            foreach ($results as $festival) {
                $dates = array_map('trim', explode(',', $festival->date));
                foreach ($dates as $date) {
                    try {
                        $carbonDate = Carbon::parse($date);

                        // Apply date filters in PHP
                        if ($request->has('start_date') && $request->has('end_date')) {
                            if (
                                $carbonDate->lt(Carbon::parse($request->start_date)) ||
                                $carbonDate->gt(Carbon::parse($request->end_date))
                            ) {
                                continue;
                            }
                        }

                        if ($request->has('month') && $carbonDate->month != $request->month) {
                            continue;
                        }

                        if ($request->has('year') && $carbonDate->year != $request->year) {
                            continue;
                        }

                        $festivalCopy = clone $festival;
                        $festivalCopy->date = $date;
                        $festivalCopy->event_date = $carbonDate->format('Y-m-d');
                        $allFestivals->push($festivalCopy);
                    } catch (\Exception $e) {
                        Log::warning("Invalid date format for festival {$festival->id}: {$date}");
                        continue;
                    }
                }
            }

            // Sort by date
            $sortedFestivals = $allFestivals->sortBy(function ($f) {
                return Carbon::parse($f->date);
            })->values();

            // Manual pagination
            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $offset = ($page - 1) * $perPage;

            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $sortedFestivals->slice($offset, $perPage)->values(),
                $sortedFestivals->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Festivals retrieved successfully',
                'data' => $paginated
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching festivals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching festivals',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming festivals
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upcoming(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $today = Carbon::today();

            $festivals = Festival::with(['festivalDesc', 'faqs'])
                ->where('is_active', 1)
                ->get();

            $upcomingFestivals = collect();

            foreach ($festivals as $festival) {
                $dates = array_map('trim', explode(',', $festival->date));
                foreach ($dates as $date) {
                    try {
                        $carbonDate = Carbon::parse($date);
                        if ($carbonDate->greaterThanOrEqualTo($today)) {
                            $festivalCopy = clone $festival;
                            $festivalCopy->date = $date;
                            $festivalCopy->event_date = $carbonDate->format('Y-m-d');
                            $upcomingFestivals->push($festivalCopy);
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $sortedUpcoming = $upcomingFestivals->sortBy(function ($f) {
                return Carbon::parse($f->date);
            })->values()->take($limit);

            return response()->json([
                'success' => true,
                'message' => 'Upcoming festivals retrieved successfully',
                'data' => $sortedUpcoming
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching upcoming festivals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching upcoming festivals',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get festival details by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $festival = Festival::with(['festivalDesc', 'faqs'])
                ->where('id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$festival) {
                return response()->json([
                    'success' => false,
                    'message' => 'Festival not found'
                ], 404);
            }

            $festival->event_date = Carbon::parse($festival->date)->format('Y-m-d');

            return response()->json([
                'success' => true,
                'message' => 'Festival details retrieved successfully',
                'data' => $festival
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching festival details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching festival details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync festival to Google Calendar
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncToGoogleCalendar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'festival_id' => 'required|integer|exists:festivals,id',
                'google_access_token' => 'required|string',
                'calendar_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id;
            $festival = Festival::with('festivalDesc')->find($request->festival_id);

            if (!$festival) {
                return response()->json([
                    'success' => false,
                    'message' => 'Festival not found'
                ], 404);
            }

            // Initialize Google Client
            $client = new Google_Client();
            $client->setAccessToken($request->google_access_token);

            // Check if token is expired
            if ($client->isAccessTokenExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google access token expired. Please re-authenticate.'
                ], 401);
            }

            // Create Calendar Service
            $service = new Google_Service_Calendar($client);

            // Use primary calendar if not specified
            $calendarId = $request->calendar_id ?? 'primary';

            // Create event
            $event = new Google_Service_Calendar_Event([
                'summary' => $festival->name,
                'description' => $festival->festivalDesc->description ?? 'Festival celebration',
                'start' => [
                    'date' => Carbon::parse($festival->date)->format('Y-m-d'),
                    'timeZone' => 'Asia/Kolkata',
                ],
                'end' => [
                    'date' => Carbon::parse($festival->date)->addDay()->format('Y-m-d'),
                    'timeZone' => 'Asia/Kolkata',
                ],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'popup', 'minutes' => 24 * 60], // 1 day before
                        ['method' => 'popup', 'minutes' => 60], // 1 hour before
                    ],
                ],
                'colorId' => '9', // Blue color for festivals
            ]);

            // Insert event into Google Calendar
            $createdEvent = $service->events->insert($calendarId, $event);

            // Store sync record in database
            FestivalCalendarSync::create([
                'user_id' => $userId,
                'festival_id' => $festival->id,
                'google_event_id' => $createdEvent->id,
                'calendar_id' => $calendarId,
                'synced_at' => now()
            ]);

            // Send push notification
            $this->sendSyncNotification($userId, $festival);

            return response()->json([
                'success' => true,
                'message' => 'Festival synced to Google Calendar successfully',
                'data' => [
                    'event_id' => $createdEvent->id,
                    'event_link' => $createdEvent->htmlLink,
                    'event_date' => Carbon::parse($festival->date)->format('Y-m-d'),
                    'festival' => $festival
                ]
            ], 201);

        } catch (\Google_Service_Exception $e) {
            Log::error('Google Calendar API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Google Calendar sync failed',
                'error' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error syncing to Google Calendar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing to Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync multiple festivals to Google Calendar
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncMultipleFestivals(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'festival_ids' => 'required|array',
                'festival_ids.*' => 'integer|exists:festivals,id',
                'google_access_token' => 'required|string',
                'calendar_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id;
            $syncedEvents = [];
            $failedEvents = [];

            // Initialize Google Client
            $client = new Google_Client();
            $client->setAccessToken($request->google_access_token);

            if ($client->isAccessTokenExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google access token expired. Please re-authenticate.'
                ], 401);
            }

            $service = new Google_Service_Calendar($client);
            $calendarId = $request->calendar_id ?? 'primary';

            foreach ($request->festival_ids as $festivalId) {
                try {
                    $festival = Festival::with('festivalDesc')->find($festivalId);

                    if (!$festival) {
                        $failedEvents[] = [
                            'festival_id' => $festivalId,
                            'error' => 'Festival not found'
                        ];
                        continue;
                    }

                    // Create event
                    $event = new Google_Service_Calendar_Event([
                        'summary' => $festival->name,
                        'description' => $festival->festivalDesc->description ?? 'Festival celebration',
                        'start' => [
                            'date' => Carbon::parse($festival->date)->format('Y-m-d'),
                            'timeZone' => 'Asia/Kolkata',
                        ],
                        'end' => [
                            'date' => Carbon::parse($festival->date)->addDay()->format('Y-m-d'),
                            'timeZone' => 'Asia/Kolkata',
                        ],
                        'reminders' => [
                            'useDefault' => false,
                            'overrides' => [
                                ['method' => 'popup', 'minutes' => 24 * 60],
                                ['method' => 'popup', 'minutes' => 60],
                            ],
                        ],
                        'colorId' => '9',
                    ]);

                    $createdEvent = $service->events->insert($calendarId, $event);

                    // Store sync record
                    FestivalCalendarSync::create([
                        'user_id' => $userId,
                        'festival_id' => $festival->id,
                        'google_event_id' => $createdEvent->id,
                        'calendar_id' => $calendarId,
                        'synced_at' => now()
                    ]);

                    $syncedEvents[] = [
                        'festival_id' => $festival->id,
                        'festival_name' => $festival->name,
                        'event_id' => $createdEvent->id,
                        'event_link' => $createdEvent->htmlLink
                    ];

                } catch (\Exception $e) {
                    $failedEvents[] = [
                        'festival_id' => $festivalId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Send bulk sync notification
            if (count($syncedEvents) > 0) {
                $this->sendBulkSyncNotification($userId, count($syncedEvents));
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk sync completed',
                'data' => [
                    'synced_count' => count($syncedEvents),
                    'failed_count' => count($failedEvents),
                    'synced_events' => $syncedEvents,
                    'failed_events' => $failedEvents
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in bulk sync: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk sync',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove festival from Google Calendar
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromGoogleCalendar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'festival_id' => 'required|integer|exists:festivals,id',
                'google_access_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id;

            // Find sync record
            $syncRecord = FestivalCalendarSync::where('user_id', $userId)
                ->where('festival_id', $request->festival_id)
                ->first();

            if (!$syncRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Festival not synced to Google Calendar'
                ], 404);
            }

            // Initialize Google Client
            $client = new Google_Client();
            $client->setAccessToken($request->google_access_token);

            if ($client->isAccessTokenExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google access token expired. Please re-authenticate.'
                ], 401);
            }

            $service = new Google_Service_Calendar($client);

            // Delete event from Google Calendar
            $service->events->delete($syncRecord->calendar_id, $syncRecord->google_event_id);

            // Delete sync record
            $syncRecord->delete();

            return response()->json([
                'success' => true,
                'message' => 'Festival removed from Google Calendar successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error removing from Google Calendar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing from Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get synced festivals for user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSyncedFestivals()
    {
        try {
            $userId = auth('api')->user()->id;

            $syncedFestivals = FestivalCalendarSync::with('festival.festivalDesc')
                ->where('user_id', $userId)
                ->orderBy('synced_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Synced festivals retrieved successfully',
                'data' => $syncedFestivals
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching synced festivals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching synced festivals',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send push notification for festival reminder
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendFestivalNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'festival_id' => 'required|integer|exists:festivals,id',
                'title' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $festival = Festival::with('festivalDesc')->find($request->festival_id);

            // Prepare notification content
            $title = $request->title ?? "🎉 {$festival->name} Reminder";
            $message = $request->message ?? ($festival->festivalDesc->description ?? "Don't forget to celebrate {$festival->name}!");

            // Determine target users
            if ($request->has('user_ids')) {
                $targetUsers = User::whereIn('id', $request->user_ids)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->get();
            } else {
                // Send to all users with festival notifications enabled
                $targetUsers = User::whereHas('notificationSetting', function ($query) {
                    $query->where('festival_notification', 1);
                })
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->get();
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($targetUsers as $user) {
                try {
                    // Get device tokens
                    $deviceTokens = UserDeviceToken::where('user_id', $user->id)
                        ->whereNotNull('device_token')
                        ->where('device_token', '!=', '')
                        ->get();

                    if ($deviceTokens->isEmpty()) {
                        $failedCount++;
                        continue;
                    }

                    // Store notification in database
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'description' => $message,
                        'event_date' => Carbon::parse($festival->date)->format('Y-m-d'),
                        'is_read' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Send push notification to each device using Firebase Service
                    foreach ($deviceTokens as $deviceToken) {
                        $notificationData = [
                            'festival_id' => (string) $festival->id,
                            'type' => 'festival_notification',
                            'date' => (string) $festival->date
                        ];

                        $result = $this->firebaseService->sendNotification(
                            $deviceToken->device_token,
                            $title,
                            $message,
                            $notificationData
                        );

                        if (!$result['success']) {
                            Log::warning("Failed to send to device: " . substr($deviceToken->device_token, 0, 20));
                        }
                    }

                    $sentCount++;

                } catch (\Exception $e) {
                    Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage());
                    $failedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifications sent successfully',
                'data' => [
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                    'total_users' => count($targetUsers)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error sending festival notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule festival notification
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scheduleFestivalNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'festival_id' => 'required|integer|exists:festivals,id',
                'scheduled_at' => 'required|date|after:now',
                'title' => 'nullable|string|max:255',
                'message' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id;
            $festival = Festival::find($request->festival_id);

            // Create scheduled notification record
            $scheduledNotification = \App\Models\ScheduledNotification::create([
                'user_id' => $userId,
                'festival_id' => $request->festival_id,
                'title' => $request->title ?? "🎉 {$festival->name} Reminder",
                'message' => $request->message ?? "Upcoming festival: {$festival->name}",
                'scheduled_at' => $request->scheduled_at,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Festival notification scheduled successfully',
                'data' => $scheduledNotification
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error scheduling notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error scheduling notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send sync notification to user
     * 
     * @param int $userId
     * @param Festival $festival
     */
    private function sendSyncNotification($userId, $festival)
    {
        try {
            $title = "✅ Calendar Synced";
            $message = "{$festival->name} has been added to your Google Calendar!";

            // Store notification
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'description' => $message,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get device tokens
            $deviceTokens = UserDeviceToken::where('user_id', $userId)
                ->whereNotNull('device_token')
                ->where('device_token', '!=', '')
                ->get();

            // Send push notification using Firebase Service
            foreach ($deviceTokens as $deviceToken) {
                $notificationData = [
                    'festival_id' => (string) $festival->id,
                    'type' => 'calendar_sync'
                ];

                $this->firebaseService->sendNotification(
                    $deviceToken->device_token,
                    $title,
                    $message,
                    $notificationData
                );
            }

        } catch (\Exception $e) {
            Log::error("Failed to send sync notification: " . $e->getMessage());
        }
    }

    /**
     * Send bulk sync notification
     * 
     * @param int $userId
     * @param int $count
     */
    private function sendBulkSyncNotification($userId, $count)
    {
        try {
            $title = "✅ Bulk Sync Complete";
            $message = "{$count} festivals have been synced to your Google Calendar!";

            // Store notification
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'description' => $message,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get device tokens
            $deviceTokens = UserDeviceToken::where('user_id', $userId)
                ->whereNotNull('device_token')
                ->where('device_token', '!=', '')
                ->get();

            // Send push notification using Firebase Service
            foreach ($deviceTokens as $deviceToken) {
                $notificationData = [
                    'count' => (string) $count,
                    'type' => 'bulk_calendar_sync'
                ];

                $this->firebaseService->sendNotification(
                    $deviceToken->device_token,
                    $title,
                    $message,
                    $notificationData
                );
            }

        } catch (\Exception $e) {
            Log::error("Failed to send bulk sync notification: " . $e->getMessage());
        }
    }
}
