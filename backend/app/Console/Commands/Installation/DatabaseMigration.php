<?php

namespace App\Console\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandy:database_migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate script database tables.';

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
        try {
            Artisan::call('migrate', ["--force" => true]);
            Artisan::call('db:seed', ['--force' => true]);
            Artisan::call('key:generate', ["--force"=> true]);
        }catch(\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->info('Database has been migrated successfully.');
    }
}
