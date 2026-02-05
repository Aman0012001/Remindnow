<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleCalendarService
{
    protected $apiKey;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_CALENDAR_API_KEY');
        $this->clientId = env('GOOGLE_CLIENT_ID');
        $this->clientSecret = env('GOOGLE_CLIENT_SECRET');
        $this->redirectUri = env('GOOGLE_REDIRECT_URI');
    }

    /**
     * Fetch events from a public calendar using the API Key (No SDK needed)
     */
    public function getPublicEvents($calendarId, $limit = 50)
    {
        try {
            if (!$this->apiKey) {
                throw new Exception("Google Calendar API Key is missing in .env");
            }

            $url = "https://www.googleapis.com/calendar/v3/calendars/" . urlencode($calendarId) . "/events";

            $response = Http::get($url, [
                'key' => $this->apiKey,
                'maxResults' => $limit,
                'orderBy' => 'startTime',
                'singleEvents' => 'true',
                'timeMin' => date('c'),
            ]);

            if ($response->failed()) {
                throw new Exception("Google API Error: " . $response->body());
            }

            return $response->json()['items'] ?? [];
        } catch (Exception $e) {
            Log::error("Error fetching public calendar events ({$calendarId}): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract event date in Y-m-d format from a Google Calendar Event Array
     */
    public function getEventDate($event)
    {
        // Support both Object (SDK) and Array (Http Response)
        if (is_array($event)) {
            $start = $event['start'] ?? [];
            $date = $start['dateTime'] ?? ($start['date'] ?? null);
        } else {
            $start = $event->getStart();
            $date = $start->getDateTime() ?? $start->getDate();
        }

        if ($date) {
            return date('Y-m-d', strtotime($date));
        }

        return null;
    }

    /**
     * Minimal implementation of OAuth methods using Http if SDK is missing
     * (Adding these as placeholders to prevent crashes in other controllers)
     */
    public function setAccessToken($token)
    { /* Simplified for now */
    }
    public function listEvents($limit = 10, $calendarId = 'primary')
    {
        return []; // OAuth version requires token
    }
}
