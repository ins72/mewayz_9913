<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Exception;

class SetupController extends Controller
{
    public function index(Request $request)
    {
        // If already setup, redirect to main app
        if (File::exists(base_path('.env')) && config('app.key')) {
            return redirect('/');
        }

        $step = $request->get('step', 1);
        $errors = session('errors', []);
        $success = session('success', '');

        return view('setup.wizard', compact('step', 'errors', 'success'));
    }

    public function process(Request $request)
    {
        $step = $request->input('step', 1);

        try {
            switch ($step) {
                case 1:
                    return $this->processAppConfig($request);
                case 2:
                    return $this->processDatabaseConfig($request);
                case 3:
                    return $this->processAdminConfig($request);
                case 4:
                    return $this->processEmailConfig($request);
                case 5:
                    return $this->processInstallation($request);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('errors', [$e->getMessage()]);
        }

        return redirect()->back();
    }

    private function processAppConfig(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_env' => 'required|in:production,local',
            'app_debug' => 'required|in:true,false'
        ]);

        session(['app_config' => $request->only('app_name', 'app_url', 'app_env', 'app_debug')]);
        return redirect('/setup?step=2');
    }

    private function processDatabaseConfig(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string'
        ]);

        // Test database connection
        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port}",
                $request->db_username,
                $request->db_password
            );
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$request->db_database}`");
            
            session(['db_config' => $request->only('db_host', 'db_port', 'db_database', 'db_username', 'db_password')]);
            return redirect('/setup?step=3');
            
        } catch (Exception $e) {
            return redirect()->back()->with('errors', ['Database connection failed: ' . $e->getMessage()]);
        }
    }

    private function processAdminConfig(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:6'
        ]);

        session(['admin_config' => $request->only('admin_name', 'admin_email', 'admin_password')]);
        return redirect('/setup?step=4');
    }

    private function processEmailConfig(Request $request)
    {
        $emailConfig = $request->only('mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address');
        session(['email_config' => $emailConfig]);
        return redirect('/setup?step=5');
    }

    private function processInstallation(Request $request)
    {
        // Generate .env file
        $this->createEnvFile();
        
        // Run installation
        $this->runInstallation();
        
        return redirect('/setup?step=6')->with('success', 'Installation completed successfully!');
    }

    private function createEnvFile()
    {
        $appConfig = session('app_config', []);
        $dbConfig = session('db_config', []);
        $adminConfig = session('admin_config', []);
        $emailConfig = session('email_config', []);

        $appKey = 'base64:' . base64_encode(random_bytes(32));

        $envContent = "APP_NAME=\"{$appConfig['app_name']}\"
APP_ENV={$appConfig['app_env']}
APP_KEY={$appKey}
APP_DEBUG={$appConfig['app_debug']}
APP_URL={$appConfig['app_url']}

LOG_CHANNEL=stack
LOG_LEVEL=" . ($appConfig['app_env'] === 'production' ? 'error' : 'debug') . "

DB_CONNECTION=mysql
DB_HOST={$dbConfig['db_host']}
DB_PORT={$dbConfig['db_port']}
DB_DATABASE={$dbConfig['db_database']}
DB_USERNAME={$dbConfig['db_username']}
DB_PASSWORD={$dbConfig['db_password']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER={$emailConfig['mail_mailer']}
MAIL_HOST={$emailConfig['mail_host']}
MAIL_PORT={$emailConfig['mail_port']}
MAIL_USERNAME={$emailConfig['mail_username']}
MAIL_PASSWORD={$emailConfig['mail_password']}
MAIL_ENCRYPTION={$emailConfig['mail_encryption']}
MAIL_FROM_ADDRESS={$emailConfig['mail_from_address']}
MAIL_FROM_NAME=\"{$appConfig['app_name']}\"

ADMIN_NAME=\"{$adminConfig['admin_name']}\"
ADMIN_EMAIL={$adminConfig['admin_email']}
ADMIN_PASSWORD={$adminConfig['admin_password']}
";

        File::put(base_path('.env'), $envContent);
    }

    private function runInstallation()
    {
        // Clear config cache
        Artisan::call('config:clear');
        
        // Run migrations
        Artisan::call('migrate', ['--force' => true, '--seed' => true]);
        
        // Create admin user
        $adminConfig = session('admin_config', []);
        \App\Models\User::create([
            'name' => $adminConfig['admin_name'],
            'email' => $adminConfig['admin_email'],
            'password' => bcrypt($adminConfig['admin_password']),
            'email_verified_at' => now(),
            'is_admin' => true
        ]);
        
        // Cache config for production
        if (session('app_config.app_env') === 'production') {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
        }
    }
}