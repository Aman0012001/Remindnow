# 🚀 Postman Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Import Collection (30 seconds)

1. Open **Postman**
2. Click **Import** button (top left)
3. Drag and drop `Festival_API.postman_collection.json`
4. Click **Import**
5. ✅ Done! You'll see "Festival API - Google Calendar & Push Notifications" in your collections

---

### Step 2: Set Up Environment (2 minutes)

1. Click the **gear icon** ⚙️ (top right)
2. Click **Add** to create new environment
3. Name it: **Festival API - Dev**
4. Add these variables:

| Variable Name | Initial Value | Current Value |
|--------------|---------------|---------------|
| `base_url` | `https://admin.drajaysaini.in/api` | `https://admin.drajaysaini.in/api` |
| `token` | `your_token_here` | `your_token_here` |
| `google_token` | `` | `` |
| `festival_id` | `1` | `1` |

5. Click **Add**
6. Select **Festival API - Dev** from the environment dropdown (top right)

---

### Step 3: Get Your Bearer Token (2 minutes)

**Option A: From Database**
```sql
SELECT token FROM personal_access_tokens 
WHERE tokenable_id = YOUR_USER_ID 
ORDER BY created_at DESC LIMIT 1;
```

**Option B: Login via API** (if you have login endpoint)
```bash
POST https://admin.drajaysaini.in/api/login
{
    "email": "your@email.com",
    "password": "your_password"
}
```

**Option C: Generate via Tinker**
```bash
php artisan tinker
$user = User::find(1);
$token = $user->createToken('postman-test')->plainTextToken;
echo $token;
```

Copy the token and paste it in your `token` environment variable.

---

### Step 4: Test Your First Request (30 seconds)

1. Open the collection: **Festival API - Google Calendar & Push Notifications**
2. Navigate to: **Festival Management** → **Get Upcoming Festivals**
3. Click **Send**
4. ✅ You should see a 200 OK response with festival data!

---

## 🎯 Quick Test Sequence

### Test 1: Festival Management (1 minute)

```
1. Get Upcoming Festivals ✓
2. Get All Festivals ✓
3. Get Festival Details ✓
```

**Expected:** All return 200 OK with festival data

---

### Test 2: Google Calendar (5 minutes)

```
1. Get Google Auth URL ✓
   → Copy the auth_url from response
   
2. Open auth_url in browser ✓
   → Login with Google
   → Grant calendar permissions
   → Copy the 'code' from redirect URL
   
3. Handle Google OAuth Callback ✓
   → Paste the code in request body
   → Save access_token to {{google_token}}
   
4. Check Google Connection Status ✓
   → Should show "connected": true
   
5. Sync Festival to Google Calendar ✓
   → Check your Google Calendar!
   
6. Get Synced Festivals ✓
   → Should show the synced festival
```

---

### Test 3: Push Notifications (2 minutes)

```
1. Send Festival Notification (All Users) ✓
   → Check response for sent_count
   
2. Schedule Festival Notification ✓
   → Notification will be sent at scheduled time
```

---

## 📝 Environment Variables Explained

### `base_url`
- **Value:** `https://admin.drajaysaini.in/api`
- **Purpose:** API base URL
- **Change for:** Local testing (`http://localhost:8000/api`)

### `token`
- **Value:** Your Bearer authentication token
- **Purpose:** Authenticate API requests
- **Get from:** Database, login endpoint, or tinker
- **Example:** `1|abc123xyz...`

### `google_token`
- **Value:** Google OAuth access token
- **Purpose:** Google Calendar API authentication
- **Get from:** `/google/callback` endpoint
- **Expires:** After 1 hour (use refresh endpoint)
- **Example:** `ya29.a0AfH6SMBx...`

### `festival_id`
- **Value:** Festival ID to test with
- **Purpose:** Test single festival operations
- **Get from:** `/festivals/all` endpoint
- **Example:** `1`

---

## 🔍 Understanding Responses

### ✅ Success Response (200/201)
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### ❌ Error Response (4xx/5xx)
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### 🔐 Validation Error (422)
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

---

## 🛠️ Common Postman Tips

### Save Responses
- Click **Save Response** after successful requests
- Use as examples for future reference

### Use Tests Tab
Add this to **Tests** tab to auto-save tokens:
```javascript
// Auto-save Google token
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.access_token) {
        pm.environment.set("google_token", jsonData.data.access_token);
    }
}
```

### Use Pre-request Scripts
Add this to **Pre-request Script** for debugging:
```javascript
console.log("Token:", pm.environment.get("token"));
console.log("Festival ID:", pm.environment.get("festival_id"));
```

---

## 🐛 Troubleshooting

### Issue: "Unauthenticated" (401)
**Fix:**
1. Check `{{token}}` variable is set
2. Verify token format: `Bearer {{token}}`
3. Check token hasn't expired

### Issue: "Not Found" (404)
**Fix:**
1. Verify `base_url` is correct
2. Check endpoint path
3. Ensure festival_id exists

### Issue: "Validation error" (422)
**Fix:**
1. Check request body format
2. Verify all required fields
3. Check data types match

### Issue: Google token expired
**Fix:**
1. Use **Refresh Google Access Token** endpoint
2. Or re-authenticate via **Get Google Auth URL**

---

## 📊 Collection Structure

```
Festival API
├── Festival Management
│   ├── Get All Festivals
│   ├── Get Upcoming Festivals
│   └── Get Festival Details
│
├── Google Calendar Integration
│   ├── Get Google Auth URL
│   ├── Handle Google OAuth Callback
│   ├── Check Google Connection Status
│   ├── Refresh Google Access Token
│   ├── Disconnect Google Calendar
│   ├── Sync Festival to Google Calendar
│   ├── Sync Multiple Festivals (Bulk)
│   ├── Remove Festival from Google Calendar
│   └── Get Synced Festivals
│
└── Push Notifications
    ├── Send Festival Notification (All Users)
    ├── Send Festival Notification (Specific Users)
    └── Schedule Festival Notification
```

---

## 🎯 Testing Checklist

- [ ] Imported Postman collection
- [ ] Created environment
- [ ] Set `base_url` variable
- [ ] Set `token` variable
- [ ] Tested "Get Upcoming Festivals"
- [ ] Tested "Get All Festivals"
- [ ] Completed Google OAuth flow
- [ ] Set `google_token` variable
- [ ] Synced festival to calendar
- [ ] Sent test notification
- [ ] Scheduled notification
- [ ] Checked all responses are 200 OK

---

## 📞 Need Help?

- **Full Guide:** `POSTMAN_TESTING_GUIDE.md`
- **API Docs:** `FESTIVAL_API_DOCUMENTATION.md`
- **Firebase Setup:** `FIREBASE_INTEGRATION_GUIDE.md`

---

## 🎉 You're Ready!

Your Postman is now configured and ready to test the Festival API!

**Start with:** Festival Management → Get Upcoming Festivals

**Happy Testing!** 🚀

---

**Quick Reference:**
- Collection: `Festival_API.postman_collection.json`
- Environment: Festival API - Dev
- Base URL: `https://admin.drajaysaini.in/api`
- Auth: Bearer Token
