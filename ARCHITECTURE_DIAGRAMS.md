# Festival API - Architecture & Flow Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Mobile/Web App                            │
│  (React Native, Flutter, Web Browser)                           │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 │ HTTPS/REST API
                 │
┌────────────────▼────────────────────────────────────────────────┐
│                     Laravel API Server                           │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              API Controllers                              │  │
│  │  • FestivalController                                     │  │
│  │  • GoogleAuthController                                   │  │
│  │  • UsersController (existing)                             │  │
│  └──────────────────────────────────────────────────────────┘  │
│                           │                                      │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              Services Layer                               │  │
│  │  • GoogleCalendarService                                  │  │
│  │  • Push Notification Service (existing)                   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                           │                                      │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              Models/ORM                                   │  │
│  │  • Festival                                               │  │
│  │  • FestivalCalendarSync                                   │  │
│  │  • ScheduledNotification                                  │  │
│  │  • UserGoogleToken                                        │  │
│  │  • User, Reminder, Notification (existing)                │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────┬────────────────────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
┌───────▼────────┐  ┌────▼──────────────┐
│   MySQL DB     │  │  External APIs    │
│  • festivals   │  │  • Google         │
│  • syncs       │  │    Calendar API   │
│  • tokens      │  │  • Firebase FCM   │
│  • users       │  │                   │
└────────────────┘  └───────────────────┘
```

## Google Calendar Sync Flow

```
┌──────────┐                                                    ┌──────────┐
│  Mobile  │                                                    │  Google  │
│   App    │                                                    │ Calendar │
└────┬─────┘                                                    └────┬─────┘
     │                                                                │
     │ 1. GET /api/google/auth-url                                   │
     ├────────────────────────────────────────────►                  │
     │                                                                │
     │ 2. Return auth URL                                            │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
     │ 3. Open auth URL in browser                                   │
     ├───────────────────────────────────────────────────────────────►
     │                                                                │
     │ 4. User logs in & grants permissions                          │
     │◄───────────────────────────────────────────────────────────────┤
     │                                                                │
     │ 5. Redirect with authorization code                           │
     ◄───────────────────────────────────────────────────────────────┤
     │                                                                │
     │ 6. POST /api/google/callback {code}                           │
     ├────────────────────────────────────────────►                  │
     │                                             │                  │
     │                                             │ 7. Exchange code │
     │                                             ├──────────────────►
     │                                             │                  │
     │                                             │ 8. Return tokens │
     │                                             ◄──────────────────┤
     │                                             │                  │
     │ 9. Return access_token                     │                  │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
     │ 10. POST /api/festivals/sync-to-calendar                      │
     │     {festival_id, google_access_token}                        │
     ├────────────────────────────────────────────►                  │
     │                                             │                  │
     │                                             │ 11. Create event │
     │                                             ├──────────────────►
     │                                             │                  │
     │                                             │ 12. Event created│
     │                                             ◄──────────────────┤
     │                                             │                  │
     │ 13. Return success + event link            │                  │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
     │ 14. Send push notification                                    │
     │     "Festival synced to calendar!"                            │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
```

## Push Notification Flow

```
┌──────────┐         ┌──────────┐         ┌──────────┐         ┌──────────┐
│  Admin   │         │   API    │         │   FCM    │         │  User    │
│  Panel   │         │  Server  │         │  Server  │         │  Device  │
└────┬─────┘         └────┬─────┘         └────┬─────┘         └────┬─────┘
     │                     │                     │                     │
     │ 1. Send notification│                     │                     │
     │    request          │                     │                     │
     ├─────────────────────►                     │                     │
     │                     │                     │                     │
     │                     │ 2. Get user device  │                     │
     │                     │    tokens from DB   │                     │
     │                     ├──────┐              │                     │
     │                     │      │              │                     │
     │                     ◄──────┘              │                     │
     │                     │                     │                     │
     │                     │ 3. Send FCM request │                     │
     │                     ├─────────────────────►                     │
     │                     │                     │                     │
     │                     │                     │ 4. Push notification│
     │                     │                     ├─────────────────────►
     │                     │                     │                     │
     │                     │                     │ 5. Notification     │
     │                     │                     │    displayed        │
     │                     │                     │                     │
     │                     │ 6. Store in DB      │                     │
     │                     ├──────┐              │                     │
     │                     │      │              │                     │
     │                     ◄──────┘              │                     │
     │                     │                     │                     │
     │ 7. Return success   │                     │                     │
     ◄─────────────────────┤                     │                     │
     │                     │                     │                     │
```

## Database Relationships

```
┌──────────────┐
│    users     │
│──────────────│
│ id (PK)      │
│ name         │
│ email        │
└──────┬───────┘
       │
       │ 1:1
       │
┌──────▼───────────────┐
│ user_google_tokens   │
│──────────────────────│
│ id (PK)              │
│ user_id (FK)         │
│ access_token         │
│ refresh_token        │
│ expires_at           │
└──────────────────────┘

