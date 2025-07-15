<?php
    use function Livewire\Volt\{state, mount, placeholder};
    state([
        'requirements' => fn() => $this->requirements(),
        'writable' => fn() => $this->writable(),

        'passed' => function(){
            $passed = true;
            foreach ($this->requirements as $key => $value) {
                if (!$value['result']) {
                    $passed = false;
                }
            }
            foreach ($this->writable as $key => $value) {
                if (!$value['result']) {
                    $passed = false;
                }
            }

            return $passed;
        },
    ]);

    $fileExistsAndNotEmpty = function($path){
        $filePath = base_path($path);
        $writable = is_writable($filePath);
        $content = $writable ? trim(file_get_contents($filePath)) : '';
        return $writable && strlen($content);
    };

    $writable = function(){
        $directories = [
            '',
            'storage',
            'bootstrap',
            'bootstrap/cache',
            'storage/app',
            'storage/logs',
            'storage/framework',
            'app'
        ];

        $results = [];

        foreach ($directories as $directory) {
            $path = rtrim(base_path($directory), '/');
            $writable = is_writable($path);
            $dir = !empty($directory) ? $directory : 'root';
            $result = ['path' => $path, 'result' => $writable, 'writable' => $writable ? 'writable' : 'not writable', 'dir' => $dir, 'message' => 'This folder is writable.'];


            if ( ! $writable) {
                $result['message'] = is_dir($path) ?
                    'Make this directory writable by giving it 0755 or 0777 permissions via file manager.' :
                    'Make this directory writable by giving it 644 permissions via file manager.';
            }

            $results[] = $result;
        }

        $files = [
            '.htaccess',
            'bootstrap/app.php',
            'public/.htaccess'
        ];

        if ( ! $this->fileExistsAndNotEmpty('.env')) {
            $results[] = [
                'path' => base_path(),
                'result' => false,
                'writable'  => 'not writable',
                'message' => "Make sure <strong>.env</strong> file has been uploaded properly to the directory above and is writable.",
            ];
        }

        foreach ($files as $file) {
            $results[] = [
                'path' => base_path($file),
                'result' => $this->fileExistsAndNotEmpty($file),
                'writable' => $this->fileExistsAndNotEmpty($file) ? 'writable' : 'not writable',
                'dir' => $file,
                'message' => (!is_writable($file) ? "Make sure <strong>$file</strong> file has been uploaded properly to your server and is writable." : 'This file is writable.')
            ];
        }

        $allPass = array_filter($results, function($item) {
            return !$item['result'];
        });
        return $results;
    };

    $requirements = function(){
        
        $result = [
            'PHP Version' => [
                'result'        => version_compare(PHP_VERSION, 8.1, '>'),
                'message'  => 'You need at least PHP 8.1.',
                'current'       => PHP_VERSION
            ],
            'PDO' => [
                'result'        => defined('PDO::ATTR_DRIVER_NAME'),
                'message'  => 'PHP PDO extension is required.',
                'current'       => defined('PDO::ATTR_DRIVER_NAME') ? 'Enabled' : 'Not enabled'
            ],
            'Mbstring' => [
                'result'        => extension_loaded('mbstring'),
                'message'  => 'PHP mbstring extension is required.',
                'current'       => extension_loaded('mbstring') ? 'Enabled' : 'Not enabled'
            ],
            'Intl' => [
                'result'        => extension_loaded('intl'),
                'message'  => 'PHP intl extension is required.',
                'current'       => extension_loaded('intl') ? 'Enabled' : 'Not enabled'
            ],
            'Fileinfo' => [
                'result'        => extension_loaded('fileinfo'),
                'message'  => 'PHP fileinfo extension is required.',
                'current'       => extension_loaded('fileinfo') ? 'Enabled' : 'Not enabled'
            ],
            'OpenSSL' => [
                'result'        => extension_loaded('openssl'),
                'message'  => 'PHP openssl extension is required.',
                'current'       => extension_loaded('openssl') ? 'Enabled' : 'Not enabled'
            ],
            'GD' => [
                'result'        => extension_loaded('gd'),
                'message'  => 'GD extension is required.',
                'current'       => extension_loaded('gd') ? 'Enabled' : 'Not enabled'
            ],
            'Curl' => [
                'result'        => extension_loaded('curl'),
                'message'  => 'PHP curl extension is required.',
                'current'       => extension_loaded('curl') ? 'Enabled' : 'Not enabled'
            ],
            'Imagick' => [
                'result'        => extension_loaded('imagick'),
                'message'       => 'PHP Imagick extension is required.',
                'current'       => extension_loaded('imagick') ? 'Enabled' : 'Not enabled'
            ],
            'Zip' => [
                'result'        => class_exists('ZipArchive'),
                'message'  => 'PHP ZipArchive extension is required.',
                'current'       => class_exists('ZipArchive') ? 'Enabled' : 'Not enabled'
            ],
        ];

        $allPass = array_filter($result, function($item) {
            return !$item['result'];
        });

        return $result;
    };
