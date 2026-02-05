# Festival API - Installation Commands

## Prerequisites
Ensure you have PHP and Composer installed on your system.

## Installation Steps

### 1. Install Required Packages
```bash
# Install Google API Client for Google Calendar integration
composer require google/apiclient:"^2.0"

# Install Firebase Admin SDK for Push Notifications
composer require kreait/firebase-php:"^7.0"
```

### 2. Run Database Migrations
```bash
php artisan migrate
```

This will create:
- `festival_calendar_syncs` table
- `scheduled_notifications` table
- `user_google_tokens` table

### 3. Clear Cache (Optional but Recommended)
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 4. Update Composer Autoload
```bash
composer dump-autoload
```

## Verification

### Check if migrations ran successfully:
```bash
php artisan migrate:status
```

### Test API endpoint:
```bash
curl -X GET "http://localhost:8000/api/festivals/upcoming" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Next Steps

1. Configure Google Calendar API credentials in `.env`
2. Test Google OAuth flow
3. Test festival sync functionality
4. Configure push notifications

## Troubleshooting

If you encounter any issues:

1. **Composer not found:**
   - Install Composer from https://getcomposer.org/

2. **PHP not found:**
   - Ensure PHP is installed and added to PATH

3. **Migration errors:**
   - Check database connection in `.env`
   - Ensure database exists

4. **Google API errors:**
   - Verify credentials in `.env`
   - Check if Google Calendar API is enabled
