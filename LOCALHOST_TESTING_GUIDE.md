# 🏠 Festival API - Localhost Testing Guide

## 🚀 Complete Setup for Local Testing (Right Now!)

---

## ⚡ Quick Start (10 Minutes)

### Step 1: Install Required Packages (5 minutes)

Open your terminal in the project directory and run:

```bash
# Navigate to your project
cd d:\000\00001\admin(3)

# Install Google API Client
composer require google/apiclient:"^2.0"

# Install Firebase Admin SDK
composer require kreait/firebase-php:"^7.0"
```

**Wait for installation to complete...**

---

### Step 2: Run Database Migrations (1 minute)

```bash
# Run migrations to create new tables
php artisan migrate

# Verify migrations
php artisan migrate:status
```

**Expected output:**
```
Migration name .................................................. Batch / Status
2026_02_04_190324_create_festival_calendar_syncs_table ......... [1] Ran
2026_02_04_190325_create_scheduled_notifications_table ......... [1] Ran
2026_02_04_190326_create_user_google_tokens_table .............. [1] Ran
```

---

### Step 3: Clear Cache (30 seconds)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
composer dump-autoload
```

---

### Step 4: Start Laravel Server (30 seconds)

```bash
# Start the development server
php artisan serve
```

**Expected output:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

**✅ Your API is now running at: `http://localhost:8000`**

---

## 🧪 Test in Postman (5 Minutes)

### Step 1: Update Postman Environment

1. Open **Postman**
2. Select your **Festival API - Dev** environment
3. Update the `base_url` variable:

```
base_url = http://localhost:8000/api
```

**Or create a new environment for localhost:**

| Variable | Value |
|----------|-------|
| `base_url` | `http://localhost:8000/api` |
| `token` | (your token - see below) |
| `google_token` | (leave empty) |
| `festival_id` | `1` |

---

### Step 2: Get Your Bearer Token

**Option A: Using Tinker (Recommended)**

Open a **new terminal** (keep server running) and run:

```bash
php artisan tinker
```

Then in tinker:

```php
// Get first user
$user = App\Models\User::first();

// Create token
$token = $user->createToken('postman-test')->plainTextToken;

// Display token
echo $token;
```

**Copy the token** (it looks like: `1|abc123xyz...`)

Type `exit` to quit tinker.

**Option B: From Database**

```bash
# Open MySQL
mysql -u root -p

# Use your database
use your_database_name;

# Get latest token
SELECT token FROM personal_access_tokens 
ORDER BY created_at DESC LIMIT 1;
```

**Option C: Check existing tokens in database**

Look in your `personal_access_tokens` table for an existing token.

---

### Step 3: Test Your First Endpoint

**In Postman:**

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/festivals/upcoming`
3. **Headers:**
   ```
   Authorization: Bearer YOUR_TOKEN_HERE
   Accept: application/json
   ```
4. Click **Send**

**Expected Response (200 OK):**
```json
{
    "success": true,
    "message": "Upcoming festivals retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Holi",
            "date": "2026-03-14",
            ...
        }
    ]
}
```

✅ **If you see this, your API is working!**

---

## 🔧 Complete Testing Workflow

### Test 1: Festival Management ✅

**1. Get Upcoming Festivals**
```http
GET http://localhost:8000/api/festivals/upcoming?limit=5
Authorization: Bearer YOUR_TOKEN
```

**2. Get All Festivals**
```http
GET http://localhost:8000/api/festivals/all?month=3&year=2026
Authorization: Bearer YOUR_TOKEN
```

**3. Get Festival Details**
```http
GET http://localhost:8000/api/festivals/1
Authorization: Bearer YOUR_TOKEN
```

---

### Test 2: Google Calendar Integration ✅

**1. Get Google Auth URL**
```http
GET http://localhost:8000/api/google/auth-url
Authorization: Bearer YOUR_TOKEN
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Authorization URL generated successfully",
    "data": {
        "auth_url": "https://accounts.google.com/o/oauth2/auth?..."
    }
}
```

**2. Complete OAuth Flow:**

a. Copy the `auth_url` from response
b. Open it in your browser
c. Login with Google
d. Grant calendar permissions
e. You'll be redirected to: `https://admin.drajaysaini.in/api/google/callback?code=...`
f. Copy the `code` parameter from URL

**3. Handle Callback**
```http
POST http://localhost:8000/api/google/callback
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

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
        "expires_in": 3600
    }
}
```

**Save the `access_token` to your `{{google_token}}` variable!**

**4. Check Connection Status**
```http
GET http://localhost:8000/api/google/status
Authorization: Bearer YOUR_TOKEN
```

