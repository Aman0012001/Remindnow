<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Log;
use Exception;

class FirebaseService
{
    protected $messaging;
    protected $factory;

    public function __construct()
    {
        try {
            // Initialize Firebase using the service account JSON file
            $serviceAccountPath = storage_path('app/firebase/firebase.json');

            // Fallback to public path if storage path doesn't exist
            if (!file_exists($serviceAccountPath)) {
                $serviceAccountPath = public_path('remyndnow-8ce2fb96e90f.json');
            }

            if (!file_exists($serviceAccountPath)) {
                throw new Exception('Firebase service account file not found');
            }

            if (class_exists('Kreait\\Firebase\\Factory')) {
                $this->factory = (new Factory)->withServiceAccount($serviceAccountPath);
                $this->messaging = $this->factory->createMessaging();
            }

        } catch (Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send push notification to a single device
     * 
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            // Create notification
            $notification = Notification::create($title, $body);

            // Build message
            $messageBuilder = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification);

            // Add data payload if provided
            if (!empty($data)) {
                // Convert all data values to strings (FCM requirement)
                $data = array_map('strval', $data);
                $messageBuilder = $messageBuilder->withData($data);
            }

            $message = $messageBuilder;

            // Send message
            $response = $this->messaging->send($message);

            // Log success
            $this->logNotification($deviceToken, $title, $body, $data, 'success', $response);

            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'response' => $response
            ];

        } catch (Exception $e) {
            // Log error
            $this->logNotification($deviceToken, $title, $body, $data, 'error', $e->getMessage());

            Log::error('Firebase send notification error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send push notification to multiple devices
     * 
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendMulticast($deviceTokens, $title, $body, $data = [])
    {
        try {
            // Create notification
            $notification = Notification::create($title, $body);

            // Build message
            $messageBuilder = CloudMessage::new()
                ->withNotification($notification);

            // Add data payload if provided
            if (!empty($data)) {
                $data = array_map('strval', $data);
                $messageBuilder = $messageBuilder->withData($data);
            }

            $message = $messageBuilder;

            // Send to multiple devices
            $report = $this->messaging->sendMulticast($message, $deviceTokens);

            $successCount = $report->successes()->count();
            $failureCount = $report->failures()->count();

            // Get invalid tokens
            $invalidTokens = [];
            foreach ($report->invalidTokens() as $invalidToken) {
                $invalidTokens[] = $invalidToken;
            }

            // Log multicast
            $this->logNotification(
                implode(',', $deviceTokens),
                $title,
                $body,
                $data,
                'multicast',
                "Success: {$successCount}, Failed: {$failureCount}"
            );

            return [
                'success' => true,
                'message' => 'Multicast notification sent',
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'invalid_tokens' => $invalidTokens
            ];

        } catch (Exception $e) {
            Log::error('Firebase multicast error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send multicast notification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification with custom options
     * 
     * @param string $deviceToken
     * @param array $options
     * @return array
     */
    public function sendCustomNotification($deviceToken, $options = [])
    {
        try {
            $title = $options['title'] ?? 'Notification';
            $body = $options['body'] ?? '';
            $data = $options['data'] ?? [];
            $imageUrl = $options['image'] ?? null;
            $badge = $options['badge'] ?? null;
            $sound = $options['sound'] ?? 'default';
            $priority = $options['priority'] ?? 'high';

            // Create notification
            $notificationBuilder = Notification::create($title, $body);

            if ($imageUrl) {
                $notificationBuilder = $notificationBuilder->withImageUrl($imageUrl);
            }

            $notification = $notificationBuilder;

            // Build message
            $messageBuilder = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification);

            // Add data
            if (!empty($data)) {
                $data = array_map('strval', $data);
                $messageBuilder = $messageBuilder->withData($data);
            }

            // Add Android config
            $androidConfig = [
                'priority' => $priority,
                'notification' => [
                    'sound' => $sound,
                    'color' => '#f45342'
                ]
            ];
            $messageBuilder = $messageBuilder->withAndroidConfig($androidConfig);

            // Add iOS config
            $apnsConfig = [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => $sound,
                        'badge' => $badge ?? 1,
                    ],
                ],
            ];
            $messageBuilder = $messageBuilder->withApnsConfig($apnsConfig);

            $message = $messageBuilder;

            // Send message
            $response = $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Custom notification sent successfully',
                'response' => $response
            ];

        } catch (Exception $e) {
            Log::error('Firebase custom notification error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send custom notification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate device token
     * 
     * @param string $deviceToken
     * @return bool
     */
    public function validateToken($deviceToken)
    {
        try {
            // Try to send a dry-run message
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create('Test', 'Test'));

            $this->messaging->validate($message);

            return true;

        } catch (Exception $e) {
            Log::warning('Invalid device token: ' . $deviceToken);
            return false;
        }
    }

    /**
     * Log notification details
     * 
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string $status
     * @param mixed $response
     */
    private function logNotification($token, $title, $body, $data, $status, $response)
    {
        try {
            $logData = [
                'timestamp' => now()->toDateTimeString(),
                'token' => substr($token, 0, 20) . '...', // Truncate for privacy
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'status' => $status,
                'response' => is_string($response) ? $response : json_encode($response)
            ];

            // Log to file
            $file = fopen(storage_path('logs/firebase_notifications.log'), 'a+');
            fwrite($file, "\n" . json_encode($logData, JSON_PRETTY_PRINT) . "\n");
            fclose($file);

        } catch (Exception $e) {
            Log::error('Failed to log notification: ' . $e->getMessage());
        }
    }

    /**
     * Get Firebase Factory instance
     * 
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get Firebase Messaging instance
     * 
     * @return \Kreait\Firebase\Messaging
     */
    public function getMessaging()
    {
        return $this->messaging;
    }
}
