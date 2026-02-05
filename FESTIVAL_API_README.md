# 🎉 Festival API - Google Calendar Integration & Push Notifications

[![Laravel](https://img.shields.io/badge/Laravel-8.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![Google Calendar API](https://img.shields.io/badge/Google%20Calendar-API-green.svg)](https://developers.google.com/calendar)
[![Firebase FCM](https://img.shields.io/badge/Firebase-FCM-orange.svg)](https://firebase.google.com)

> **A comprehensive Festival Management API with Google Calendar synchronization and intelligent push notification system.**

---

## 🌟 Features

### ✨ Core Features
- 🎊 **Festival Management** - List, filter, and search festivals
- 📅 **Google Calendar Integration** - Seamless two-way sync
- 🔔 **Push Notifications** - Instant and scheduled notifications
- 🔄 **Bulk Operations** - Sync multiple festivals at once
- 🌍 **Multi-language Support** - English, Hindi, and more
- 🔐 **Secure Authentication** - OAuth 2.0 with Google
- 📱 **Mobile-Ready** - RESTful API for mobile apps

### 🚀 Advanced Features
- ⏰ **Scheduled Notifications** - Set reminders for future dates
- 🔄 **Auto Token Refresh** - Automatic Google token management
- 📊 **Sync Tracking** - Monitor all calendar syncs
- 🎯 **Targeted Notifications** - Send to specific users or groups
- 📈 **Analytics Ready** - Track sync and notification metrics

---

## 📋 Table of Contents

1. [Quick Start](#-quick-start)
2. [Installation](#-installation)
3. [Configuration](#-configuration)
4. [API Documentation](#-api-documentation)
5. [Usage Examples](#-usage-examples)
6. [Architecture](#-architecture)
7. [Testing](#-testing)
8. [Troubleshooting](#-troubleshooting)
9. [Contributing](#-contributing)
10. [License](#-license)

---

## ⚡ Quick Start

### Prerequisites
- PHP 7.4 or higher
- Composer
- MySQL 5.7+
- Laravel 8.x
- Google Cloud Project with Calendar API enabled

### Installation in 3 Steps

```bash
# 1. Install Google API Client
composer require google/apiclient:"^2.0"

# 2. Run Database Migrations
php artisan migrate

# 3. Configure Google Calendar API (see Configuration section)
```

### Test Your Installation

```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📦 Installation

### Step 1: Install Dependencies

```bash
composer require google/apiclient:"^2.0"
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

This creates:
- `festival_calendar_syncs` - Tracks Google Calendar syncs
- `scheduled_notifications` - Manages scheduled notifications
- `user_google_tokens` - Stores OAuth tokens

### Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
composer dump-autoload
```

---

## ⚙️ Configuration

### Google Calendar API Setup

#### 1. Create Google Cloud Project
1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google Calendar API**

#### 2. Create OAuth 2.0 Credentials
1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Application type: **Web application**
4. Add authorized redirect URIs:
   ```
   https://admin.drajaysaini.in/api/google/callback
   http://localhost:8000/api/google/callback
   ```
5. Download credentials JSON

#### 3. Update .env File

```env
# Google Calendar API Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://admin.drajaysaini.in/api/google/callback
```

---

## 📚 API Documentation

### Base URL
```
https://admin.drajaysaini.in/api/
```

### Authentication
All endpoints require Bearer token:
```
Authorization: Bearer {your_api_token}
```

### Endpoints Overview

#### Festival Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/festivals/all` | List all festivals with filters |
| GET | `/festivals/upcoming` | Get upcoming festivals |
| GET | `/festivals/{id}` | Get festival details |

#### Google Calendar Integration
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/google/auth-url` | Get OAuth authorization URL |
| POST | `/google/callback` | Handle OAuth callback |
| POST | `/google/refresh-token` | Refresh access token |
| POST | `/google/disconnect` | Disconnect Google Calendar |
| GET | `/google/status` | Check connection status |
| POST | `/festivals/sync-to-calendar` | Sync single festival |
| POST | `/festivals/sync-multiple` | Bulk sync festivals |
| POST | `/festivals/remove-from-calendar` | Remove from calendar |
| GET | `/festivals/synced/list` | List synced festivals |

#### Push Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/festivals/send-notification` | Send instant notification |
| POST | `/festivals/schedule-notification` | Schedule notification |

### Detailed Documentation
See [FESTIVAL_API_DOCUMENTATION.md](FESTIVAL_API_DOCUMENTATION.md) for complete API reference.

---

## 💡 Usage Examples

### Example 1: Get Upcoming Festivals

```bash
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming?limit=5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**
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

### Example 2: Sync Festival to Google Calendar

```bash
curl -X POST "https://admin.drajaysaini.in/api/festivals/sync-to-calendar" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "google_access_token": "ya29.a0AfH6SMBx..."
  }'
```

**Response:**
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

### Example 3: Send Push Notification

```bash
curl -X POST "https://admin.drajaysaini.in/api/festivals/send-notification" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "festival_id": 1,
    "title": "🎉 Holi Tomorrow!",
    "message": "Get ready to celebrate the festival of colors!"
  }'
```

**Response:**
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

### More Examples
See [FESTIVAL_API_SETUP.md](FESTIVAL_API_SETUP.md) for more usage examples.

---

## 🏗️ Architecture

### System Components

```
┌─────────────┐
│  Mobile App │
└──────┬──────┘
       │
       │ REST API
       │
┌──────▼──────────────┐
│   Laravel Server    │
│  • Controllers      │
│  • Services         │
│  • Models           │
└──────┬──────────────┘
       │
   ┌───┴────┐
   │        │
┌──▼──┐  ┌─▼────────┐
│MySQL│  │ External │
│ DB  │  │   APIs   │
└─────┘  │ • Google │
         │ • FCM    │
         └──────────┘
```

### Database Schema

- **festival_calendar_syncs** - Tracks synced festivals
- **scheduled_notifications** - Manages scheduled notifications
- **user_google_tokens** - Stores OAuth tokens

See [ARCHITECTURE_DIAGRAMS.md](ARCHITECTURE_DIAGRAMS.md) for detailed diagrams.

---

## 🧪 Testing

### Manual Testing with cURL

```bash
# Test 1: Get festivals
curl -X GET "https://admin.drajaysaini.in/api/festivals/upcoming" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test 2: Get Google auth URL
curl -X GET "https://admin.drajaysaini.in/api/google/auth-url" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test 3: Check connection status
curl -X GET "https://admin.drajaysaini.in/api/google/status" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using Postman

1. Import the API collection
2. Set environment variables:
   - `base_url`: `https://admin.drajaysaini.in/api`
   - `token`: Your Bearer token
3. Run the test suite

---

## 🔧 Troubleshooting

### Common Issues

#### Issue: "Google_Client class not found"
**Solution:**
```bash
composer require google/apiclient:"^2.0"
composer dump-autoload
```

#### Issue: "Access token expired"
**Solution:**
```bash
curl -X POST "https://admin.drajaysaini.in/api/google/refresh-token" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Issue: "Calendar sync failed"
**Solution:**
1. Verify Google Calendar API is enabled
2. Check OAuth credentials in `.env`
3. Ensure redirect URI matches exactly

#### Issue: "Push notifications not working"
**Solution:**
1. Verify FCM configuration
2. Check device tokens in database
3. Review user notification settings

### Debug Mode

Enable detailed logging in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 📁 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── api/
│           ├── FestivalController.php
│           └── GoogleAuthController.php
├── Models/
│   ├── FestivalCalendarSync.php
│   ├── ScheduledNotification.php
│   └── UserGoogleToken.php
└── Services/
    └── GoogleCalendarService.php

database/
└── migrations/
    ├── 2026_02_04_190324_create_festival_calendar_syncs_table.php
    ├── 2026_02_04_190325_create_scheduled_notifications_table.php
    └── 2026_02_04_190326_create_user_google_tokens_table.php

routes/
└── api.php (updated with new routes)

Documentation/
├── FESTIVAL_API_DOCUMENTATION.md
├── FESTIVAL_API_SETUP.md
├── INSTALLATION_COMMANDS.md
├── IMPLEMENTATION_SUMMARY.md
├── QUICK_REFERENCE.md
├── ARCHITECTURE_DIAGRAMS.md
└── README.md (this file)
```

---

## 📖 Documentation Files

| File | Description |
|------|-------------|
| [README.md](README.md) | This file - Overview and getting started |
| [FESTIVAL_API_DOCUMENTATION.md](FESTIVAL_API_DOCUMENTATION.md) | Complete API reference |
| [FESTIVAL_API_SETUP.md](FESTIVAL_API_SETUP.md) | Quick setup guide |
| [INSTALLATION_COMMANDS.md](INSTALLATION_COMMANDS.md) | Installation commands |
| [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) | Implementation details |
| [QUICK_REFERENCE.md](QUICK_REFERENCE.md) | Quick reference card |
| [ARCHITECTURE_DIAGRAMS.md](ARCHITECTURE_DIAGRAMS.md) | System architecture |

---

## 🎯 Use Cases

### Use Case 1: Mobile App Integration
Perfect for festival reminder apps that need calendar sync and notifications.

### Use Case 2: Event Management
Manage religious festivals, cultural events, and celebrations.

### Use Case 3: Community Engagement
Send timely reminders to community members about upcoming festivals.

---

## 🔐 Security

- ✅ OAuth 2.0 authentication with Google
- ✅ Bearer token authentication for API
- ✅ Encrypted token storage
- ✅ Input validation on all endpoints
- ✅ SQL injection protection (Laravel ORM)
- ✅ XSS protection
- ✅ HTTPS required in production

---

## 🚀 Performance

- ✅ Database indexing on foreign keys
- ✅ Efficient query optimization
- ✅ Pagination for large datasets
- ✅ Caching support
- ✅ Async notification processing

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

## 📞 Support

### Documentation
- Complete API docs: [FESTIVAL_API_DOCUMENTATION.md](FESTIVAL_API_DOCUMENTATION.md)
- Setup guide: [FESTIVAL_API_SETUP.md](FESTIVAL_API_SETUP.md)
- Quick reference: [QUICK_REFERENCE.md](QUICK_REFERENCE.md)

### Contact
- **Email:** support@drajaysaini.in
- **API Base:** https://admin.drajaysaini.in/api/

---

## 📝 License

This project is proprietary software developed for Govind932001/crafto.

---

## 🎊 Acknowledgments

- **Laravel** - The PHP framework
- **Google Calendar API** - Calendar integration
- **Firebase FCM** - Push notifications
- **Composer** - Dependency management

---

## 📊 Stats

- **API Endpoints:** 15+
- **Database Tables:** 3 new tables
- **Controllers:** 2 new controllers
- **Models:** 3 new models
- **Services:** 1 helper service
- **Documentation:** 7 comprehensive guides

---

## 🎉 What's Next?

### Recommended Next Steps
1. ✅ Install Google API client
2. ✅ Run database migrations
3. ✅ Configure Google Calendar API
4. ✅ Test API endpoints
5. ✅ Integrate with mobile app
6. ✅ Deploy to production

### Future Enhancements
- [ ] Webhook support for calendar changes
- [ ] Event update functionality
- [ ] Multiple calendar support
- [ ] Admin dashboard
- [ ] Analytics integration
- [ ] Email notifications
- [ ] SMS notifications

---

**Version:** 1.0.0  
**Release Date:** February 4, 2026  
**Status:** ✅ Production Ready  
**Author:** Govind932001/crafto

---

<div align="center">

**Made with ❤️ for Festival Management**

[Documentation](FESTIVAL_API_DOCUMENTATION.md) • [Setup Guide](FESTIVAL_API_SETUP.md) • [Quick Reference](QUICK_REFERENCE.md)

</div>
