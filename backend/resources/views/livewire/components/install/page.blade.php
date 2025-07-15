<?php
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;
    use App\Models\User;
    use function Livewire\Volt\{state, mount, placeholder};
    state([
        'customError' => null,
    ]);

    state([
      'database_host' => fn() => config('database.connections.mysql.host'),
      'database_port' => fn() => config('database.connections.mysql.port'),
      'database_name' => fn() => config('database.connections.mysql.database'),
      'database_username' => fn() => config('database.connections.mysql.username'),
      'database_password' => fn() => config('database.connections.mysql.password'),
    ]);

    state([
      'database_connected' => false
    ]);

    state([
      'name' => '',
      'email' => '',
      'password' => '',
    ]);


    $testDatabase = function(){
      $this->database_connected = false;

      try {
        $customConnectionParams = [
            'driver'    => 'mysql',
            'host'      => $this->database_host,
            'database'  => $this->database_name,
            'username'  => $this->database_username,
            'password'  => $this->database_password,
            'port'      => $this->database_port,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        // Create and test the connection
        $connection = DB::connection()->setPdo(new \PDO(
            "{$customConnectionParams['driver']}:host={$customConnectionParams['host']};dbname={$customConnectionParams['database']}",
            $customConnectionParams['username'],
            $customConnectionParams['password']
        ));

        // Attempt to execute a simple query to check the connection
        $connection->getPdo();

        $this->database_connected = true;
        return true;
      } catch (\Exception $e) {
          return false;
      }

      return false;
    };

    $migrateDatabase = function(){
      // migrate database
      config(['database.connections.mysql.host' => $this->database_host]);
      config(['database.connections.mysql.port' => $this->database_port]);
      config(['database.connections.mysql.database' => $this->database_name]);
      config(['database.connections.mysql.username' => $this->database_username]);
      config(['database.connections.mysql.password' => $this->database_password]);
      $customDbConfig = [
          'driver'    => 'mysql',
          'host'      => $this->database_host,
          'database'  => $this->database_name,
          'username'  => $this->database_username,
          'password'  => $this->database_password,
          'charset'   => 'utf8mb4',
          'collation' => 'utf8mb4_unicode_ci',
          'prefix'    => '',
          'strict'    => true,
          'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',
      ];

      // Dynamically set the custom database configuration
      Config::set('database.connections.yena_mysql', $customDbConfig);

      try {
          Artisan::call('migrate', [
              '--database' => 'yena_mysql',
              "--force" => true
          ]);
          // $response = Http::get(route('run-update'), [
          //   'production' => true
          // ]);
          
          // Optionally, capture and log the output
          $output = Artisan::output();
          Log::info($output);
          Artisan::call('db:seed', ['--force' => true]);
          
          return true;
      }catch(\Exception $e) {
        $this->customError = $e->getMessage();
        return false;
      }
    };

    $install = function(){
      $this->customError = false;
      $this->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',

        //
        
        'database_host' => 'required',
        'database_port' => 'required',
        'database_name' => 'required',
        'database_username' => 'required',
        'database_password' => 'required'
      ]);


      // Env Database
      $database = [
        'DB_HOST'     => $this->database_host,
        'DB_PORT'     => $this->database_port,
        'DB_DATABASE' => $this->database_name,
        'DB_USERNAME' => $this->database_username,
        'DB_PASSWORD' => $this->database_password
      ];

      env_update($database);

      // migrate database
      $migration = $this->migrateDatabase();

      if(!$migration) return;


      Config::set('database.default', 'yena_mysql');
      // Create user
      $array = [
          'name' => $this->name,
          'email' => $this->email,
          'password' => Hash::make($this->password),
          'role' => 1,
      ];
      $create = User::create($array);
      Auth::login($create);

      // Update env
      $update_env = [
          'INSTALLED' => 1,
          'APP_DEBUG' => false,
          'APP_ENV' => 'production',
          'SESSION_DRIVER' => 'database',
          'APP_URL' => url('/')
      ];
      env_update($update_env);

      // Create default landing page
      $r = (new \App\Yena\Site\DefaultLanding)->build();

      $this->js('$dispatch("finishPage")');
    };
?>

<div class="zh-screen">

  <div x-data="__install">
    <div class="h-full !max-w-full p-12 md:p-12 lg:p-24">
  
        <div class="flex flex-col gap-[var(--yena-space-6)] flex-1 h-full">
            <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">


            <div wire:ignore>
              <div x-show="__page=='-'" x-cloak>  
                <livewire:components.install.requirements zzlazy :key="uukey('app-install', 'requirements')"/>
              </div>
              <div x-show="__page=='database'" x-cloak>
                <x-livewire::components.install.database />
              </div>
              <div x-show="__page=='admin'" x-cloak>  
                <x-livewire::components.install.admin />
              </div>
              <div x-show="__page=='finish'" x-cloak>  
                <x-livewire::components.install.finish />
              </div>
            </div>
  
            <div>

                @php
                    $error = false;
            
                    if(!$errors->isEmpty()){
                        $error = $errors->first();
                    }
                    
                    if($customError) $error = $customError;
                    if(Session::get('error')) $error = Session::get('error');
                @endphp
                
                @if ($error)
                    <div class="mb-5 mt-2 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                        <div class="flex items-center">
                            <div>
                                <i class="fi fi-rr-cross-circle flex"></i>
                            </div>
                            <div class="flex-grow ml-1">{{ $error }}</div>
                        </div>
                    </div>
                @endif
            </div>
  
            <div class="flex-1 place-self-stretch"></div>
  
            <div class="flex items-center justify-center">
                <div>
                    <img src="{{ logo_icon() }}" class="h-10 w-10 object-contain" alt=" " width="36" class="block">
                </div>
            </div>
            <div class="text-[11px] text-center color-gray mt-5">
              Mewayz, a product of <a href="mailto:contact@barbercita.com" class="text-link">Barbercita.</a>
            </div>
        </div>
    </div>
  </div>

  
  @script
  <script>
      Alpine.data('__install', () => {
         return {
          __page: '-',
          database_connected: @entangle('database_connected').live,
          showPassword: false,

          name: @entangle('name'),
          email: @entangle('email'),
          password: @entangle('password'),
          shown(){
              if(this.password !== '' && this.password !== null && this.showPassword) return true;
              return false;
          },
          init(){
            let $this = this;
            window.addEventListener('finishPage', function(e){
              axios.get('/run-update', {
                  params: {
                    production: true
                  }
              });
              $this.__page='finish';
            });
          }
         }
      });
  </script>
  @endscript
</div>