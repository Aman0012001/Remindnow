<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\User;
use App\Models\UserGoogleToken;
use Log;

class GoogleAuthController extends Controller
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Get Google OAuth URL
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUrl()
    {
        try {
            $authUrl = $this->googleCalendarService->getAuthUrl();

            return response()->json([
                'success' => true,
                'message' => 'Authorization URL generated successfully',
                'data' => [
                    'auth_url' => $authUrl
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error generating auth URL: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating authorization URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle OAuth callback
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization code not provided'
                ], 400);
            }

            $token = $this->googleCalendarService->getAccessToken($request->code);

            // Store token for authenticated user
            if (auth('api')->check()) {
                $userId = auth('api')->user()->id;

                UserGoogleToken::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'access_token' => $token['access_token'],
                        'refresh_token' => $token['refresh_token'] ?? null,
                        'expires_at' => now()->addSeconds($token['expires_in']),
                        'token_type' => $token['token_type'] ?? 'Bearer',
                        'scope' => $token['scope'] ?? ''
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Google Calendar connected successfully',
                'data' => [
                    'access_token' => $token['access_token'],
                    'expires_in' => $token['expires_in'],
                    'token_type' => $token['token_type'] ?? 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error handling OAuth callback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh access token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        try {
            $userId = auth('api')->user()->id;
            $userToken = UserGoogleToken::where('user_id', $userId)->first();

            if (!$userToken || !$userToken->refresh_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No refresh token found. Please re-authenticate.'
                ], 404);
            }

            $newToken = $this->googleCalendarService->refreshAccessToken($userToken->refresh_token);

            // Update stored token
            $userToken->update([
                'access_token' => $newToken['access_token'],
                'expires_at' => now()->addSeconds($newToken['expires_in'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $newToken['access_token'],
                    'expires_in' => $newToken['expires_in']
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error refreshing token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect Google Calendar
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function disconnect()
    {
        try {
            $userId = auth('api')->user()->id;
            $userToken = UserGoogleToken::where('user_id', $userId)->first();

            if ($userToken) {
                // Revoke token
                $this->googleCalendarService->setAccessToken($userToken->access_token);
                $this->googleCalendarService->revokeToken();

                // Delete from database
                $userToken->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Google Calendar disconnected successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error disconnecting Google Calendar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error disconnecting Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List user's Google Calendar events
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listEvents(Request $request)
    {
        try {
            $userId = auth('api')->user()->id;
            $userToken = UserGoogleToken::where('user_id', $userId)->first();

            if (!$userToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Calendar not connected'
                ], 404);
            }

            // Refresh token if expired
            if (now()->greaterThanOrEqual($userToken->expires_at)) {
                if (!$userToken->refresh_token) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired. Please re-authenticate with Google.'
                    ], 401);
                }

                $newToken = $this->googleCalendarService->refreshAccessToken($userToken->refresh_token);
                $userToken->update([
                    'access_token' => $newToken['access_token'],
                    'expires_at' => now()->addSeconds($newToken['expires_in'])
                ]);
            }

            $this->googleCalendarService->setAccessToken($userToken->access_token);
            $limit = $request->get('limit', 10);
            $events = $this->googleCalendarService->listEvents($limit);

            $formattedEvents = [];
            foreach ($events as $event) {
                $start = $event->start->dateTime ?? $event->start->date;
                $end = $event->end->dateTime ?? $event->end->date;

                $formattedEvents[] = [
                    'id' => $event->getId(),
                    'summary' => $event->getSummary(),
                    'description' => $event->getDescription(),
                    'location' => $event->getLocation(),
                    'start' => $start,
                    'end' => $end,
                    'link' => $event->getHtmlLink(),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Events retrieved successfully',
                'data' => $formattedEvents
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching Google events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching events from Google Calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get connection status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus()
    {
        try {
            $userId = auth('api')->user()->id;
            $userToken = UserGoogleToken::where('user_id', $userId)->first();

            $isConnected = false;
            $isExpired = true;

            if ($userToken) {
                $isConnected = true;
                $isExpired = now()->greaterThan($userToken->expires_at);
            }

            return response()->json([
                'success' => true,
                'message' => 'Connection status retrieved',
                'data' => [
                    'is_connected' => $isConnected,
                    'is_expired' => $isExpired,
                    'expires_at' => $userToken ? $userToken->expires_at : null
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting connection status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting connection status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
