# Mewayz Platform - Security Documentation

This document outlines the comprehensive security measures, best practices, and protocols implemented in the Mewayz platform.

## 🔒 Security Overview

### Security Philosophy
The Mewayz platform implements a **defense-in-depth** security strategy, incorporating multiple layers of protection at the application, infrastructure, and operational levels. Security is integrated throughout the development lifecycle and continuously monitored in production.

### Security Compliance
- **GDPR Compliant** - Data protection and privacy
- **PCI DSS Ready** - Payment card industry standards
- **SOC 2 Type II** - Security operations controls
- **ISO 27001** - Information security management

### Security Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Edge Security                            │
├─────────────────────────────────────────────────────────────┤
│  WAF | DDoS Protection | SSL/TLS | Rate Limiting            │
└─────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────┐
│                  Application Security                       │
├─────────────────────────────────────────────────────────────┤
│  Authentication | Authorization | Input Validation | CSRF   │
└─────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────┐
│                    Data Security                            │
├─────────────────────────────────────────────────────────────┤
│  Encryption | Hashing | Data Masking | Backup Security     │
└─────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────┐
│                Infrastructure Security                      │
├─────────────────────────────────────────────────────────────┤
│  Container Security | Network Segmentation | Monitoring     │
└─────────────────────────────────────────────────────────────┘
```

## 🔐 Authentication & Authorization

### Multi-Factor Authentication

#### Web Authentication
```php
// Laravel Sanctum implementation
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log security event
            SecurityLogger::log('successful_login', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return redirect()->intended('/dashboard');
        }
        
        // Log failed attempt
        SecurityLogger::log('failed_login', [
            'email' => $credentials['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
}
```

#### API Authentication
```php
// API token authentication
class APIAuthController extends Controller
{
    public function createToken(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Create API token with abilities
            $token = $user->createToken('api-token', [
                'read:sites',
                'write:sites',
                'read:analytics',
                'write:payments',
            ]);
            
            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at,
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
```

### Role-Based Access Control (RBAC)

#### Permission System
```php
// Role and permission model
class User extends Authenticatable
{
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }
    
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }
}

// Middleware for permission checking
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!$request->user()->hasPermission($permission)) {
            abort(403, 'Insufficient permissions');
        }
        
        return $next($request);
    }
}
```

#### Authorization Policies
```php
// Site management policy
class SitePolicy
{
    public function view(User $user, Site $site): bool
    {
        return $user->id === $site->user_id || $user->hasRole('admin');
    }
    
    public function update(User $user, Site $site): bool
    {
        return $user->id === $site->user_id;
    }
    
    public function delete(User $user, Site $site): bool
    {
        return $user->id === $site->user_id || $user->hasRole('admin');
    }
}
```

## 🛡️ Input Validation & Sanitization

### Form Request Validation

#### Payment Request Validation
```php
class CreateCheckoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        return [
            'package_id' => [
                'required',
                'string',
                Rule::in(['starter', 'professional', 'enterprise']),
            ],
            'success_url' => [
                'required',
                'url',
                'regex:/^https:\/\//',
            ],
            'cancel_url' => [
                'required',
                'url',
                'regex:/^https:\/\//',
            ],
            'metadata' => 'array',
            'metadata.source' => 'string|max:50',
        ];
    }
    
    public function messages(): array
    {
        return [
            'package_id.in' => 'Invalid package selected.',
            'success_url.regex' => 'Success URL must use HTTPS.',
            'cancel_url.regex' => 'Cancel URL must use HTTPS.',
        ];
    }
}
```

#### Site Creation Validation
```php
class CreateSiteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
            'domain' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-]+$/',
                'unique:sites,domain',
            ],
            'template' => [
                'required',
                'string',
                Rule::in(['professional', 'modern', 'classic']),
            ],
            'settings' => 'array',
            'settings.theme' => 'string|in:dark,light',
            'settings.custom_css' => 'string|max:10000',
        ];
    }
}
```

### Data Sanitization

#### HTML Sanitization
```php
class SanitizationService
{
    public function sanitizeHtml(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href],br,ul,ol,li,h1,h2,h3');
        $config->set('Attr.AllowedProtocols', 'http,https,mailto');
        
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }
    
    public function sanitizeText(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
```

#### SQL Injection Prevention
```php
// Always use parameterized queries
class SecureQueryService
{
    public function getUserSites(int $userId): Collection
    {
        // Correct: Using Eloquent ORM
        return Site::where('user_id', $userId)->get();
        
        // Correct: Using Query Builder
        return DB::table('sites')
            ->where('user_id', '=', $userId)
            ->get();
    }
    
    // Never do this - vulnerable to SQL injection
    public function unsafeQuery(string $userInput): Collection
    {
        // NEVER DO THIS
        return DB::select("SELECT * FROM sites WHERE name = '{$userInput}'");
    }
}
```

## 🔒 Data Protection & Encryption

### Data Encryption

#### Database Encryption
```php
// Model with encrypted attributes
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // Encrypt sensitive data
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = encrypt($value);
    }
    
    public function getPhoneAttribute($value)
    {
        return decrypt($value);
    }
}
```

#### File Encryption
```php
class FileEncryptionService
{
    public function encryptFile(string $filePath): string
    {
        $contents = file_get_contents($filePath);
        $encrypted = encrypt($contents);
        
        $encryptedPath = $filePath . '.encrypted';
        file_put_contents($encryptedPath, $encrypted);
        
        // Securely delete original
        unlink($filePath);
        
        return $encryptedPath;
    }
    