**5. Sync Festival to Calendar**
```http
POST http://localhost:8000/api/festivals/sync-to-calendar
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "festival_id": 1,
    "google_access_token": "YOUR_GOOGLE_TOKEN",
    "calendar_id": "primary"
}
```

**6. Check Your Google Calendar!**
Open https://calendar.google.com - you should see the festival!

---

### Test 3: Push Notifications ✅

**1. Send Notification to All Users**
```http
POST http://localhost:8000/api/festivals/send-notification
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "festival_id": 1,
    "title": "🎉 Test Notification",
    "message": "Testing from localhost!"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Notifications sent successfully",
    "data": {
        "sent_count": 5,
        "failed_count": 0,
        "total_users": 5
    }
}
```

**2. Check Notification Logs**
```bash
# View Firebase notification logs
cat storage/logs/firebase_notifications.log

# Or on Windows
type storage\logs\firebase_notifications.log
```

**3. Schedule Notification**
```http
POST http://localhost:8000/api/festivals/schedule-notification
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "festival_id": 1,
    "scheduled_at": "2026-02-04 20:30:00",
    "title": "Scheduled Test",
    "message": "This will be sent at 8:30 PM"
}
```

---

## 🔍 Debugging & Monitoring

### Monitor Laravel Logs

**Terminal 1:** (Server running)
```bash
php artisan serve
```

**Terminal 2:** (Watch logs)
```bash
# Watch Laravel logs in real-time
tail -f storage/logs/laravel.log

# Or on Windows
Get-Content storage\logs\laravel.log -Wait -Tail 50
```

### Check Database Tables

```bash
php artisan tinker
```

```php
// Check festivals
DB::table('festivals')->count();
DB::table('festivals')->first();

// Check synced festivals
DB::table('festival_calendar_syncs')->get();

// Check scheduled notifications
DB::table('scheduled_notifications')->get();

// Check Google tokens
DB::table('user_google_tokens')->get();

// Check users with device tokens
DB::table('user_device_tokens')->get();
```

---

## 🐛 Common Issues & Solutions

### Issue 1: "Class not found" errors

**Problem:** Packages not installed

**Solution:**
```bash
composer require google/apiclient:"^2.0"
composer require kreait/firebase-php:"^7.0"
composer dump-autoload
```

---

### Issue 2: "Table doesn't exist"

**Problem:** Migrations not run

**Solution:**
```bash
php artisan migrate
php artisan migrate:status
```

---

### Issue 3: "Unauthenticated" (401)

**Problem:** Invalid or missing token

**Solution:**
```bash
# Generate new token
php artisan tinker
$user = App\Models\User::first();
$token = $user->createToken('test')->plainTextToken;
echo $token;
exit
```

Copy the token to Postman's `Authorization` header.

---

### Issue 4: "Firebase service account file not found"

**Problem:** Firebase JSON file missing

**Solution:**
```bash
# Check if file exists
dir public\remyndnow-8ce2fb96e90f.json

# Or create firebase directory
mkdir storage\app\firebase

# Copy your firebase.json there
copy public\remyndnow-8ce2fb96e90f.json storage\app\firebase\firebase.json
```

---

### Issue 5: "Google Calendar API error"

**Problem:** Google credentials not configured

**Solution:**

1. Check `.env` file has:
```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/google/callback
```

2. Update redirect URI in Google Cloud Console:
   - Go to https://console.cloud.google.com/
   - APIs & Services > Credentials
   - Edit OAuth 2.0 Client
   - Add: `http://localhost:8000/api/google/callback`

---

### Issue 6: Port 8000 already in use

**Solution:**
```bash
# Use different port
php artisan serve --port=8001

# Update Postman base_url to:
# http://localhost:8001/api
```

---

## 📊 Testing Checklist

### Before Testing:
- [ ] Packages installed (`composer require`)
- [ ] Migrations run (`php artisan migrate`)
- [ ] Cache cleared
- [ ] Server running (`php artisan serve`)
- [ ] Bearer token obtained
- [ ] Postman environment updated

### Basic Tests:
- [ ] GET /festivals/upcoming (200 OK)
- [ ] GET /festivals/all (200 OK)
- [ ] GET /festivals/1 (200 OK)

### Google Calendar Tests:
- [ ] GET /google/auth-url (200 OK)
- [ ] Completed OAuth flow
- [ ] POST /google/callback (200 OK)
- [ ] GET /google/status (connected: true)
- [ ] POST /festivals/sync-to-calendar (201 Created)
- [ ] Festival appears in Google Calendar

