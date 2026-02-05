# 🚀 Festival API - Quick Reference Card

## Installation (Run These Commands)

```bash
# 1. Install Google API Client
composer require google/apiclient:"^2.0"

# 2. Run Migrations
php artisan migrate

# 3. Clear Cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
composer dump-autoload
```

## Configuration (.env)

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://admin.drajaysaini.in/api/google/callback
```

## API Endpoints Quick Reference

### Festival Management
```
GET  /api/festivals/all              - List all festivals
GET  /api/festivals/upcoming         - Get upcoming festivals
GET  /api/festivals/{id}             - Get festival details
```

### Google Calendar
```
GET  /api/google/auth-url            - Get OAuth URL
POST /api/google/callback            - Handle OAuth callback
POST /api/google/refresh-token       - Refresh access token
POST /api/google/disconnect          - Disconnect calendar
GET  /api/google/status              - Check connection status
POST /api/festivals/sync-to-calendar - Sync single festival
POST /api/festivals/sync-multiple    - Bulk sync festivals
POST /api/festivals/remove-from-calendar - Remove from calendar
GET  /api/festivals/synced/list      - List synced festivals
```

### Push Notifications
```
POST /api/festivals/send-notification      - Send instant notification
POST /api/festivals/schedule-notification  - Schedule notification
```

## Quick Test Commands

```bash
# Get upcoming festivals
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get Google auth URL
curl -X GET "https://admin.drajaysaini.in/api/google/auth-url" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Sync festival
curl -X POST "https://admin.drajaysaini.in/api/festivals/sync-to-calendar" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"festival_id": 1, "google_access_token": "TOKEN"}'

# Send notification
curl -X POST "https://admin.drajaysaini.in/api/festivals/send-notification" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"festival_id": 1, "title": "Test", "message": "Test message"}'
```

## Files Created

### Controllers
- `app/Http/Controllers/api/FestivalController.php`
- `app/Http/Controllers/api/GoogleAuthController.php`

### Models
- `app/Models/FestivalCalendarSync.php`
- `app/Models/ScheduledNotification.php`
- `app/Models/UserGoogleToken.php`

### Services
- `app/Services/GoogleCalendarService.php`

### Migrations
- `database/migrations/2026_02_04_190324_create_festival_calendar_syncs_table.php`
- `database/migrations/2026_02_04_190325_create_scheduled_notifications_table.php`
- `database/migrations/2026_02_04_190326_create_user_google_tokens_table.php`

### Documentation
- `FESTIVAL_API_DOCUMENTATION.md` - Full API docs
- `FESTIVAL_API_SETUP.md` - Setup guide
- `INSTALLATION_COMMANDS.md` - Installation steps
- `IMPLEMENTATION_SUMMARY.md` - Complete summary
- `QUICK_REFERENCE.md` - This file

## Google Calendar Setup Steps

1. Go to https://console.cloud.google.com/
2. Create project or select existing
3. Enable Google Calendar API
4. Create OAuth 2.0 credentials (Web application)
5. Add redirect URI: `https://admin.drajaysaini.in/api/google/callback`
6. Copy Client ID and Secret to .env

## Common Request Bodies

### Sync Single Festival
```json
{
  "festival_id": 1,
  "google_access_token": "ya29.a0AfH6SMBx...",
  "calendar_id": "primary"
}
```

### Bulk Sync
```json
{
  "festival_ids": [1, 2, 3, 4, 5],
  "google_access_token": "ya29.a0AfH6SMBx...",
  "calendar_id": "primary"
}
```

### Send Notification
```json
{
  "festival_id": 1,
  "title": "🎉 Festival Alert!",
  "message": "Don't forget to celebrate!",
  "user_ids": [123, 456]
}
```

### Schedule Notification
```json
{
  "festival_id": 1,
  "scheduled_at": "2026-03-13 18:00:00",
  "title": "Reminder",
  "message": "Festival tomorrow!"
}
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Google_Client not found | Run: `composer require google/apiclient:"^2.0"` |
| Token expired | Use: `POST /api/google/refresh-token` |
| Calendar sync failed | Check Google Calendar API is enabled |
| Push not working | Verify FCM config and device tokens |

## Support

📧 Email: support@drajaysaini.in  
📚 Docs: See `FESTIVAL_API_DOCUMENTATION.md`  
🌐 API Base: https://admin.drajaysaini.in/api/

---

**Version:** 1.0.0 | **Date:** Feb 4, 2026 | **Status:** ✅ Ready