?>
<div class="flex flex-col gap-6 flex-1">
    
    <div class="flex-1 place-self-stretch"></div>
    <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">
        Get started with <b>Zeph</b>.
    </h2>
    <div class="tiny-content-init">
        <ol class="!m-0">
            <li class="font-bold">Database HOST</li>
            <li class="font-bold">Database NAME</li>
            <li class="font-bold">Database USERNAME</li>
            <li class="font-bold">Database PASSWORD</li>
        </ol>
    </div>

    <p class="yena-text">
        Please note that this installation process only updates the information in the .env file found in your application's main directory. If the installation runs into problems or fails, you can manually edit the .env file to enter your database details. Once done, follow these steps:
    </p>

    <div class="tiny-content-init text-sm">
        <ul class="!m-0">
            <li class="font--bold">Click <span class="text-link" wire:click="migrateDatabase">here</span> to migrate the database.</li>
            <li class="font--bold">Click <span class="text-link" @click="__page='admin'">here</span> to create an admin account.</li>
            {{-- <li class="font--bold">Click <span class="text-link">here</span> to create default site landing page.</li> --}}
        </ul>
    </div>

    <div class="mb-5 text-sm relative z-50">
        If manually installed, update the value in your .env : <b>INSTALLED="1"</b>
    </div>
    <div class="mb-5 text-sm">
        Wanna check the server <span class="text-link" @click="$dispatch('open-modal', 'requirements-modal')">requirements?</span>
    </div>
    <div class="flex flex-col">
        @if ($passed)
        <a class="yena-button-stack --black cursor-pointer" @click="__page='database'">{{ __('Proceed') }}</a>
        @else
        <a class="yena-button-stack --black cursor-pointer" @click="$dispatch('open-modal', 'requirements-modal')">{{ __('Requirements') }}</a>
        @endif
    </div>

    
   
   <template x-teleport="body">
      <x-modal name="requirements-modal" :show="false" removeoverflow="true" maxWidth="xl" >

        <div class="w-full">
            <div class="flex flex-col">
               <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                  <i class="fi fi-rr-cross text-sm"></i>
               </a>
         
               <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Requirements') }}</header>
         
               <hr class="yena-divider">
                
               <div class="px-8 pt-4 pb-6">
                <table class="custom-table">
                    <tbody>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Message') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        @foreach ($requirements as $key => $item)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ ao($item, 'message') }}</td>
                            <td>{{ ao($item, 'current') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <header class="flex-[0_1_0%] py-4 text-3xl font-extrabold tracking-[-1px]">{{ __('Permissions') }}</header>
                <table class="custom-table">
                    <tbody>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Message') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        @foreach ($writable as $key => $item)
                        <tr>
                            <td>{{ ao($item, 'dir') }}</td>
                            <td>{{ ao($item, 'message') }}</td>
                            <td>{{ ao($item, 'writable') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               </div>
            </div>
         </div>
      </x-modal>
   </template>
</div>