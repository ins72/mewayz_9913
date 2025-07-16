# 🛠️ Troubleshooting Guide

This guide covers common issues and solutions for the Mewayz platform. Find quick fixes and detailed troubleshooting steps.

## 🚨 Common Issues

### 🔐 Authentication Issues

#### Unable to Login
**Symptoms:**
- Login form not working
- "Invalid credentials" error
- Redirect loops

**Solutions:**
1. **Clear browser cache and cookies**
   ```bash
   # Clear Laravel cache
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Check database connection**
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

3. **Verify user exists**
   ```bash
   php artisan tinker
   User::where('email', 'your@email.com')->first();
   ```

4. **Reset password**
   ```bash
   php artisan tinker
   $user = User::find(1);
   $user->password = Hash::make('newpassword');
   $user->save();
   ```

#### OAuth Login Not Working
**Symptoms:**
- OAuth redirect fails
- "Invalid state" error
- Missing user data

**Solutions:**
1. **Check OAuth configuration**
   ```env
   GOOGLE_CLIENT_ID=your_client_id
   GOOGLE_CLIENT_SECRET=your_client_secret
   GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
   ```

2. **Verify redirect URIs**
   - Check OAuth app settings
   - Ensure HTTPS in production
   - Verify callback URL matches

3. **Test OAuth flow**
   ```bash
   # Check OAuth routes
   php artisan route:list | grep oauth
   ```

#### Session Issues
**Symptoms:**
- Frequent logouts
- Session data lost
- "Session expired" messages

**Solutions:**
1. **Check session configuration**
   ```env
   SESSION_DRIVER=redis
   SESSION_LIFETIME=120
   SESSION_DOMAIN=.yourdomain.com
   ```

2. **Clear session data**
   ```bash
   php artisan session:table
   php artisan migrate
   ```

3. **Check Redis connection**
   ```bash
   redis-cli ping
   ```

### 🌐 Database Issues

#### Connection Errors
**Symptoms:**
- "Connection refused" errors
- Database timeout
- Migration failures

**Solutions:**
1. **Check database credentials**
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mewayz
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

2. **Test database connection**
   ```bash
   mysql -h 127.0.0.1 -u your_username -p
   ```

3. **Check database service**
   ```bash
   sudo systemctl status mysql
   sudo systemctl start mysql
   ```

4. **Verify database exists**
   ```sql
   SHOW DATABASES;
   USE mewayz;
   SHOW TABLES;
   ```

#### Migration Issues
**Symptoms:**
- Migration stuck
- Foreign key constraints
- Column already exists

**Solutions:**
1. **Check migration status**
   ```bash
   php artisan migrate:status
   ```

2. **Rollback and retry**
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```

3. **Fresh migration**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Skip problematic migration**
   ```bash
   php artisan migrate --pretend
   ```

### 🎨 Frontend Issues

#### Assets Not Loading
**Symptoms:**
- CSS/JS files not found
- Broken styling
- Console errors

**Solutions:**
1. **Rebuild assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

2. **Check asset links**
   ```bash
   php artisan storage:link
   ```

3. **Clear compiled assets**
   ```bash
   rm -rf public/build
   npm run build
   ```

4. **Check file permissions**
   ```bash
   sudo chown -R www-data:www-data public/
   sudo chmod -R 755 public/
   ```

#### Livewire Issues
**Symptoms:**
- Components not updating
- CSRF token mismatch
- JavaScript errors

**Solutions:**
1. **Clear Livewire cache**
   ```bash
   php artisan livewire:discover
   ```

2. **Check CSRF protection**
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

3. **Verify Livewire assets**
   ```bash
   php artisan livewire:publish --assets
   ```

### 📧 Email Issues

#### Emails Not Sending
**Symptoms:**
- No emails received
- Queue stuck
- SMTP errors

**Solutions:**
1. **Check email configuration**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   ```

2. **Test email sending**
   ```bash
   php artisan tinker
   Mail::raw('Test email', function ($message) {
       $message->to('test@example.com')->subject('Test');
   });
   ```

3. **Check queue workers**
   ```bash
   php artisan queue:work
   # or
   sudo supervisorctl status
   ```

4. **Process failed jobs**
   ```bash
   php artisan queue:failed
   php artisan queue:retry all
   ```

### 🔄 Queue Issues

#### Queue Not Processing
**Symptoms:**
- Jobs stuck in queue
- No job processing
- Worker not running

**Solutions:**
1. **Check queue workers**
   ```bash
   sudo supervisorctl status
   sudo supervisorctl start laravel-worker:*
   ```

2. **Manually process queue**
   ```bash
   php artisan queue:work
   ```

3. **Check failed jobs**
   ```bash
   php artisan queue:failed
   php artisan queue:retry all
   ```

4. **Clear queue**
   ```bash
   php artisan queue:clear
   ```

### 📱 Social Media Integration Issues

#### Instagram API Errors
**Symptoms:**
- API rate limiting
- Token expired
- Permission denied

**Solutions:**
1. **Check Instagram credentials**
   ```env
   INSTAGRAM_CLIENT_ID=your_client_id
   INSTAGRAM_CLIENT_SECRET=your_client_secret
   ```

2. **Refresh access token**
   ```bash
   php artisan instagram:refresh-tokens
   ```

3. **Check API limits**
   ```bash
   php artisan instagram:check-limits
   ```

4. **Verify permissions**
   - Check Instagram app settings
   - Verify approved permissions
   - Test with basic permissions

### 💳 Payment Issues

#### Stripe Integration Problems
**Symptoms:**
- Payment failures
- Webhook not working
- Invalid API keys

**Solutions:**
1. **Check Stripe configuration**
   ```env
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

