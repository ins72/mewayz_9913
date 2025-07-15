<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SandyUpdateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandy:update_database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare and update the database';

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
            DB::table('migrations')->where('migration', '2023_12_28_092315_updates')->delete();
            Artisan::call('migrate', ["--force" => true]);

            $this->info("Database updated successfully");
          }catch(\Exception $e) {

            $this->info($e->getMessage());
          }
    }
}
