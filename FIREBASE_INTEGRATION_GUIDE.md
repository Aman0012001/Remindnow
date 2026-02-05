# 🔥 Firebase Integration - Installation Guide

## Overview

The Festival API now uses **kreait/firebase-php** SDK for sending push notifications instead of direct HTTP v1 API calls. This provides better reliability, automatic token management, and cleaner code.

---

## 📦 Installation Steps

### Step 1: Install Required Packages

```bash
# Install Google API Client for Google Calendar
composer require google/apiclient:"^2.0"

# Install Firebase Admin SDK for Push Notifications
composer require kreait/firebase-php:"^7.0"
```

### Step 2: Run Database Migrations

```bash
php artisan migrate
```

### Step 3: Configure Firebase Service Account

Your Firebase service account JSON file should be located at one of these paths:
- `storage/app/firebase/firebase.json` (recommended)
- `public/remyndnow-8ce2fb96e90f.json` (current fallback)

**Recommended Setup:**

1. Create the firebase directory:
```bash
mkdir -p storage/app/firebase
```

2. Copy your Firebase service account JSON to the recommended location:
```bash
copy public\remyndnow-8ce2fb96e90f.json storage\app\firebase\firebase.json
```

3. Update `.env` with your Firebase project ID:
```env
FIREBASE_PROJECT_ID=remyndnow
```

### Step 4: Configure Google Calendar API

Update `.env` with Google Calendar credentials:
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://admin.drajaysaini.in/api/google/callback
```

### Step 5: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
composer dump-autoload
```

---

## 🔧 Firebase Service Configuration

### Service Account JSON Structure

Your `firebase.json` should look like this:

```json
{
  "type": "service_account",
  "project_id": "remyndnow",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@remyndnow.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "..."
}
```

### How to Get Firebase Service Account JSON

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project (`remyndnow`)
3. Click the gear icon ⚙️ > **Project settings**
4. Go to **Service accounts** tab
5. Click **Generate new private key**
6. Download the JSON file
7. Rename it to `firebase.json`
8. Place it in `storage/app/firebase/`

---

## 🚀 Firebase Service Features

### Available Methods

#### 1. Send Single Notification
```php
$firebaseService->sendNotification(
    $deviceToken,
    $title,
    $body,
    $data = []
);
```

#### 2. Send Multicast (Multiple Devices)
```php
$firebaseService->sendMulticast(
    $deviceTokens,  // array of tokens
    $title,
    $body,
    $data = []
);
```

#### 3. Send Custom Notification
```php
$firebaseService->sendCustomNotification(
    $deviceToken,
    [
        'title' => 'Custom Title',
        'body' => 'Custom Body',
        'data' => ['key' => 'value'],
        'image' => 'https://example.com/image.png',
        'badge' => 1,
        'sound' => 'default',
        'priority' => 'high'
    ]
);
```

#### 4. Validate Device Token
```php
$isValid = $firebaseService->validateToken($deviceToken);
```

---

## 📊 Notification Logging

All notifications are automatically logged to:
- `storage/logs/firebase_notifications.log`

Log format:
```json
{
    "timestamp": "2026-02-04 19:47:34",
    "token": "dXJk8F9...",
    "title": "🎉 Holi Reminder",
    "body": "Don't forget to celebrate Holi!",
    "data": {
        "festival_id": "1",
        "type": "festival_notification"
    },
    "status": "success",
    "response": "..."
}
```

---

## 🔄 Migration from Old System

### What Changed?

**Before (Direct HTTP v1 API):**
```php
$this->send_push_notification(
    $deviceToken,
    '',
    $message,
    $title,
    'festival',
    ['festival_id' => $festivalId]
);
```

**After (Firebase Service):**
```php
$this->firebaseService->sendNotification(
    $deviceToken,
    $title,
    $message,
    ['festival_id' => (string)$festivalId]
);
```

### Benefits of New System

✅ **Better Error Handling** - Detailed error messages  
✅ **Automatic Retries** - Built-in retry logic  
✅ **Token Validation** - Validate tokens before sending  
✅ **Multicast Support** - Send to multiple devices efficiently  
✅ **Cleaner Code** - Service-based architecture  
✅ **Better Logging** - Structured JSON logs  
✅ **Type Safety** - Proper type checking  

---

## 🧪 Testing Firebase Integration

### Test 1: Send Test Notification