    public function decryptFile(string $encryptedPath): string
    {
        $encrypted = file_get_contents($encryptedPath);
        return decrypt($encrypted);
    }
}
```

### Password Security

#### Password Hashing
```php
class PasswordService
{
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }
    
    public function needsRehash(string $hash): bool
    {
        return Hash::needsRehash($hash);
    }
}
```

#### Password Policy
```php
class PasswordPolicy
{
    public function validate(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
}
```

## 🔐 Payment Security

### Stripe Integration Security

#### Secure Payment Processing
```php
class SecureStripeService
{
    private $stripe;
    
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }
    
    public function createSecureCheckoutSession(array $params): array
    {
        // Validate parameters server-side
        $validatedParams = $this->validateCheckoutParams($params);
        
        // Create transaction record before Stripe call
        $transaction = PaymentTransaction::create([
            'user_id' => auth()->id(),
            'package_id' => $validatedParams['package_id'],
            'amount' => $this->getPackageAmount($validatedParams['package_id']),
            'currency' => 'usd',
            'status' => 'pending',
            'metadata' => $validatedParams['metadata'] ?? [],
        ]);
        
        try {
            $session = $this->stripe->checkout->sessions->create([
                'success_url' => $validatedParams['success_url'],
                'cancel_url' => $validatedParams['cancel_url'],
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => $this->getPackageAmount($validatedParams['package_id']),
                            'product_data' => [
                                'name' => $this->getPackageName($validatedParams['package_id']),
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'user_id' => auth()->id(),
                ],
            ]);
            
            // Update transaction with Stripe session ID
            $transaction->update([
                'stripe_session_id' => $session->id,
            ]);
            
            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];
            
        } catch (\Exception $e) {
            // Update transaction status
            $transaction->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    private function validateCheckoutParams(array $params): array
    {
        $validator = Validator::make($params, [
            'package_id' => 'required|in:starter,professional,enterprise',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'metadata' => 'array',
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }
}
```

#### Webhook Security
```php
class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('stripe-signature');
        
        try {
            // Verify webhook signature
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
            
            // Process event securely
            $this->processWebhookEvent($event);
            
            return response()->json(['success' => true]);
            
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            SecurityLogger::log('webhook_signature_invalid', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'error' => $e->getMessage(),
            ]);
            
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
    
    private function processWebhookEvent($event)
    {
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
                
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
                
            default:
                SecurityLogger::log('webhook_unknown_event', [
                    'event_type' => $event->type,
                    'event_id' => $event->id,
                ]);
        }
    }
}
```

## 🛡️ Security Monitoring & Logging

### Security Event Logging

#### Security Logger Implementation
```php
class SecurityLogger
{
    public static function log(string $event, array $data = []): void
    {
        $logData = [
            'event' => $event,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'data' => $data,
        ];
        
        // Log to security channel
        Log::channel('security')->info($event, $logData);
        
        // Send to monitoring service
        if (config('app.env') === 'production') {
            MonitoringService::alert($event, $logData);
        }
    }
}
```

#### Monitored Security Events
```php
class SecurityEvents
{
    const EVENTS = [
        'login_attempt' => 'User login attempt',
        'login_success' => 'Successful login',
        'login_failure' => 'Failed login attempt',
        'logout' => 'User logout',
        'password_change' => 'Password changed',
        'email_change' => 'Email address changed',
        'payment_created' => 'Payment transaction created',
        'payment_completed' => 'Payment completed',
        'payment_failed' => 'Payment failed',
        'unauthorized_access' => 'Unauthorized access attempt',
        'permission_denied' => 'Permission denied',
        'api_rate_limit' => 'API rate limit exceeded',
        'suspicious_activity' => 'Suspicious activity detected',
    ];
}
```

### Intrusion Detection

#### Suspicious Activity Detection
```php
class IntrusionDetectionService
{
    public function detectSuspiciousActivity(Request $request): bool
    {
        $suspiciousPatterns = [
            'rapid_requests' => $this->detectRapidRequests($request),
            'sql_injection' => $this->detectSQLInjection($request),
            'xss_attempt' => $this->detectXSSAttempt($request),
            'unusual_user_agent' => $this->detectUnusualUserAgent($request),
            'geo_anomaly' => $this->detectGeoAnomaly($request),
        ];
        
        $suspiciousCount = array_sum($suspiciousPatterns);
        
        if ($suspiciousCount >= 2) {
            SecurityLogger::log('suspicious_activity', [
                'patterns' => $suspiciousPatterns,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return true;
        }
        
        return false;
    }
    
    private function detectRapidRequests(Request $request): bool
    {
        $key = 'requests_' . $request->ip();
        $requests = Cache::get($key, 0);
        
        if ($requests > 100) { // More than 100 requests per minute
            return true;
        }
        
        Cache::put($key, $requests + 1, 60);
        return false;
    }
}
```

## 🔐 CSRF Protection

### CSRF Token Implementation
```php
// Middleware configuration
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/api/webhook/stripe', // Stripe webhooks
    ];
    
    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->shouldPassThrough($request)) {
            return $next($request);
        }
        
