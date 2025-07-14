<div>

    <div x-init="window.addEventListener(`links_item_media:${item.uuid}`, (event) => {
        item.image = event.detail.image;
    });"></div>
    <div class="flex items-center justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-5 bg-[#F7F7F7] =shadow">
       <div class="MarginRightS FlexCenterCenter">
        {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
       </div>
       <div class="flex-auto overflow-clip">
          <div class="flex items-start gap-3">
             <div class="flex flex-col gap-2 grow shrink">
                <div class="">
                   <div>
                        <input placeholder="{{ __('Title') }}" x-model="item.content.title" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px]">
                    </div>
                </div>
                <div class="">
                   <div>
                        <input placeholder="{{ __('Subtitle (optional)') }}" x-model="item.content.subtitle" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px]">
                    </div>
                </div>
                <div class="">
                   <div>
      
                     <x-builder.input>
                        <div class="link-options__main relative">
                           <input placeholder="{{ __('URL or email (required)') }}" x-model="item.content.link" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px]">
                        </div>
                     </x-builder.input>
                    </div>
                </div>
             </div>
             <div role="button" tabindex="0" class="cursor-pointer bg-gray-200 hover:opacity-70 flex items-center justify-center rounded-8 shrink-0 h-20 w-20" @click="openMedia({
                    event: 'links_item_media:' + item.uuid,
                    sectionBack:'navigatePage(\'__last_state\')'
                });">
                <template x-if="!item.image">
                    <div class="default-image p-2 !block">
                       {!! __i('--ie', 'image-picture', 'text-gray-400 w-5 h-5') !!}
                    </div>
                 </template>
                 <template x-if="item.image">
                    <img :src="$store.builder.getMedia(item.image)" class="h-full w-[100%] rounded-8 object-cover" alt="">
                 </template>
            </div>
          </div>
          
          <div x-data="{_page:'-'}" class="mt-3">

            <div x-show="_page=='animate'">
            </div>

            <div class="flex justify-between" :class="{'!hidden': _page!=='-'}">
                <div class="flex items-center">
                   <div class="p-0 m-0 flex items-center hover:opacity-70 opacity-50">
                     
                     <div x-data="{ tippy: {
                                 
                        content: () => $refs.template.innerHTML,
                        allowHTML: true,
                        appendTo: $root,
                        maxWidth: 360,
                        interactive: true,
                        trigger: 'click',
                        animation: 'scale',
                     } }">
                        <template x-ref="template">
                           <div class="grid grid-cols-1 gap-3">
                              <div class="form-input">
                                  <label class="initial">{{ __('Animation run') }}</label>
                                  <select x-model="item.settings.animation_runs" class="text-sm">
                                      <template x-for="(runs, index) in animation_runs">
                                          <option :value="runs" x-text="runs.replace('repeat-', '')"></option>
                                      </template>   
                                  </select>
                              </div>
                              <div class="grid grid-cols-4 gap-2">
                                  <template x-for="(animation, index) in animations" :key="index">
                                      <label class="sandy-big-checkbox o-checkbox">
                                          <input class="sandy-input-inner" type="radio" name="item.settings.animation" x-model="item.settings.animation" value="item">
                                          <div class="checkbox-inner h-full p-1 rounded-xl w-[100%]">
                                              <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg animate__animated animate__slow animate__infinite" :class="`animate__${item}`"></div>
                                          </div>
                                      </label>
                                  </template>
                              </div>
                          </div>
                        </template>
                        <button class=" " type="button" x-tooltip="tippy">
                           <div class="flex items-center justify-center gap-1 w-max">
                              {!! __i('Photo Edit', 'retouch-stars-edit', 'w-5 h-5') !!}
                           </div>
                        </button>
                     </div>
                   </div>
                   <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[20px]"></div>
                   <div class="p-0 m-0 flex items-center hover:opacity-70 opacity-50">
                      <button class=" " type="button" @click="_page='animate'">
                         <div class="flex items-center justify-center gap-1 w-max">
                            {!! __i('--ie', 'calendar-schedule-31.2', 'w-5 h-5') !!}
                         </div>
                      </button>
                   </div>
                </div>
                <div class="flex items-center">
                   <button class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeMedium m-0 p-0 flex items-center opacity-50 hover:opacity-70 css-1yxmbwk" tabindex="0" type="button" aria-label="link analytics button">
                      <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="LeaderboardOutlinedIcon" style="width: 16px; height: 16px; color: black; fill: black; line-height: 1; filter: invert(11%) brightness(1);">
                         <path d="M16 11V3H8v6H2v12h20V11zm-6-6h4v14h-4zm-6 6h4v8H4zm16 8h-4v-6h4z"></path>
                      </svg>
                      <span class="MuiTouchRipple-root css-w0pj6f"></span>
                   </button>
                   <button class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeMedium css-14dwzbg" tabindex="0" type="button" aria-label="link options button" style="min-height: initial;">
                      <div class="flex items-center justify-center gap-1 w-max">
                         <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="MoreHorizIcon" style="width: 20px; height: 20px; color: black; fill: black; line-height: 1; filter: invert(11%) brightness(1);">
                            <path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2m12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2m-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2"></path>
                         </svg>
                      </div>
                      <span class="MuiTouchRipple-root css-w0pj6f"></span>
                   </button>
                   <div class="flex h-6">
                      <div class="w-px flex-shrink-0 self-stretch" style="background: rgb(224, 224, 224);"></div>
                   </div>
                   <div class="ml-2" aria-label="Hide link"><span class="MuiSwitch-root MuiSwitch-sizeMedium css-1fntnc1"><span class="MuiButtonBase-root MuiSwitch-switchBase MuiSwitch-colorPrimary Mui-checked PrivateSwitchBase-root MuiSwitch-switchBase MuiSwitch-colorPrimary Mui-checked Mui-checked css-1nr2wod" aria-label="hide-link-switch"><input class="PrivateSwitchBase-input MuiSwitch-input css-1m9pwf3" type="checkbox" checked=""><span class="MuiSwitch-thumb css-19gndve"></span><span class="MuiTouchRipple-root css-w0pj6f"></span></span><span class="MuiSwitch-track css-1ju1kxc"></span></span></div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>