```php
use App\Services\FirebaseService;

$firebaseService = new FirebaseService();

$result = $firebaseService->sendNotification(
    'YOUR_DEVICE_TOKEN',
    'Test Notification',
    'This is a test message from Firebase Service',
    ['type' => 'test']
);

if ($result['success']) {
    echo "Notification sent successfully!";
} else {
    echo "Error: " . $result['error'];
}
```

### Test 2: Validate Token

```php
$isValid = $firebaseService->validateToken('YOUR_DEVICE_TOKEN');

if ($isValid) {
    echo "Token is valid!";
} else {
    echo "Token is invalid or expired";
}
```

### Test 3: Send Multicast

```php
$tokens = [
    'token1...',
    'token2...',
    'token3...'
];

$result = $firebaseService->sendMulticast(
    $tokens,
    'Multicast Test',
    'Testing multicast notification',
    ['type' => 'multicast_test']
);

echo "Success: {$result['success_count']}, Failed: {$result['failure_count']}";
```

---

## 🔐 Security Best Practices

### 1. Protect Service Account File

**Never commit `firebase.json` to version control!**

Add to `.gitignore`:
```
storage/app/firebase/firebase.json
public/remyndnow-8ce2fb96e90f.json
```

### 2. File Permissions

Set proper permissions:
```bash
chmod 600 storage/app/firebase/firebase.json
```

### 3. Environment Variables

Store sensitive data in `.env`:
```env
FIREBASE_PROJECT_ID=remyndnow
FIREBASE_SERVICE_ACCOUNT_PATH=storage/app/firebase/firebase.json
```

---

## 🐛 Troubleshooting

### Issue: "Firebase service account file not found"

**Solution:**
1. Check file exists at `storage/app/firebase/firebase.json`
2. Verify file permissions
3. Check fallback path: `public/remyndnow-8ce2fb96e90f.json`

### Issue: "Class 'Kreait\Firebase\Factory' not found"

**Solution:**
```bash
composer require kreait/firebase-php:"^7.0"
composer dump-autoload
```

### Issue: "Invalid service account JSON"

**Solution:**
1. Re-download service account JSON from Firebase Console
2. Verify JSON is valid (use JSON validator)
3. Ensure all required fields are present

### Issue: "Permission denied"

**Solution:**
1. Check Firebase project permissions
2. Verify service account has "Firebase Cloud Messaging Admin" role
3. Regenerate service account key

### Issue: "Notifications not received"

**Solution:**
1. Verify device token is valid
2. Check notification logs: `storage/logs/firebase_notifications.log`
3. Test with Firebase Console's "Cloud Messaging" tool
4. Verify FCM is enabled in Firebase project

---

## 📈 Performance Optimization

### Use Multicast for Bulk Notifications

**Instead of:**
```php
foreach ($deviceTokens as $token) {
    $firebaseService->sendNotification($token, $title, $body);
}
```

**Use:**
```php
$firebaseService->sendMulticast($deviceTokens, $title, $body);
```

**Benefits:**
- Faster execution
- Fewer API calls
- Better error handling
- Automatic invalid token detection

---

## 📝 API Changes Summary

### Festival Controller Updates

All notification methods now use `FirebaseService`:

1. **sendFestivalNotification()** - Uses `firebaseService->sendNotification()`
2. **sendSyncNotification()** - Uses `firebaseService->sendNotification()`
3. **sendBulkSyncNotification()** - Uses `firebaseService->sendNotification()`

### Data Format Changes

All data values must be strings (FCM requirement):

```php
// ✅ Correct
$data = [
    'festival_id' => (string)$festivalId,
    'type' => 'festival_notification',
    'date' => (string)$festivalDate
];

// ❌ Incorrect
$data = [
    'festival_id' => $festivalId,  // Integer
    'type' => 'festival_notification',
    'date' => $festivalDate  // Date object
];
```

---

## 🎯 Next Steps

1. ✅ Install packages: `composer require kreait/firebase-php google/apiclient`
2. ✅ Run migrations: `php artisan migrate`
3. ✅ Configure Firebase service account
4. ✅ Configure Google Calendar API
5. ✅ Clear cache
6. ✅ Test notification sending
7. ✅ Monitor logs
8. ✅ Deploy to production

---

## 📞 Support

- **Firebase Documentation:** https://firebase-php.readthedocs.io/
- **Google Calendar API:** https://developers.google.com/calendar
- **Project Email:** support@drajaysaini.in

---

**Version:** 2.0.0  
**Last Updated:** February 4, 2026  
**Firebase SDK:** kreait/firebase-php ^7.0  
**Google API Client:** google/apiclient ^2.0