┌──────────────┐
│  festivals   │
│──────────────│
│ id (PK)      │
│ name         │
│ date         │
│ is_active    │
└──────┬───────┘
       │
       │ 1:N
       │
┌──────▼──────────────────┐
│ festival_calendar_syncs │
│─────────────────────────│
│ id (PK)                 │
│ user_id (FK)            │
│ festival_id (FK)        │
│ google_event_id         │
│ calendar_id             │
│ synced_at               │
└─────────────────────────┘

┌──────────────┐
│  festivals   │
│──────────────│
│ id (PK)      │
└──────┬───────┘
       │
       │ 1:N
       │
┌──────▼────────────────────┐
│ scheduled_notifications   │
│───────────────────────────│
│ id (PK)                   │
│ user_id (FK)              │
│ festival_id (FK)          │
│ title                     │
│ message                   │
│ scheduled_at              │
│ status                    │
│ sent_at                   │
└───────────────────────────┘
```

## API Request/Response Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    API Request Flow                              │
└─────────────────────────────────────────────────────────────────┘

1. Client Request
   ↓
   POST /api/festivals/sync-to-calendar
   Headers: {
     Authorization: Bearer {token}
     Content-Type: application/json
   }
   Body: {
     festival_id: 1,
     google_access_token: "ya29..."
   }

2. Middleware (AuthApi)
   ↓
   • Validate Bearer token
   • Authenticate user
   • Check permissions

3. Controller (FestivalController@syncToGoogleCalendar)
   ↓
   • Validate request data
   • Get festival from database
   • Initialize Google Client
   • Set access token

4. Google Calendar API
   ↓
   • Create calendar event
   • Set reminders
   • Return event details

5. Database Operations
   ↓
   • Store sync record
   • Create notification record

6. Push Notification
   ↓
   • Get user device tokens
   • Send FCM notification
   • Log notification

7. Response
   ↓
   {
     "success": true,
     "message": "Festival synced successfully",
     "data": {
       "event_id": "abc123",
       "event_link": "https://calendar.google.com/...",
       "festival": {...}
     }
   }
```

## Error Handling Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    Error Handling                                │
└─────────────────────────────────────────────────────────────────┘

Request
   ↓
┌──────────────────┐
│ Validation Error │ → 422 Unprocessable Entity
└──────────────────┘    {
                          "success": false,
                          "message": "Validation error",
                          "errors": {...}
                        }

┌──────────────────┐
│ Auth Error       │ → 401 Unauthorized
└──────────────────┘    {
                          "success": false,
                          "message": "Token expired"
                        }

┌──────────────────┐
│ Not Found        │ → 404 Not Found
└──────────────────┘    {
                          "success": false,
                          "message": "Festival not found"
                        }

┌──────────────────┐
│ Google API Error │ → 500 Server Error
└──────────────────┘    {
                          "success": false,
                          "message": "Calendar sync failed",
                          "error": "..."
                        }

┌──────────────────┐
│ Server Error     │ → 500 Server Error
└──────────────────┘    {
                          "success": false,
                          "message": "Internal error",
                          "error": "..."
                        }
```

## Token Refresh Flow

```
┌──────────┐                                                    ┌──────────┐
│  Client  │                                                    │  Google  │
└────┬─────┘                                                    └────┬─────┘
     │                                                                │
     │ 1. API call with expired token                                │
     ├────────────────────────────────────────────►                  │
     │                                                                │
     │ 2. Return 401 "Token expired"                                 │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
     │ 3. POST /api/google/refresh-token                             │
     ├────────────────────────────────────────────►                  │
     │                                             │                  │
     │                                             │ 4. Refresh token │
     │                                             ├──────────────────►
     │                                             │                  │
     │                                             │ 5. New token     │
     │                                             ◄──────────────────┤
     │                                             │                  │
     │ 6. Return new access_token                 │                  │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
     │ 7. Retry original request with new token                      │
     ├────────────────────────────────────────────►                  │
     │                                                                │
     │ 8. Success response                                           │
     ◄────────────────────────────────────────────┤                  │
     │                                                                │
```

## Scheduled Notification Processing

```
┌─────────────────────────────────────────────────────────────────┐
│              Scheduled Notification Cron Job                     │
└─────────────────────────────────────────────────────────────────┘

Every minute (or configured interval):

1. Query scheduled_notifications table
   ↓
   WHERE status = 'pending'
   AND scheduled_at <= NOW()

2. For each notification:
   ↓
   • Get user device tokens
   • Send FCM push notification
   • Update status to 'sent'
   • Set sent_at timestamp

3. Handle failures:
   ↓
   • Retry 3 times
   • If still fails, set status to 'failed'
   • Log error details

4. Cleanup:
   ↓
   • Archive old notifications (optional)
   • Update statistics
```

---

**Legend:**
- `→` or `├─►` : Request/Action
- `◄─` or `◄──┤` : Response
- `↓` : Flow direction
- `PK` : Primary Key
- `FK` : Foreign Key
- `1:1` : One-to-One relationship
- `1:N` : One-to-Many relationship

---

**Version:** 1.0.0  
**Created:** February 4, 2026
