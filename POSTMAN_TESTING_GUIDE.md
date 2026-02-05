# 🧪 Festival API - Postman Testing Guide

## 📋 Table of Contents

1. [Setup](#setup)
2. [Authentication](#authentication)
3. [Festival Management Endpoints](#festival-management-endpoints)
4. [Google Calendar Integration](#google-calendar-integration)
5. [Push Notifications](#push-notifications)
6. [Testing Workflow](#testing-workflow)
7. [Common Issues](#common-issues)

---

## 🔧 Setup

### Step 1: Import Postman Collection

I've created a complete Postman collection file: `Festival_API.postman_collection.json`

**To import:**
1. Open Postman
2. Click **Import** button
3. Select `Festival_API.postman_collection.json`
4. Collection will be imported with all endpoints

### Step 2: Set Environment Variables

Create a new environment in Postman with these variables:

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | `https://admin.drajaysaini.in/api` | API base URL |
| `token` | `your_bearer_token_here` | Your API authentication token |
| `google_token` | `your_google_access_token` | Google OAuth access token |
| `festival_id` | `1` | Test festival ID |
| `user_id` | `1` | Test user ID |

**To create environment:**
1. Click the gear icon ⚙️ (top right)
2. Click **Add**
3. Name it "Festival API - Dev"
4. Add the variables above
5. Click **Add**
6. Select the environment from dropdown

---

## 🔐 Authentication

All API endpoints require Bearer token authentication.

### Headers Required:

```
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

### How to Get Your Token:

1. **Login to your app** (if you have a login endpoint)
2. **Or use existing token** from your database `personal_access_tokens` table
3. **Or generate via Passport/Sanctum**

**Example:**
```bash
POST https://admin.drajaysaini.in/api/login
{
    "email": "user@example.com",
    "password": "password"
}
```

Response will contain `access_token` - use this as your `{{token}}`.

---

## 📅 Festival Management Endpoints

### 1. Get All Festivals

**Endpoint:** `GET {{base_url}}/festivals/all`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Query Parameters (Optional):**
```
?start_date=2026-01-01
&end_date=2026-12-31
&month=3
&year=2026
&state=Maharashtra
&search=Holi
&per_page=15
```

**Example Request:**
```
GET https://admin.drajaysaini.in/api/festivals/all?month=3&year=2026
```

**Expected Response:**
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
                "is_active": 1,
                "festivalDesc": {
                    "description": "Festival of colors..."
                },
                "faqs": []
            }
        ],
        "total": 50,
        "per_page": 15
    }
}
```

---

### 2. Get Upcoming Festivals

**Endpoint:** `GET {{base_url}}/festivals/upcoming`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Query Parameters:**
```
?limit=10
```

**Example Request:**
```
GET https://admin.drajaysaini.in/api/festivals/upcoming?limit=5
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Upcoming festivals retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Holi",
            "date": "2026-03-14",
            "festivalDesc": {
                "description": "Festival of colors..."
            }
        }
    ]
}
```

---

### 3. Get Festival Details

**Endpoint:** `GET {{base_url}}/festivals/{{festival_id}}`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Example Request:**
```
GET https://admin.drajaysaini.in/api/festivals/1
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Festival details retrieved successfully",
    "data": {
        "id": 1,
        "name": "Holi",
        "date": "2026-03-14",
        "is_active": 1,
        "festivalDesc": {
            "description": "Festival of colors celebrated across India..."
        },
        "faqs": [
            {
                "question": "When is Holi?",
                "answer": "March 14, 2026"
            }
        ]
    }
}
```

---

## 📆 Google Calendar Integration

### 1. Get Google Auth URL

**Endpoint:** `GET {{base_url}}/google/auth-url`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Example Request:**
```
GET https://admin.drajaysaini.in/api/google/auth-url
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Authorization URL generated successfully",
    "data": {
        "auth_url": "https://accounts.google.com/o/oauth2/auth?client_id=...&redirect_uri=...&scope=..."
    }
}
```

**Next Steps:**
1. Copy the `auth_url`
2. Open it in browser
3. Login with Google
4. Grant calendar permissions
5. You'll be redirected with a `code` parameter
6. Use that code in the next endpoint

---

### 2. Handle Google OAuth Callback

**Endpoint:** `POST {{base_url}}/google/callback`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "code": "4/0AY0e-g7xxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Google Calendar connected successfully",
    "data": {
        "access_token": "ya29.a0AfH6SMBx...",
        "refresh_token": "1//0gxxxxxxxxxxx",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

**Important:** Save the `access_token` to your `{{google_token}}` environment variable!

---

### 3. Check Google Connection Status

**Endpoint:** `GET {{base_url}}/google/status`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (Connected):**
```json
{
    "success": true,
    "message": "Google Calendar is connected",
    "data": {
        "connected": true,
        "expires_at": "2026-02-04 20:52:54",
        "expires_in_minutes": 60
    }
}
```

**Expected Response (Not Connected):**
```json
{
    "success": true,
    "message": "Google Calendar is not connected",
    "data": {
        "connected": false
    }
}
```

---

### 4. Sync Festival to Google Calendar

**Endpoint:** `POST {{base_url}}/festivals/sync-to-calendar`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "festival_id": 1,
    "google_access_token": "{{google_token}}",
    "calendar_id": "primary"
}
```

**Expected Response:**
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

### 5. Sync Multiple Festivals (Bulk Sync)

**Endpoint:** `POST {{base_url}}/festivals/sync-multiple`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "festival_ids": [1, 2, 3, 4, 5],
    "google_access_token": "{{google_token}}",
    "calendar_id": "primary"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Bulk sync completed",
    "data": {
        "synced_count": 5,
        "failed_count": 0,
        "synced_events": [
            {
                "festival_id": 1,
                "festival_name": "Holi",
                "event_id": "abc123",
                "event_link": "https://calendar.google.com/..."
            }
        ],
        "failed_events": []
    }
}
```

---

### 6. Remove Festival from Google Calendar

**Endpoint:** `POST {{base_url}}/festivals/remove-from-calendar`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "festival_id": 1,
    "google_access_token": "{{google_token}}"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Festival removed from Google Calendar successfully"
}
```

---

### 7. Get Synced Festivals

**Endpoint:** `GET {{base_url}}/festivals/synced/list`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Synced festivals retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "festival_id": 1,
            "google_event_id": "abc123xyz",
            "calendar_id": "primary",
            "synced_at": "2026-02-04 19:30:00",
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

### 8. Refresh Google Access Token

**Endpoint:** `POST {{base_url}}/google/refresh-token`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "access_token": "ya29.a0AfH6SMBx...",
        "expires_in": 3600
    }
}
```

---

### 9. Disconnect Google Calendar

**Endpoint:** `POST {{base_url}}/google/disconnect`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Google Calendar disconnected successfully"
}
```

---

## 🔔 Push Notifications

### 1. Send Festival Notification

**Endpoint:** `POST {{base_url}}/festivals/send-notification`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON) - Send to All Users:**
```json
{
    "festival_id": 1,
    "title": "🎉 Holi Tomorrow!",
    "message": "Get ready to celebrate the festival of colors!"
}
```

**Body (JSON) - Send to Specific Users:**
```json
{
    "festival_id": 1,
    "title": "🎉 Holi Reminder",
    "message": "Don't forget to celebrate Holi tomorrow!",
    "user_ids": [1, 2, 3, 4, 5]
}
```

**Expected Response:**
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

### 2. Schedule Festival Notification

**Endpoint:** `POST {{base_url}}/festivals/schedule-notification`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "festival_id": 1,
    "scheduled_at": "2026-03-13 18:00:00",
    "title": "Reminder: Holi Tomorrow",
    "message": "Don't forget to celebrate Holi tomorrow at 10 AM!"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Festival notification scheduled successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "festival_id": 1,
        "title": "Reminder: Holi Tomorrow",
        "message": "Don't forget to celebrate Holi tomorrow at 10 AM!",
        "scheduled_at": "2026-03-13 18:00:00",
        "status": "pending",
        "created_at": "2026-02-04 19:52:54"
    }
}
```

---

## 🔄 Testing Workflow

### Complete Testing Flow:

#### **1. Test Festival Management**
```
1. GET /festivals/upcoming
2. GET /festivals/all?month=3&year=2026
3. GET /festivals/1
```

#### **2. Test Google Calendar Integration**
```
1. GET /google/auth-url
2. Open auth URL in browser → Get code
3. POST /google/callback (with code)
4. GET /google/status
5. POST /festivals/sync-to-calendar
6. GET /festivals/synced/list
7. POST /festivals/sync-multiple
8. POST /festivals/remove-from-calendar
9. POST /google/disconnect
```

#### **3. Test Push Notifications**
```
1. POST /festivals/send-notification (all users)
2. POST /festivals/send-notification (specific users)
3. POST /festivals/schedule-notification
```

---

## 🧪 Sample Test Data

### Festival IDs (Use existing from your database)
```json
{
    "festival_ids": [1, 2, 3, 4, 5]
}
```

### User IDs (Use existing from your database)
```json
{
    "user_ids": [1, 2, 3]
}
```

### Date Filters
```json
{
    "start_date": "2026-01-01",
    "end_date": "2026-12-31",
    "month": 3,
    "year": 2026
}
```

---

## ❌ Common Issues & Solutions

### Issue 1: "Unauthenticated" (401)

**Problem:** Missing or invalid Bearer token

**Solution:**
```
1. Check Authorization header: "Bearer {{token}}"
2. Verify token is valid
3. Check token hasn't expired
4. Ensure token is for correct user
```

---

### Issue 2: "Validation error" (422)

**Problem:** Missing required fields or invalid data

**Solution:**
```
1. Check request body matches examples
2. Verify all required fields are present
3. Check data types (string, integer, array)
4. Validate date formats: "YYYY-MM-DD HH:MM:SS"
```

---

### Issue 3: "Festival not found" (404)

**Problem:** Invalid festival ID

**Solution:**
```
1. Check festival exists in database
2. Verify festival is active (is_active = 1)
3. Use correct festival ID from /festivals/all
```

---

### Issue 4: "Google access token expired" (401)

**Problem:** Google token has expired

**Solution:**
```
1. POST /google/refresh-token
2. Or re-authenticate: GET /google/auth-url
3. Update {{google_token}} variable
```

---

### Issue 5: "No device tokens found"

**Problem:** User has no registered devices

**Solution:**
```
1. Check user_device_tokens table
2. Ensure user has valid device tokens
3. Test with user who has registered devices
```

---

## 📊 Response Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 401 | Unauthorized | Invalid or missing token |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation error |
| 500 | Server Error | Internal server error |

---

## 🎯 Quick Test Checklist

- [ ] Import Postman collection
- [ ] Set up environment variables
- [ ] Test authentication
- [ ] Test GET /festivals/upcoming
- [ ] Test GET /festivals/all
- [ ] Test GET /festivals/{id}
- [ ] Test GET /google/auth-url
- [ ] Complete Google OAuth flow
- [ ] Test POST /festivals/sync-to-calendar
- [ ] Test POST /festivals/send-notification
- [ ] Test POST /festivals/schedule-notification
- [ ] Verify notifications in logs
- [ ] Test error scenarios

---

## 📝 Notes

### Important Headers:
```
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

### Date Format:
```
YYYY-MM-DD HH:MM:SS
Example: 2026-03-13 18:00:00
```

### Google Token:
- Expires in 1 hour
- Use refresh token endpoint to renew
- Store in environment variable

### Device Tokens:
- Must be valid FCM tokens
- Check user_device_tokens table
- Invalid tokens will be logged

---

## 🚀 Ready to Test!

1. Import the Postman collection
2. Set up your environment variables
3. Start testing with `/festivals/upcoming`
4. Follow the testing workflow
5. Check logs for any issues

**Happy Testing!** 🎉

---

**Version:** 1.0.0  
**Last Updated:** February 4, 2026  
**API Base:** https://admin.drajaysaini.in/api/