        if (!$this->tokensMatch($request)) {
            SecurityLogger::log('csrf_token_mismatch', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
            ]);
            
            throw new TokenMismatchException('CSRF token mismatch');
        }
        
        return $next($request);
    }
}
```

### Frontend CSRF Protection
```javascript
// Automatic CSRF token inclusion
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Form submission with CSRF protection
async function submitForm(formData) {
    try {
        const response = await axios.post('/api/endpoint', formData);
        return response.data;
    } catch (error) {
        if (error.response?.status === 419) {
            // CSRF token expired - refresh page
            window.location.reload();
        }
        throw error;
    }
}
```

## 🔒 Rate Limiting & DDoS Protection

### API Rate Limiting
```php
// Rate limiting configuration
class RateLimitingService
{
    public function configureRateLimits(): void
    {
        // Standard API endpoints
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        
        // Payment endpoints - stricter limits
        RateLimiter::for('payments', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
        
        // Authentication endpoints
        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perHour(20)->by($request->ip()),
            ];
        });
    }
}
```

### DDoS Protection
```php
class DDoSProtectionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'ddos_protection_' . $request->ip();
        $requests = Cache::get($key, 0);
        
        // Block if too many requests
        if ($requests > 1000) {
            SecurityLogger::log('ddos_attempt', [
                'ip_address' => $request->ip(),
                'requests_count' => $requests,
            ]);
            
            return response()->json(['error' => 'Too many requests'], 429);
        }
        
        Cache::put($key, $requests + 1, 60);
        
        return $next($request);
    }
}
```

## 🔐 Security Headers

### HTTP Security Headers
```php
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://js.stripe.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https://api.stripe.com",
            "font-src 'self' https://fonts.gstatic.com",
            "frame-src https://js.stripe.com",
        ];
        
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        
        // HSTS for HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}
```

## 🔐 Incident Response

### Security Incident Handling
```php
class SecurityIncidentHandler
{
    public function handleIncident(string $type, array $data): void
    {
        $incident = SecurityIncident::create([
            'type' => $type,
            'severity' => $this->calculateSeverity($type),
            'data' => $data,
            'status' => 'open',
            'created_at' => now(),
        ]);
        
        // Alert security team
        $this->alertSecurityTeam($incident);
        
        // Auto-remediation for certain incident types
        $this->autoRemediate($incident);
        
        // Log incident
        SecurityLogger::log('security_incident', [
            'incident_id' => $incident->id,
            'type' => $type,
            'severity' => $incident->severity,
        ]);
    }
    
