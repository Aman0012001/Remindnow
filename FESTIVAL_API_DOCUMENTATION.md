# Festival API - Google Calendar Integration & Push Notifications

## Overview
This comprehensive Festival API provides seamless integration with Google Calendar and push notification capabilities for festival management.

## Features
✅ Festival Management (List, Filter, Search)
✅ Google Calendar Integration (Sync, Bulk Sync, Remove)
✅ Push Notifications (Instant & Scheduled)
✅ Reminder Management
✅ Multi-language Support

---

## Installation

### 1. Install Google API PHP Client

```bash
composer require google/apiclient:"^2.0"
```

### 2. Run Database Migrations

```bash
php artisan migrate
```

### 3. Configure Google Calendar API

#### Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable **Google Calendar API**

#### Step 2: Create OAuth 2.0 Credentials
1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Choose **Web application**
4. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback`
   - `https://yourdomain.com/auth/google/callback`
5. Download the credentials JSON file

#### Step 3: Add to .env File
```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

---

## API Endpoints

### Base URL
```
https://admin.drajaysaini.in/api/
```

### Authentication
All endpoints require Bearer token authentication:
```
Authorization: Bearer {your_api_token}
```

---

## 1. Festival Management APIs

### 1.1 Get All Festivals
**Endpoint:** `GET /festivals/all`

**Query Parameters:**
- `start_date` (optional) - Filter by start date (Y-m-d)
- `end_date` (optional) - Filter by end date (Y-m-d)
- `month` (optional) - Filter by month (1-12)
- `year` (optional) - Filter by year (YYYY)
- `state` (optional) - Filter by state
- `search` (optional) - Search by festival name
- `per_page` (optional) - Items per page (default: 15)

**Example Request:**
```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/all?month=3&year=2026&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Festivals retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Holi",
        "date": "2026-03-14",
        "states": ["Delhi", "UP", "Rajasthan"],
        "is_active": 1,
        "festivalDesc": {
          "description": "Festival of colors..."
        }
      }
    ],
    "total": 50
  }
}
```

---

### 1.2 Get Upcoming Festivals
**Endpoint:** `GET /festivals/upcoming`

**Query Parameters:**
- `limit` (optional) - Number of festivals (default: 10)

**Example Request:**
```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming?limit=5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 1.3 Get Festival Details
**Endpoint:** `GET /festivals/{id}`

**Example Request:**
```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 2. Google Calendar Integration APIs

### 2.1 Sync Festival to Google Calendar
**Endpoint:** `POST /festivals/sync-to-calendar`

**Request Body:**
```json
{
  "festival_id": 1,
  "google_access_token": "ya29.a0AfH6SMBx...",
  "calendar_id": "primary"
}
```

**Example Request:**
```bash
curl -X POST "https://admin.drajaysaini.in/api/festivals/sync-to-calendar" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "google_access_token": "ya29.a0AfH6SMBx...",
    "calendar_id": "primary"
  }'
```

**Example Response:**
```json
{
  "success": true,
  "message": "Festival synced to Google Calendar successfully",
  "data": {
    "event_id": "abc123xyz",
    "event_link": "https://calendar.google.com/calendar/event?eid=abc123xyz",
    "festival": {
      "id": 1,
      "name": "Holi",
      "date": "2026-03-14"
    }
  }
}
```

---

### 2.2 Bulk Sync Multiple Festivals
**Endpoint:** `POST /festivals/sync-multiple`

**Request Body:**
```json
{
  "festival_ids": [1, 2, 3, 4, 5],
  "google_access_token": "ya29.a0AfH6SMBx...",
  "calendar_id": "primary"
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Bulk sync completed",
  "data": {
    "synced_count": 4,
    "failed_count": 1,
    "synced_events": [
      {
        "festival_id": 1,
        "festival_name": "Holi",
        "event_id": "abc123",
        "event_link": "https://calendar.google.com/..."
      }
    ],
    "failed_events": [
      {
        "festival_id": 5,
        "error": "Festival not found"
      }
    ]
  }
}
```

---

### 2.3 Remove Festival from Google Calendar
**Endpoint:** `POST /festivals/remove-from-calendar`

**Request Body:**
```json
{
  "festival_id": 1,
  "google_access_token": "ya29.a0AfH6SMBx..."
}
```

---

### 2.4 Get Synced Festivals
**Endpoint:** `GET /festivals/synced/list`

**Example Response:**
```json
{
  "success": true,
  "message": "Synced festivals retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 123,
      "festival_id": 1,
      "google_event_id": "abc123xyz",
      "calendar_id": "primary",
      "synced_at": "2026-02-04T19:00:00.000000Z",
      "festival": {
        "id": 1,
        "name": "Holi",
        "date": "2026-03-14"
      }
    }
  ]
}
```

---

## 3. Push Notification APIs

### 3.1 Send Festival Notification
**Endpoint:** `POST /festivals/send-notification`

**Request Body:**
```json
{
  "festival_id": 1,
  "title": "🎉 Holi is Tomorrow!",
  "message": "Get ready to celebrate the festival of colors!",
  "user_ids": [123, 456, 789]
}
```

**Note:** If `user_ids` is not provided, notification will be sent to all users with festival notifications enabled.

**Example Response:**
```json
{
  "success": true,
  "message": "Notifications sent successfully",
  "data": {
    "sent_count": 150,
    "failed_count": 5,
    "total_users": 155
  }
}
```

---

### 3.2 Schedule Festival Notification
**Endpoint:** `POST /festivals/schedule-notification`

**Request Body:**
```json
{
  "festival_id": 1,
  "scheduled_at": "2026-03-13 18:00:00",
  "title": "🎉 Holi Tomorrow!",
  "message": "Don't forget to celebrate Holi tomorrow!"
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Festival notification scheduled successfully",
  "data": {
    "id": 1,
    "user_id": 123,
    "festival_id": 1,
    "title": "🎉 Holi Tomorrow!",
    "message": "Don't forget to celebrate Holi tomorrow!",
    "scheduled_at": "2026-03-13T18:00:00.000000Z",
    "status": "pending"
  }
}
```

---

## 4. How to Get Google Access Token

### Method 1: OAuth 2.0 Flow (Recommended)

Create an endpoint to handle Google OAuth:

```php
// routes/web.php
Route::get('/auth/google', function () {
    $client = new Google_Client();
    $client->setClientId(env('GOOGLE_CLIENT_ID'));
    $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
    $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
    $client->addScope(Google_Service_Calendar::CALENDAR);
    
    $authUrl = $client->createAuthUrl();
    return redirect($authUrl);
});

