<?php
session_start();

// Check if setup is already completed
if (file_exists('/var/www/html/.env') && !isset($_GET['reconfigure'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . ':80');
    exit;
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            $_SESSION['app_config'] = [
                'name' => $_POST['app_name'] ?? 'Mewayz',
                'url' => $_POST['app_url'] ?? 'http://' . $_SERVER['HTTP_HOST'],
                'env' => $_POST['app_env'] ?? 'production',
                'debug' => $_POST['app_debug'] ?? 'false'
            ];
            header('Location: ?step=2');
            exit;
            
        case 2:
            $_SESSION['db_config'] = [
                'host' => $_POST['db_host'] ?? 'localhost',
                'port' => $_POST['db_port'] ?? '3306',
                'database' => $_POST['db_database'] ?? 'mewayz',
                'username' => $_POST['db_username'] ?? 'root',
                'password' => $_POST['db_password'] ?? ''
            ];
            
            // Test database connection
            try {
                $pdo = new PDO(
                    "mysql:host={$_SESSION['db_config']['host']};port={$_SESSION['db_config']['port']}",
                    $_SESSION['db_config']['username'],
                    $_SESSION['db_config']['password']
                );
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_SESSION['db_config']['database']}`");
                header('Location: ?step=3');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Database connection failed: ' . $e->getMessage();
            }
            break;
            
        case 3:
            $_SESSION['admin_config'] = [
                'name' => $_POST['admin_name'] ?? 'Admin',
                'email' => $_POST['admin_email'] ?? 'admin@example.com',
                'password' => $_POST['admin_password'] ?? 'admin123'
            ];
            header('Location: ?step=4');
            exit;
            
        case 4:
            $_SESSION['email_config'] = [
                'mailer' => $_POST['mail_mailer'] ?? 'log',
                'host' => $_POST['mail_host'] ?? 'localhost',
                'port' => $_POST['mail_port'] ?? '587',
                'username' => $_POST['mail_username'] ?? '',
                'password' => $_POST['mail_password'] ?? '',
                'encryption' => $_POST['mail_encryption'] ?? 'tls',
                'from_address' => $_POST['mail_from_address'] ?? 'noreply@example.com'
            ];
            header('Location: ?step=5');
            exit;
            
        case 5:
            // Install the application
            if (installApplication()) {
                $success = 'Installation completed successfully!';
            } else {
                $errors[] = 'Installation failed. Please check the logs.';
            }
            break;
    }
}

function installApplication() {
    try {
        // Create .env file
        $envContent = generateEnvFile();
        file_put_contents('/var/www/html/.env', $envContent);
        
        // Create install script
        $installScript = generateInstallScript();
        file_put_contents('/var/www/html/install.sh', $installScript);
        chmod('/var/www/html/install.sh', 0755);
        
        // Execute installation in background
        exec('nohup /var/www/html/install.sh > /var/log/install.log 2>&1 &');
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function generateEnvFile() {
    $app = $_SESSION['app_config'];
    $db = $_SESSION['db_config'];
    $admin = $_SESSION['admin_config'];
    $email = $_SESSION['email_config'];
    
    $appKey = 'base64:' . base64_encode(random_bytes(32));
    
    return "APP_NAME=\"{$app['name']}\"
APP_ENV={$app['env']}
APP_KEY={$appKey}
APP_DEBUG={$app['debug']}
APP_URL={$app['url']}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$db['host']}
DB_PORT={$db['port']}
DB_DATABASE={$db['database']}
DB_USERNAME={$db['username']}
DB_PASSWORD={$db['password']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER={$email['mailer']}
MAIL_HOST={$email['host']}
MAIL_PORT={$email['port']}
MAIL_USERNAME={$email['username']}
MAIL_PASSWORD={$email['password']}
MAIL_ENCRYPTION={$email['encryption']}
MAIL_FROM_ADDRESS={$email['from_address']}
MAIL_FROM_NAME=\"{$app['name']}\"

ADMIN_NAME=\"{$admin['name']}\"
ADMIN_EMAIL={$admin['email']}
ADMIN_PASSWORD={$admin['password']}
";
}

function generateInstallScript() {
    return '#!/bin/bash
cd /var/www/html

# Install dependencies
composer install --optimize-autoloader --no-dev --no-interaction
npm ci && npm run build

# Create directories
mkdir -p resources/views/livewire storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Run migrations
php artisan migrate --force --seed

# Create admin user
php artisan tinker --execute="
\\App\\Models\\User::create([
    \'name\' => env(\'ADMIN_NAME\', \'Admin\'),
    \'email\' => env(\'ADMIN_EMAIL\', \'admin@example.com\'),
    \'password\' => bcrypt(env(\'ADMIN_PASSWORD\', \'admin123\')),
    \'email_verified_at\' => now(),
    \'is_admin\' => true
]);"

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create success flag
touch /var/www/html/.setup_complete

echo "Installation completed successfully!"
';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mewayz v2 Setup Wizard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .setup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            margin: 1rem;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo h1 {
            color: #667eea;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .logo p {
            color: #64748b;
            font-size: 1.1rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            position: relative;
        }
        
        .step.active {
            background: #667eea;
        }
        
        .step.completed {
            background: #10b981;
        }
        
        .step.pending {
            background: #e5e7eb;
            color: #9ca3af;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 2px;
            background: #e5e7eb;
        }
        
        .step.completed:not(:last-child)::after {
            background: #10b981;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background: #5a67d8;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #374151;
        }
        
        .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #fecaca;
        }
        
        .success {
            background: #f0fdf4;
            color: #16a34a;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #bbf7d0;
        }
        
        .navigation {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .navigation .btn {
            flex: 1;
        }
        
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-box h3 {
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .info-box p {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
        }
        
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .final-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .final-info h3 {
            color: #0369a1;
            margin-bottom: 1rem;
        }
        
        .final-info ul {
            list-style: none;
            padding: 0;
        }
        
        .final-info li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0f2fe;
        }
        
        .final-info li:last-child {
            border-bottom: none;
        }
        
        .final-info strong {
            color: #0c4a6e;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="logo">
            <h1>🚀 Mewayz v2</h1>
            <p>Setup Wizard</p>
        </div>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? ($step == 1 ? 'active' : 'completed') : 'pending'; ?>">1</div>
            <div class="step <?php echo $step >= 2 ? ($step == 2 ? 'active' : 'completed') : 'pending'; ?>">2</div>
            <div class="step <?php echo $step >= 3 ? ($step == 3 ? 'active' : 'completed') : 'pending'; ?>">3</div>
            <div class="step <?php echo $step >= 4 ? ($step == 4 ? 'active' : 'completed') : 'pending'; ?>">4</div>
            <div class="step <?php echo $step >= 5 ? ($step == 5 ? 'active' : 'completed') : 'pending'; ?>">5</div>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <h2>Application Configuration</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="app_name">Application Name</label>
                    <input type="text" id="app_name" name="app_name" value="<?php echo $_SESSION['app_config']['name'] ?? 'Mewayz'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="app_url">Application URL</label>
                    <input type="url" id="app_url" name="app_url" value="<?php echo $_SESSION['app_config']['url'] ?? 'http://' . $_SERVER['HTTP_HOST']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="app_env">Environment</label>
                    <select id="app_env" name="app_env" required>
                        <option value="production" <?php echo ($_SESSION['app_config']['env'] ?? 'production') == 'production' ? 'selected' : ''; ?>>Production</option>
                        <option value="local" <?php echo ($_SESSION['app_config']['env'] ?? '') == 'local' ? 'selected' : ''; ?>>Development</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="app_debug">Debug Mode</label>
                    <select id="app_debug" name="app_debug" required>
                        <option value="false" <?php echo ($_SESSION['app_config']['debug'] ?? 'false') == 'false' ? 'selected' : ''; ?>>Disabled (Recommended)</option>
                        <option value="true" <?php echo ($_SESSION['app_config']['debug'] ?? '') == 'true' ? 'selected' : ''; ?>>Enabled</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Next: Database Configuration</button>
            </form>
        <?php endif; ?>
        
        <?php if ($step == 2): ?>
            <h2>Database Configuration</h2>
            <div class="info-box">
                <h3>Database Requirements</h3>
                <p>Make sure your MySQL/MariaDB server is running and accessible. The wizard will create the database automatically if it doesn't exist.</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="<?php echo $_SESSION['db_config']['host'] ?? 'localhost'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_port">Database Port</label>
                    <input type="number" id="db_port" name="db_port" value="<?php echo $_SESSION['db_config']['port'] ?? '3306'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_database">Database Name</label>
                    <input type="text" id="db_database" name="db_database" value="<?php echo $_SESSION['db_config']['database'] ?? 'mewayz'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_username">Database Username</label>
                    <input type="text" id="db_username" name="db_username" value="<?php echo $_SESSION['db_config']['username'] ?? 'root'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_password">Database Password</label>
                    <input type="password" id="db_password" name="db_password" value="<?php echo $_SESSION['db_config']['password'] ?? ''; ?>">
                </div>
                
                <div class="navigation">
                    <a href="?step=1" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Test & Continue</button>
                </div>
            </form>
        <?php endif; ?>
        
        <?php if ($step == 3): ?>
            <h2>Admin User Setup</h2>
            <div class="info-box">
                <h3>Administrator Account</h3>
                <p>This account will have full access to your Mewayz platform. You can create additional users later.</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="admin_name">Full Name</label>
                    <input type="text" id="admin_name" name="admin_name" value="<?php echo $_SESSION['admin_config']['name'] ?? 'Admin User'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email Address</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo $_SESSION['admin_config']['email'] ?? 'admin@example.com'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" id="admin_password" name="admin_password" value="<?php echo $_SESSION['admin_config']['password'] ?? ''; ?>" required minlength="6">
                </div>
                
                <div class="navigation">
                    <a href="?step=2" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Next: Email Configuration</button>
                </div>
            </form>
        <?php endif; ?>
        
        <?php if ($step == 4): ?>
            <h2>Email Configuration</h2>
            <div class="info-box">
                <h3>Email Settings (Optional)</h3>
                <p>Configure email settings for notifications, password resets, and other features. You can skip this and configure later.</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="mail_mailer">Mail Driver</label>
                    <select id="mail_mailer" name="mail_mailer" required>
                        <option value="log" <?php echo ($_SESSION['email_config']['mailer'] ?? 'log') == 'log' ? 'selected' : ''; ?>>Log (No Email)</option>
                        <option value="smtp" <?php echo ($_SESSION['email_config']['mailer'] ?? '') == 'smtp' ? 'selected' : ''; ?>>SMTP</option>
                        <option value="sendmail" <?php echo ($_SESSION['email_config']['mailer'] ?? '') == 'sendmail' ? 'selected' : ''; ?>>Sendmail</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mail_host">SMTP Host</label>
                    <input type="text" id="mail_host" name="mail_host" value="<?php echo $_SESSION['email_config']['host'] ?? 'smtp.gmail.com'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mail_port">SMTP Port</label>
                    <input type="number" id="mail_port" name="mail_port" value="<?php echo $_SESSION['email_config']['port'] ?? '587'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mail_username">SMTP Username</label>
                    <input type="text" id="mail_username" name="mail_username" value="<?php echo $_SESSION['email_config']['username'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mail_password">SMTP Password</label>
                    <input type="password" id="mail_password" name="mail_password" value="<?php echo $_SESSION['email_config']['password'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mail_encryption">Encryption</label>
                    <select id="mail_encryption" name="mail_encryption">
                        <option value="tls" <?php echo ($_SESSION['email_config']['encryption'] ?? 'tls') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($_SESSION['email_config']['encryption'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        <option value="null" <?php echo ($_SESSION['email_config']['encryption'] ?? '') == 'null' ? 'selected' : ''; ?>>None</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mail_from_address">From Email Address</label>
                    <input type="email" id="mail_from_address" name="mail_from_address" value="<?php echo $_SESSION['email_config']['from_address'] ?? 'noreply@example.com'; ?>" required>
                </div>
                
                <div class="navigation">
                    <a href="?step=3" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Next: Review & Install</button>
                </div>
            </form>
        <?php endif; ?>
        
        <?php if ($step == 5): ?>
            <h2>Review & Install</h2>
            
            <?php if (!$success): ?>
                <div class="final-info">
                    <h3>Configuration Summary</h3>
                    <ul>
                        <li><strong>Application:</strong> <?php echo htmlspecialchars($_SESSION['app_config']['name']); ?></li>
                        <li><strong>URL:</strong> <?php echo htmlspecialchars($_SESSION['app_config']['url']); ?></li>
                        <li><strong>Environment:</strong> <?php echo htmlspecialchars($_SESSION['app_config']['env']); ?></li>
                        <li><strong>Database:</strong> <?php echo htmlspecialchars($_SESSION['db_config']['database']); ?> @ <?php echo htmlspecialchars($_SESSION['db_config']['host']); ?></li>
                        <li><strong>Admin Email:</strong> <?php echo htmlspecialchars($_SESSION['admin_config']['email']); ?></li>
                        <li><strong>Mail Driver:</strong> <?php echo htmlspecialchars($_SESSION['email_config']['mailer']); ?></li>
                    </ul>
                </div>
                
                <form method="POST">
                    <div class="navigation">
                        <a href="?step=4" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn">Install Mewayz v2</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="loading">
                    <div class="spinner"></div>
                    <h3>Installation in Progress</h3>
                    <p>Your Mewayz v2 platform is being installed. This may take a few minutes...</p>
                </div>
                
                <div class="final-info">
                    <h3>Access Your Platform</h3>
                    <ul>
                        <li><strong>Application URL:</strong> <a href="<?php echo $_SESSION['app_config']['url']; ?>" target="_blank"><?php echo $_SESSION['app_config']['url']; ?></a></li>
                        <li><strong>Admin Email:</strong> <?php echo htmlspecialchars($_SESSION['admin_config']['email']); ?></li>
                        <li><strong>Admin Password:</strong> [Your chosen password]</li>
                    </ul>
                </div>
                
                <script>
                    // Redirect to main application after 30 seconds
                    setTimeout(function() {
                        window.location.href = '<?php echo $_SESSION['app_config']['url']; ?>';
                    }, 30000);
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>