    private function autoRemediate(SecurityIncident $incident): void
    {
        switch ($incident->type) {
            case 'brute_force_attack':
                $this->blockIPAddress($incident->data['ip_address']);
                break;
                
            case 'sql_injection_attempt':
                $this->blockIPAddress($incident->data['ip_address']);
                break;
                
            case 'unusual_payment_activity':
                $this->flagUserForReview($incident->data['user_id']);
                break;
        }
    }
}
```

## 🔒 Compliance & Auditing

### GDPR Compliance
```php
class GDPRComplianceService
{
    public function handleDataRequest(User $user, string $type): void
    {
        switch ($type) {
            case 'export':
                $this->exportUserData($user);
                break;
                
            case 'delete':
                $this->deleteUserData($user);
                break;
                
            case 'anonymize':
                $this->anonymizeUserData($user);
                break;
        }
        
        SecurityLogger::log('gdpr_request', [
            'user_id' => $user->id,
            'request_type' => $type,
        ]);
    }
    
    private function exportUserData(User $user): void
    {
        $data = [
            'user_profile' => $user->toArray(),
            'sites' => $user->sites()->get()->toArray(),
            'payments' => $user->paymentTransactions()->get()->toArray(),
            'activity_logs' => $user->activityLogs()->get()->toArray(),
        ];
        
        // Create encrypted export file
        $exportFile = storage_path('app/exports/user_' . $user->id . '_' . time() . '.json');
        file_put_contents($exportFile, encrypt(json_encode($data)));
        
        // Send to user
        Mail::to($user)->send(new DataExportMail($exportFile));
    }
}
```

### Security Audit Trail
```php
class SecurityAuditService
{
    public function auditAction(string $action, $model, array $changes = []): void
    {
        SecurityAudit::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
```

## 🔐 Security Testing

### Automated Security Testing
```php
class SecurityTestSuite extends TestCase
{
    public function testSQLInjectionProtection()
    {
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->post('/api/sites', [
            'name' => $maliciousInput,
            'domain' => 'test',
        ]);
        
        $response->assertStatus(422); // Validation should fail
        $this->assertDatabaseHas('users', ['id' => 1]); // Users table should still exist
    }
    
    public function testXSSProtection()
    {
        $xssPayload = '<script>alert("XSS")</script>';
        
        $response = $this->post('/api/sites', [
            'name' => $xssPayload,
            'domain' => 'test',
        ]);
        
        $response->assertStatus(422); // Should be blocked by validation
    }
    
    public function testRateLimiting()
    {
        for ($i = 0; $i < 70; $i++) {
            $response = $this->get('/api/health');
            
            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Rate limit exceeded
            }
        }
    }
}
```

## 📋 Security Checklist

### Development Security Checklist
- [ ] Input validation implemented for all user inputs
- [ ] Output encoding used for all dynamic content
- [ ] SQL injection prevention via parameterized queries
- [ ] XSS protection via input sanitization
- [ ] CSRF tokens implemented for all forms
- [ ] Authentication and authorization properly implemented
- [ ] Sensitive data encrypted at rest and in transit
- [ ] Security headers configured
- [ ] Rate limiting implemented
- [ ] Security logging enabled
- [ ] Dependency vulnerabilities scanned
- [ ] Security tests written and passing

### Production Security Checklist
- [ ] HTTPS enforced site-wide
- [ ] Security headers configured
- [ ] WAF deployed and configured
- [ ] DDoS protection enabled
- [ ] Intrusion detection system active
- [ ] Security monitoring alerts configured
- [ ] Regular security audits scheduled
- [ ] Incident response plan in place
- [ ] Backup and recovery procedures tested
- [ ] Staff security training completed

## 📞 Security Contact

### Reporting Security Issues
- **Security Email**: security@mewayz.com
- **GPG Key**: Available on request
- **Response Time**: Within 24 hours
- **Disclosure Policy**: Responsible disclosure preferred

### Security Resources
- **Security Documentation**: This document
- **Security Policies**: Available on request
- **Security Updates**: Check release notes
- **Security Training**: Available for team members

---

**Last Updated**: January 16, 2025  
**Security Review**: Quarterly  
**Next Audit**: April 2025

*Security is an ongoing process. This document is regularly updated to reflect the latest security measures and best practices.*