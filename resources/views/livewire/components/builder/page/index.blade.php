
<?php

    use function Livewire\Volt\{state, mount};

    state(['site'])->reactive();

?>

<div>

    <div x-init="sidebarClass = ''"></div>
    
    <div class="application_sidebar-content">
       <div class="yena-sidebar-inner p-0">
          <div class="-header-sidebar">
             <div class="sidebar-workspace">
                <div class="--padded">
                   <div class="flex-grow flex-shrink basis-auto pointer-events-none min-w-0">
                      <div class="w-full">
                         <div class="flex items-center mr-4">
                            <div class="--avatar">
                               <div class="--icon">J</div>
                            </div>
 
                            <div class="--text">
                               <p>Jeffrey's workspace</p>
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          
          <div class="flex flex-col items-center mt-10 mb-2">
            <a class="sidebar-item">
               <div class="--inner">
                  {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
                  <p>{{ __('Blocks') }}</p>
               </div>
            </a>
 
            <div class="w-full pt-5 pb-3 border-[var(--yena-colors-gray-200)]">
               <hr class="w-full opacity-[0.6] border-b border-solid">
            </div>

             <a class="sidebar-item">
                <div class="--inner">
                   {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
                   <p>{{ __('Pages') }}</p>
                </div>
             </a>
             <a class="sidebar-item">
                <div class="--inner">
                   {!! __icon('Content Edit', 'open-book') !!}
                   <p>{{ __('Design') }}</p>
                </div>
             </a>
             <a class="sidebar-item">
                <div class="--inner">
                   {!! __icon('Design Tools', 'Bucket, Paint') !!}
                   <p>{{ __('Code') }}</p>
                </div>
             </a>
             <a class="sidebar-item">
                <div class="--inner">
                   {!! __icon('Construction, Tools', 'project-book-house') !!}
                   <p>{{ __('Domain') }}</p>
                </div>
             </a>
             <a class="sidebar-item" @click="sidebarNavigate('', '{{ $site->toRoute('console-builder-settings') }}')">
                <div class="--inner">
                   {!! __icon('interface-essential', 'trash-bin-delete') !!}
                   <p>{{ __('Settings') }}</p>
                </div>
             </a>
 
             <div class="w-full pt-5 pb-3 border-[var(--yena-colors-gray-200)]">
                <hr class="w-full opacity-[0.6] border-b border-solid">
             </div>
 
             <div class="w-full mt-1">
                <div class="flex items-center justify-between">
                   <p class="text-[color:var(--yena-colors-gray-500)] text-sm text-left mb-3 mt-4">{{ __('Pages') }}</p>
 
                   <a href="" class="dot-button">
                      <i class="fi fi-rr-plus text-[10px]"></i>
                   </a>
                </div>
                <a class="sidebar-item">
                   <div class="--inner">
                      {!! __icon('Folders', 'folder-bookmark') !!}
                      <p>{{ __('Home') }}</p>
                   </div>
                </a>
             </div>
          </div>
 
          <div class="flex-1"></div>
 
          <div class="upgrade-plan !hidden">
             <div class="--header">
                <span class="--plan-card">{{ __('Pro') }}</span>
                <span class="--plan-text">{{ __('Upgrade to Yena Pro') }}</span>
             </div>
 
             <div class="--text">{{ __('Unlock unlimited AI and remove our branding') }}</div>
 
             <div class="--button">
                <a href="">{{ __('View Plans') }}</a>
             </div>
          </div>
       </div>
    </div>
</div>
