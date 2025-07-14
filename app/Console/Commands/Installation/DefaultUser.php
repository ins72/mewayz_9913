<?php

namespace App\Console\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;

class DefaultUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandy:create_default_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default "admin" user.';

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


        $name = $this->validate_cmd(function() {
            return $this->ask('Name');
        }, ['string','required']);


        $username = $this->validate_cmd(function() {
            return $this->ask('Username');
        }, ['string', 'required', 'max:20']);

        $email = $this->validate_cmd(function() {
            return $this->ask('Email');
        }, ['email', 'required']);

        $password = $this->validate_cmd(function() {
            return $this->ask('Password');
        }, ['required', 'min:8']);

        // Array of user to be created
        $username = slugify($username);

        $array = [
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'role' => 1,
            'password' => Hash::make($password)
        ];
        $create = User::create($array);
        // Create blocks
        try {
            // Create blocks
            \Blocks::preset_blocks($create->id);
        } catch (\Exception $e) {
            
        }

        return $this->info("User has been created with email: $email, password: $password.");
    }

    /**
     * Validate an input.
     *
     * @param  mixed   $method
     * @param  array   $rules
     * @return string
     */
    public function validate_cmd($method, $rules)
    {
        $value = $method();
        $validate = $this->validateInput($rules, $value);

        if ($validate !== true) {
            $this->warn($validate);
            $value = $this->validate_cmd($method, $rules);
        }
        return $value;
    }

    public function validateInput($rules, $value)
    {

        $validator = \Validator::make([$rules[0] => $value], [ $rules[0] => $rules[1] ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            return $error->first($rules[0]);
        }else{
            return true;
        }

    }
}
