<?php

namespace App\Console\Commands\Installation;

use Illuminate\Console\Command;

class Database extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandy:update_database_config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update script database config.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){


        $host = $this->ask('What\'s your database host? (default: localhost.)');
        $port = $this->anticipate('What\'s your database port? (default: 3306.)', ['3306']);
        $database = $this->ask('What\'s your database name?');
        $username = $this->ask('What\'s your database username?');
        $password = $this->ask('What\'s your database password?');


        $database = [
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $password
        ];


        env_update($database);

        return $this->info('Database config has been updated successfully.');
    }
}
