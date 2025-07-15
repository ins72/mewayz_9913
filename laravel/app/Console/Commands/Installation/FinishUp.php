<?php

namespace App\Console\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;

class FinishUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandy:installation_finish_up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finish up installation.';

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
        // Last Stuff
        $update_env = [
            'APP_DEBUG' => false,
            'APP_ENV' => 'production',
            'APP_INSTALL' => 1,
            'SESSION_DRIVER' => 'database'
        ];
        env_update($update_env);

        return $this->info("Env set to production & installation status set to installed. Enjoy your script. :)");
    }
}
