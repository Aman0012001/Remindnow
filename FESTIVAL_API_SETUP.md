# Festival API - Quick Setup Guide

## 🚀 Quick Start

### Step 1: Install Google API Client
Run this command in your project root:

```bash
composer require google/apiclient:"^2.0"
```

### Step 2: Run Database Migrations
```bash
php artisan migrate
```

This will create the following tables:
- `festival_calendar_syncs` - Tracks synced festivals
- `scheduled_notifications` - Manages scheduled notifications
- `user_google_tokens` - Stores Google OAuth tokens

### Step 3: Configure Google Calendar API

#### A. Create Google Cloud Project
1. Visit: https://console.cloud.google.com/
2. Create a new project (or select existing)
3. Enable **Google Calendar API**

#### B. Create OAuth 2.0 Credentials
1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Application type: **Web application**
4. Authorized redirect URIs:
   ```
   https://admin.drajaysaini.in/api/google/callback
   http://localhost:8000/api/google/callback
   ```
5. Download credentials JSON

#### C. Update .env File
Replace these values in your `.env` file:

```env
GOOGLE_CLIENT_ID=your_actual_client_id_here
GOOGLE_CLIENT_SECRET=your_actual_client_secret_here
GOOGLE_REDIRECT_URI=https://admin.drajaysaini.in/api/google/callback
```

### Step 4: Test the API

#### Test 1: Get Upcoming Festivals
```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming?limit=5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Test 2: Get Google Auth URL
```bash
curl -X GET "https://admin.drajaysaini.in/api/google/auth-url" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Test 3: Sync Festival to Calendar
```bash
curl -X POST "https://admin.drajaysaini.in/api/festivals/sync-to-calendar" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "google_access_token": "YOUR_GOOGLE_TOKEN"
  }'
```

---

## 📋 Available API Endpoints

### Festival Management
- `GET /api/festivals/all` - Get all festivals with filters
- `GET /api/festivals/upcoming` - Get upcoming festivals
- `GET /api/festivals/{id}` - Get festival details

### Google Calendar Integration
- `GET /api/google/auth-url` - Get Google OAuth URL
- `POST /api/google/callback` - Handle OAuth callback
- `POST /api/google/refresh-token` - Refresh access token
- `POST /api/google/disconnect` - Disconnect Google Calendar
- `GET /api/google/status` - Check connection status
- `POST /api/festivals/sync-to-calendar` - Sync single festival
- `POST /api/festivals/sync-multiple` - Bulk sync festivals
- `POST /api/festivals/remove-from-calendar` - Remove from calendar
- `GET /api/festivals/synced/list` - Get synced festivals

### Push Notifications
- `POST /api/festivals/send-notification` - Send instant notification
- `POST /api/festivals/schedule-notification` - Schedule notification

---

## 🔐 Authentication Flow

### For Mobile Apps

1. **Get Auth URL**
   ```javascript
   GET /api/google/auth-url
   Response: { "auth_url": "https://accounts.google.com/..." }
   ```

2. **Open Auth URL in WebView/Browser**
   - User logs in with Google
   - User grants calendar permissions

3. **Handle Callback**
   ```javascript
   POST /api/google/callback
   Body: { "code": "authorization_code_from_google" }
   Response: { "access_token": "...", "expires_in": 3600 }
   ```

4. **Use Access Token**
   ```javascript
   POST /api/festivals/sync-to-calendar
   Body: {
     "festival_id": 1,
     "google_access_token": "token_from_step_3"
   }
   ```

5. **Auto-Refresh Token**
   ```javascript
   POST /api/google/refresh-token
   Response: { "access_token": "new_token", "expires_in": 3600 }
   ```

---

## 🎯 Common Use Cases

### Use Case 1: Sync All Upcoming Festivals
```javascript
// 1. Get upcoming festivals
const festivals = await fetch('/api/festivals/upcoming?limit=10');

// 2. Extract festival IDs
const festivalIds = festivals.data.map(f => f.id);

// 3. Bulk sync to Google Calendar
await fetch('/api/festivals/sync-multiple', {
  method: 'POST',
  body: JSON.stringify({
    festival_ids: festivalIds,
    google_access_token: userToken
  })
});
```

### Use Case 2: Send Festival Reminder
```javascript
// Send notification to all users
await fetch('/api/festivals/send-notification', {
  method: 'POST',
  body: JSON.stringify({
    festival_id: 1,
    title: "🎉 Holi Tomorrow!",
    message: "Get ready for the festival of colors!"
  })
});
```

### Use Case 3: Schedule Notification
```javascript
// Schedule notification for 1 day before festival
await fetch('/api/festivals/schedule-notification', {
  method: 'POST',
  body: JSON.stringify({
    festival_id: 1,
    scheduled_at: "2026-03-13 18:00:00",
    title: "Reminder: Holi Tomorrow",
    message: "Don't forget to celebrate!"
  })
});
```

---

## 🔧 Troubleshooting

### Issue: "Google_Client class not found"
**Solution:** Run `composer require google/apiclient:"^2.0"`

### Issue: "Access token expired"
**Solution:** Use the refresh token endpoint:
```bash
POST /api/google/refresh-token
```

### Issue: "Calendar sync failed"
**Solution:** 
1. Check if Google Calendar API is enabled
2. Verify OAuth credentials in .env
3. Ensure redirect URI matches exactly

### Issue: "Push notifications not working"
**Solution:**
1. Verify FCM configuration
2. Check device tokens in database
3. Review notification settings for user

---

## 📊 Database Tables

### festival_calendar_syncs
Stores synced festival-calendar mappings
```sql
user_id, festival_id, google_event_id, calendar_id, synced_at
```

### scheduled_notifications
Manages scheduled notifications
```sql
user_id, festival_id, title, message, scheduled_at, status, sent_at
```

### user_google_tokens
Stores Google OAuth tokens
```sql
user_id, access_token, refresh_token, expires_at, token_type, scope
```

---

## 🎨 Example Mobile App Integration

### React Native Example
```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// Configure Google Sign-In
GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID',
  scopes: ['https://www.googleapis.com/auth/calendar']
});

// Sign in and get token
const signIn = async () => {
  await GoogleSignin.hasPlayServices();
  const userInfo = await GoogleSignin.signIn();
  const tokens = await GoogleSignin.getTokens();
  
  // Send to your API
  const response = await fetch('https://admin.drajaysaini.in/api/google/callback', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${yourApiToken}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      code: tokens.accessToken
    })
  });
};

// Sync festival
const syncFestival = async (festivalId, googleToken) => {
  const response = await fetch('https://admin.drajaysaini.in/api/festivals/sync-to-calendar', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${yourApiToken}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      festival_id: festivalId,
      google_access_token: googleToken
    })
  });
  
  return response.json();
};
```

---

## 📞 Support

For detailed documentation, see: `FESTIVAL_API_DOCUMENTATION.md`

**Need Help?**
- Email: support@drajaysaini.in
- API Base: https://admin.drajaysaini.in/api/

---

**Version:** 1.0.0  
**Last Updated:** February 4, 2026