2. **Test Stripe connection**
   ```bash
   php artisan tinker
   \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
   \Stripe\Account::retrieve();
   ```

3. **Verify webhook endpoint**
   ```bash
   curl -X POST https://yourdomain.com/webhook/stripe \
     -H "Content-Type: application/json" \
     -d '{"test": "data"}'
   ```

4. **Check webhook signatures**
   ```php
   $payload = @file_get_contents('php://input');
   $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
   $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
   
   \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
   ```

## 🔍 Debugging Tools

### Laravel Telescope
```bash
# Install Telescope
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `http://yourdomain.com/telescope`

### Debug Mode
```env
APP_DEBUG=true
APP_LOG_LEVEL=debug
```

### Query Debugging
```php
// In routes/web.php or controller
DB::listen(function ($query) {
    dump($query->sql);
    dump($query->bindings);
});
```

### Error Logging
```php
// Log custom errors
Log::error('Custom error message', [
    'user_id' => auth()->id(),
    'data' => $request->all()
]);
```

## 🚀 Performance Issues

### Slow Page Load
**Symptoms:**
- Pages take long to load
- High server response time
- Database queries slow

**Solutions:**
1. **Enable query caching**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize database queries**
   ```php
   // Use eager loading
   $users = User::with('bioSites')->get();
   
   // Add database indexes
   Schema::table('users', function (Blueprint $table) {
       $table->index('email');
   });
   ```

3. **Enable Redis caching**
   ```env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

4. **Optimize assets**
   ```bash
   npm run build
   ```

### Memory Issues
**Symptoms:**
- "Memory limit exceeded" errors
- Server crashes
- Slow performance

**Solutions:**
1. **Increase memory limit**
   ```php
   // In php.ini
   memory_limit = 512M
   
   // Or in specific scripts
   ini_set('memory_limit', '512M');
   ```

2. **Optimize code**
   ```php
   // Use chunk processing for large datasets
   User::chunk(100, function ($users) {
       foreach ($users as $user) {
           // Process user
       }
   });
   ```

3. **Clear unnecessary data**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

## 🛡️ Security Issues

### CSRF Token Mismatch
**Symptoms:**
- Forms not submitting
- AJAX requests failing
- 419 errors

**Solutions:**
1. **Include CSRF token**
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   
   <form>
       @csrf
       <!-- form fields -->
   </form>
   ```

2. **AJAX CSRF setup**
   ```javascript
   $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });
   ```

3. **Verify session configuration**
   ```env
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=strict
   ```

### Permission Denied
**Symptoms:**
- 403 errors
- Access denied messages
- Unauthorized actions

**Solutions:**
1. **Check user permissions**
   ```bash
   php artisan tinker
   $user = User::find(1);
   $user->can('update', $bioSite);
   ```

2. **Verify policies**
   ```php
   // In BioSitePolicy
   public function update(User $user, BioSite $bioSite)
   {
       return $user->id === $bioSite->user_id;
   }
   ```

3. **Check middleware**
   ```php
   // In routes
   Route::middleware(['auth', 'verified'])->group(function () {
       // Protected routes
   });
   ```

## 📊 Monitoring & Logs

### Log Locations
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# System logs
tail -f /var/log/syslog
```

### Health Checks
```bash
# Check application health
curl -I http://yourdomain.com/health

# Check database
php artisan tinker
DB::connection()->getPdo();

# Check Redis
redis-cli ping

# Check queue workers
php artisan queue:monitor
```

### Performance Monitoring
```php
// Monitor response times
Route::middleware(['response.time'])->group(function () {
    // Your routes
});
```

## 🔧 Environment-Specific Issues

### Development Environment
```bash
# Common dev issues
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
```

### Staging Environment
```bash
# Staging-specific checks
php artisan migrate --pretend
php artisan config:cache
```

### Production Environment
```bash
# Production troubleshooting
php artisan optimize
php artisan queue:restart
sudo supervisorctl restart all
```

## 🆘 Getting Help

### Before Seeking Help
1. **Check this troubleshooting guide**
2. **Search existing issues** on GitHub
3. **Check Laravel documentation**
4. **Review error logs**
5. **Test in isolation**

### How to Report Issues
1. **Gather information**
   - Error messages
   - Steps to reproduce
   - Environment details
   - Log files

2. **Create detailed report**
   ```markdown
   ## Environment
   - OS: Ubuntu 20.04
   - PHP: 8.2
   - Laravel: 11.0
   - Database: MySQL 8.0
   
   ## Issue Description
   Detailed description of the problem
   
   ## Steps to Reproduce
   1. Step one
   2. Step two
   3. Step three
   
   ## Expected Behavior
   What should happen
   
   ## Actual Behavior
   What actually happens
   
   ## Error Messages
   ```
   Any error messages or logs
   ```
   
   ## Additional Context
   Any other relevant information
   ```

### Support Channels
- **GitHub Issues**: Bug reports and feature requests
- **Discord**: Real-time community support
- **Email**: Direct support for critical issues
- **Documentation**: Comprehensive guides and references

## 📚 Additional Resources

### Official Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### Community Resources
- [Laravel Forums](https://laracasts.com/discuss)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [Reddit Laravel Community](https://reddit.com/r/laravel)

### Tools
- [Laravel Telescope](https://laravel.com/docs/telescope)
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- [Clockwork](https://github.com/itsgoingd/clockwork)

---

**Still Need Help?**
- 📧 Support: support@mewayz.com
- 💬 Discord: [discord.gg/mewayz](https://discord.gg/mewayz)
- 📚 Documentation: [docs.mewayz.com](https://docs.mewayz.com)
- 🐛 Bug Reports: [GitHub Issues](https://github.com/mewayz/platform/issues)

**Last Updated**: January 2025  
**Version**: 1.0.0