### Notification Tests:
- [ ] POST /festivals/send-notification (200 OK)
- [ ] Check logs: `storage/logs/firebase_notifications.log`
- [ ] POST /festivals/schedule-notification (201 Created)
- [ ] Check database: `scheduled_notifications` table

---

## 🎯 Quick Test Script

Save this as `test-api.ps1` (PowerShell):

```powershell
# Test Festival API on localhost

$baseUrl = "http://localhost:8000/api"
$token = "YOUR_TOKEN_HERE"

Write-Host "Testing Festival API..." -ForegroundColor Green

# Test 1: Get Upcoming Festivals
Write-Host "`n1. Testing GET /festivals/upcoming..." -ForegroundColor Yellow
curl.exe -X GET "$baseUrl/festivals/upcoming" `
  -H "Authorization: Bearer $token" `
  -H "Accept: application/json"

# Test 2: Get All Festivals
Write-Host "`n2. Testing GET /festivals/all..." -ForegroundColor Yellow
curl.exe -X GET "$baseUrl/festivals/all?month=3" `
  -H "Authorization: Bearer $token" `
  -H "Accept: application/json"

# Test 3: Get Google Auth URL
Write-Host "`n3. Testing GET /google/auth-url..." -ForegroundColor Yellow
curl.exe -X GET "$baseUrl/google/auth-url" `
  -H "Authorization: Bearer $token" `
  -H "Accept: application/json"

Write-Host "`nTests completed!" -ForegroundColor Green
```

Run it:
```powershell
.\test-api.ps1
```

---

## 🔥 Firebase Testing

### Check Firebase Configuration

```bash
php artisan tinker
```

```php
// Test Firebase Service
$firebaseService = new App\Services\FirebaseService();

// Check if initialized
echo "Firebase service initialized successfully!";

// Test with a device token (if you have one)
$result = $firebaseService->sendNotification(
    'YOUR_DEVICE_TOKEN',
    'Test from Localhost',
    'Testing Firebase integration',
    ['type' => 'test']
);

print_r($result);
```

---

## 📱 Testing with Real Device

### Get Device Token:

1. **From your mobile app**, register a device
2. **Check database:**
```sql
SELECT * FROM user_device_tokens WHERE user_id = 1;
```

3. **Send test notification:**
```http
POST http://localhost:8000/api/festivals/send-notification
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "festival_id": 1,
    "title": "Test from Localhost",
    "message": "Testing push notification!",
    "user_ids": [1]
}
```

4. **Check your device** - you should receive the notification!

---

## 🎊 Success Indicators

### ✅ Everything is working if:

1. **Server starts without errors**
   ```
   Starting Laravel development server: http://127.0.0.1:8000
   ```

2. **GET /festivals/upcoming returns 200**
   ```json
   {"success": true, "data": [...]}
   ```

3. **Google OAuth flow completes**
   ```json
   {"success": true, "data": {"access_token": "..."}}
   ```

4. **Festival syncs to Google Calendar**
   - Check https://calendar.google.com
   - See your festival event

5. **Notifications send successfully**
   ```json
   {"success": true, "data": {"sent_count": 5}}
   ```

6. **Logs show activity**
   ```bash
   cat storage/logs/firebase_notifications.log
   ```

---

## 🚀 Next Steps

After successful localhost testing:

1. **Deploy to production server**
2. **Update Postman to production URL**
3. **Test on production**
4. **Monitor logs**
5. **Set up automated notifications**

---

## 📞 Need Help?

### Check These Files:
- **POSTMAN_TESTING_GUIDE.md** - Complete API testing guide
- **FIREBASE_INTEGRATION_GUIDE.md** - Firebase setup
- **FESTIVAL_API_DOCUMENTATION.md** - Full API docs

### Common Commands:
```bash
# Start server
php artisan serve

# Watch logs
tail -f storage/logs/laravel.log

# Check routes
php artisan route:list | grep festival

# Clear everything
php artisan optimize:clear

# Generate token
php artisan tinker
```

---

## 🎉 You're Ready to Test!

**Current Status:**
- ✅ Code is ready
- ✅ Documentation is complete
- ✅ Postman collection is ready

**What to do now:**
1. Install packages
2. Run migrations
3. Start server
4. Get token
5. Test in Postman!

**Happy Testing!** 🚀

---

**Quick Reference:**
- **Server:** `php artisan serve`
- **URL:** `http://localhost:8000/api`
- **Token:** Get via tinker
- **Logs:** `storage/logs/laravel.log`
- **Firebase Logs:** `storage/logs/firebase_notifications.log`