Route::get('/auth/google/callback', function (Request $request) {
    $client = new Google_Client();
    $client->setClientId(env('GOOGLE_CLIENT_ID'));
    $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
    $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
    
    $token = $client->fetchAccessTokenWithAuthCode($request->code);
    
    // Store token in user's session or database
    return response()->json([
        'access_token' => $token['access_token'],
        'refresh_token' => $token['refresh_token'] ?? null,
        'expires_in' => $token['expires_in']
    ]);
});
```

### Method 2: Mobile App Integration

For mobile apps, use Google Sign-In SDK:
- **Android:** Google Sign-In for Android
- **iOS:** Google Sign-In for iOS

After successful sign-in, send the access token to your API.

---

## 5. Automated Notifications

### Daily Festival Notifications (Already Configured)

The system automatically sends notifications using the existing command:

```bash
php artisan notifications:daily-festival
```

**Cron Job Setup:**
```bash
# Run daily at 6 PM
0 18 * * * cd /path/to/project && php artisan notifications:daily-festival
```

---

## 6. Testing the API

### Using Postman

1. **Import Collection:**
   - Create a new collection
   - Add all endpoints listed above
   - Set Authorization header with Bearer token

2. **Test Sequence:**
   ```
   1. Login → Get Bearer Token
   2. GET /festivals/upcoming
   3. POST /festivals/sync-to-calendar
   4. GET /festivals/synced/list
   5. POST /festivals/send-notification
   ```

### Using cURL

```bash
# 1. Get upcoming festivals
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming" \
  -H "Authorization: Bearer YOUR_TOKEN"

# 2. Sync to Google Calendar
curl -X POST "https://admin.drajaysaini.in/api/festivals/sync-to-calendar" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "google_access_token": "GOOGLE_TOKEN"
  }'

# 3. Send notification
curl -X POST "https://admin.drajaysaini.in/api/festivals/send-notification" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "title": "Festival Alert!",
    "message": "Celebrate today!"
  }'
```

---

## 7. Error Handling

### Common Error Responses

**401 Unauthorized:**
```json
{
  "success": false,
  "message": "Google access token expired. Please re-authenticate."
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Festival not found"
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "festival_id": ["The festival id field is required."]
  }
}
```

**500 Server Error:**
```json
{
  "success": false,
  "message": "Error syncing to Google Calendar",
  "error": "Detailed error message"
}
```

---

## 8. Best Practices

### Security
- ✅ Always use HTTPS in production
- ✅ Store Google tokens securely (encrypted)
- ✅ Implement token refresh mechanism
- ✅ Validate all user inputs
- ✅ Rate limit API endpoints

### Performance
- ✅ Use pagination for large datasets
- ✅ Cache frequently accessed data
- ✅ Queue bulk operations
- ✅ Optimize database queries

### Notifications
- ✅ Respect user notification preferences
- ✅ Handle invalid device tokens gracefully
- ✅ Log all notification attempts
- ✅ Implement retry mechanism for failed notifications

---

## 9. Database Schema

### festival_calendar_syncs
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key)
- festival_id (bigint, foreign key)
- google_event_id (varchar)
- calendar_id (varchar)
- synced_at (timestamp)
- created_at (timestamp)
- updated_at (timestamp)
```

### scheduled_notifications
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key)
- festival_id (bigint, foreign key)
- title (varchar)
- message (text)
- scheduled_at (timestamp)
- status (enum: pending, sent, failed, cancelled)
- sent_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## 10. Support & Troubleshooting

### Common Issues

**Issue 1: Google Calendar sync fails**
- Verify Google Calendar API is enabled
- Check access token validity
- Ensure proper OAuth scopes

**Issue 2: Push notifications not received**
- Verify FCM configuration
- Check device token validity
- Ensure user has notifications enabled

**Issue 3: Token expired errors**
- Implement token refresh logic
- Store refresh tokens securely
- Handle token expiration gracefully

---

## Contact & Support

For issues or questions:
- Email: support@drajaysaini.in
- Documentation: https://admin.drajaysaini.in/docs

---

**Version:** 1.0.0  
**Last Updated:** February 4, 2026  
**Author:** Govind932001/crafto
