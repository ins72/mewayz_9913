<div>
    <div class="flex flex-col gap-6 flex-1">
    
        <div class="flex-1 place-self-stretch"></div>
        <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">
            Database information
        </h2>
        

        <div>
            <div class="grid grid-cols-2 gap-4">
                
                <div class="form-input mb-5">
                    <label>Database Host</label>
                    <input type="text" name="database_host" wire:model="database_host" value="{{ env('DB_HOST') }}">
                </div>
                <div class="form-input mb-5">
                    <label>Database Port</label>
                    <input type="text" name="database_port" wire:model="database_port" value="{{ env('DB_PORT') }}">
                </div>
            </div>
            <div class="form-input mb-5">
                <label>Database Name</label>
                <input type="text" name="database_name" wire:model="database_name" value="{{ env('DB_DATABASE') }}">
            </div>
            <div class="form-input mb-5">
                <label>Database Username</label>
                <input type="text" name="database_username" wire:model="database_username" value="{{ env('DB_USERNAME') }}">
            </div>
            <div class="form-input mb-0">
                <label>Database Password</label>
                <input type="text" name="database_password" wire:model="database_password" value="{{ env('DB_PASSWORD') }}">
            </div>
        </div>
        <div class="flex justify-between">
            <a class="flex items-center justify-center h-5 px-3 text-white bg-red-400 rounded-lg text-sm cursor-pointer" wire:click="testDatabase">
                <div wire:loading wire:target="testDatabase">
                    <div class="loader-animation-container flex">
                        <div class="inner-circles-loader !h-3 !w-3"></div>
                    </div>
                </div>
    
                <span wire:loading.class="hidden" wire:target="testDatabase">{{ __('Test Connection') }}</span>
            </a>

            <template x-if="database_connected">
                <span class="flex items-center justify-center h-5 px-3 text-white bg-green-400 rounded-lg text-xs">
                    {{ __('Database Connected') }}
                </span>
            </template>
        </div>

        <template x-if="database_connected">
            <a class="yena-button-stack --black cursor-pointer" @click="__page='admin'">{{ __('Proceed') }}</a>
        </template>

    </div>
</div>