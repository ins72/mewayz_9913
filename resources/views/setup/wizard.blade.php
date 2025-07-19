<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mewayz v2 Setup</title>
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
            padding: 1rem;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
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
        
        .steps {
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
            font-size: 0.875rem;
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
            width: 30px;
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
        
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        
        .navigation {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .navigation .btn {
            flex: 1;
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
            font-size: 1.1rem;
        }
        
        .info-box p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
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
        
        h2 {
            color: #1f2937;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>🚀 Mewayz v2</h1>
            <p>CloudPanel Setup</p>
        </div>
        
        <div class="steps">
            <div class="step {{ $step >= 1 ? ($step == 1 ? 'active' : 'completed') : 'pending' }}">1</div>
            <div class="step {{ $step >= 2 ? ($step == 2 ? 'active' : 'completed') : 'pending' }}">2</div>
            <div class="step {{ $step >= 3 ? ($step == 3 ? 'active' : 'completed') : 'pending' }}">3</div>
            <div class="step {{ $step >= 4 ? ($step == 4 ? 'active' : 'completed') : 'pending' }}">4</div>
            <div class="step {{ $step >= 5 ? ($step == 5 ? 'active' : 'completed') : 'pending' }}">5</div>
        </div>
        
        @if($errors && count($errors) > 0)
            <div class="error">
                @foreach($errors as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        @if($success)
            <div class="success">
                <p>{{ $success }}</p>
            </div>
        @endif
        
        @if($step == 1)
            <h2>Application Settings</h2>
            <div class="info-box">
                <h3>CloudPanel Configuration</h3>
                <p>Configure your Mewayz v2 application for CloudPanel deployment.</p>
            </div>
            
            <form method="POST" action="{{ route('setup.process') }}">
                @csrf
                <input type="hidden" name="step" value="1">
                
                <div class="form-group">
                    <label for="app_name">Application Name</label>
                    <input type="text" id="app_name" name="app_name" value="{{ session('app_config.app_name', 'Mewayz') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="app_url">Application URL</label>
                    <input type="url" id="app_url" name="app_url" value="{{ session('app_config.app_url', 'https://' . request()->getHost()) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="app_env">Environment</label>
                    <select id="app_env" name="app_env" required>
                        <option value="production" {{ session('app_config.app_env', 'production') == 'production' ? 'selected' : '' }}>Production</option>
                        <option value="local" {{ session('app_config.app_env') == 'local' ? 'selected' : '' }}>Development</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="app_debug">Debug Mode</label>
                    <select id="app_debug" name="app_debug" required>
                        <option value="false" {{ session('app_config.app_debug', 'false') == 'false' ? 'selected' : '' }}>Disabled (Recommended)</option>
                        <option value="true" {{ session('app_config.app_debug') == 'true' ? 'selected' : '' }}>Enabled</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Next: Database Setup</button>
            </form>
        @endif
        
        @if($step == 2)
            <h2>Database Configuration</h2>
            <div class="info-box">
                <h3>CloudPanel Database</h3>
                <p>Enter your CloudPanel database credentials. Make sure your database is created in CloudPanel first.</p>
            </div>
            
            <form method="POST" action="{{ route('setup.process') }}">
                @csrf
                <input type="hidden" name="step" value="2">
                
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="{{ session('db_config.db_host', '127.0.0.1') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="db_port">Database Port</label>
                    <input type="number" id="db_port" name="db_port" value="{{ session('db_config.db_port', '3306') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="db_database">Database Name</label>
                    <input type="text" id="db_database" name="db_database" value="{{ session('db_config.db_database', 'mewayz-test') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="db_username">Database Username</label>
                    <input type="text" id="db_username" name="db_username" value="{{ session('db_config.db_username', 'mewayz-test') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="db_password">Database Password</label>
                    <input type="password" id="db_password" name="db_password" value="{{ session('db_config.db_password', '') }}">
                </div>
                
                <div class="navigation">
                    <a href="/setup?step=1" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Test & Continue</button>
                </div>
            </form>
        @endif
        
        @if($step == 3)
            <h2>Admin Account</h2>
            <div class="info-box">
                <h3>Administrator Setup</h3>
                <p>Create your admin account to manage your Mewayz v2 platform.</p>
            </div>
            
            <form method="POST" action="{{ route('setup.process') }}">
                @csrf
                <input type="hidden" name="step" value="3">
                
                <div class="form-group">
                    <label for="admin_name">Full Name</label>
                    <input type="text" id="admin_name" name="admin_name" value="{{ session('admin_config.admin_name', 'Admin User') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email Address</label>
                    <input type="email" id="admin_email" name="admin_email" value="{{ session('admin_config.admin_email', 'admin@' . request()->getHost()) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" id="admin_password" name="admin_password" value="{{ session('admin_config.admin_password', '') }}" required minlength="6">
                </div>
                
                <div class="navigation">
                    <a href="/setup?step=2" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Next: Email Setup</button>
                </div>
            </form>
        @endif
        
        @if($step == 4)
            <h2>Email Configuration</h2>
            <div class="info-box">
                <h3>Email Settings (Optional)</h3>
                <p>Configure email settings for notifications and password resets. You can skip this step.</p>
            </div>
            
            <form method="POST" action="{{ route('setup.process') }}">
                @csrf
                <input type="hidden" name="step" value="4">
                
                <div class="form-group">
                    <label for="mail_mailer">Mail Driver</label>
                    <select id="mail_mailer" name="mail_mailer" required>
                        <option value="log" {{ session('email_config.mail_mailer', 'log') == 'log' ? 'selected' : '' }}>Log (No Email)</option>
                        <option value="smtp" {{ session('email_config.mail_mailer') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ session('email_config.mail_mailer') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mail_host">SMTP Host</label>
                    <input type="text" id="mail_host" name="mail_host" value="{{ session('email_config.mail_host', 'smtp.gmail.com') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail_port">SMTP Port</label>
                    <input type="number" id="mail_port" name="mail_port" value="{{ session('email_config.mail_port', '587') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail_username">SMTP Username</label>
                    <input type="text" id="mail_username" name="mail_username" value="{{ session('email_config.mail_username', '') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail_password">SMTP Password</label>
                    <input type="password" id="mail_password" name="mail_password" value="{{ session('email_config.mail_password', '') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail_encryption">Encryption</label>
                    <select id="mail_encryption" name="mail_encryption">
                        <option value="tls" {{ session('email_config.mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ session('email_config.mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ session('email_config.mail_encryption') == 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mail_from_address">From Email</label>
                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ session('email_config.mail_from_address', 'noreply@' . request()->getHost()) }}" required>
                </div>
                
                <div class="navigation">
                    <a href="/setup?step=3" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Next: Install</button>
                </div>
            </form>
        @endif
        
        @if($step == 5)
            <h2>Ready to Install</h2>
            
            <div class="final-info">
                <h3>Configuration Summary</h3>
                <ul>
                    <li><strong>Application:</strong> {{ session('app_config.app_name') }}</li>
                    <li><strong>URL:</strong> {{ session('app_config.app_url') }}</li>
                    <li><strong>Environment:</strong> {{ session('app_config.app_env') }}</li>
                    <li><strong>Database:</strong> {{ session('db_config.db_database') }} @ {{ session('db_config.db_host') }}</li>
                    <li><strong>Admin:</strong> {{ session('admin_config.admin_email') }}</li>
                    <li><strong>Email:</strong> {{ session('email_config.mail_mailer') }}</li>
                </ul>
            </div>
            
            <form method="POST" action="{{ route('setup.process') }}">
                @csrf
                <input type="hidden" name="step" value="5">
                
                <div class="navigation">
                    <a href="/setup?step=4" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn">Install Mewayz v2</button>
                </div>
            </form>
        @endif
        
        @if($step == 6)
            <div class="loading">
                <div class="spinner"></div>
                <h3>Installation Complete!</h3>
                <p>Your Mewayz v2 platform is ready to use.</p>
            </div>
            
            <div class="final-info">
                <h3>Access Your Platform</h3>
                <ul>
                    <li><strong>Application:</strong> <a href="{{ session('app_config.app_url') }}">{{ session('app_config.app_url') }}</a></li>
                    <li><strong>Admin Email:</strong> {{ session('admin_config.admin_email') }}</li>
                    <li><strong>CloudPanel:</strong> Manage via CloudPanel dashboard</li>
                </ul>
            </div>
            
            <a href="{{ session('app_config.app_url') }}" class="btn">Access Your Platform</a>
        @endif
    </div>
</body>
</html>