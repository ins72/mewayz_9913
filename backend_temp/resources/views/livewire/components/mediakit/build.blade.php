
<?php
   use function Livewire\Volt\{state, mount, on, placeholder};
   
   state(['site']);

   $checkAddress = function($address){
      $address = slugify($address, '-');
      $validator = Validator::make([
         'address' => $address
      ], [
         'address' => 'required|string|min:3|unique:pages,address,'.$this->site->id
      ]);

      if($validator->fails()){
         $this->js('$store.builder.savingState = 2');
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('address'),
         ];
      }

      $this->site->address = $address;
      $this->site->save();

      $this->js('$store.builder.savingState = 2');
      return [
         'status' => 'success',
         'response' => '',
      ];
   };
?>
<div>

    <div x-data="builder_bio_build" class="">
        
<div class="overflow-y-auto max-h-full Profile relative overflow-x-hidden" aria-label="media kit page background style container" style="width: 100%;">
   <div id="media-kit-profile-tag" style="border-radius: 2px; color: rgb(36, 36, 36); font-family: Jost, sans-serif; font-weight: 400;">
      <div class="relative h-full w-[100%]" aria-label="media kit page layout container">
         <div class="MuiContainer-root MuiContainer-maxWidthLg relative css-4q4avz">
            <div class="right-8 absolute flex pt-6">
               <div class="ml-auto self-end"><button class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeSmall z-40 css-1j7qk7u" tabindex="0" type="button" aria-label="launch share media kit dialog"><img alt="share media kit dialog icon" class="invert h-6 w-6" src="https://cdn.beacons.ai/images/ui_icons/share.svg"><span class="MuiTouchRipple-root css-w0pj6f"></span></button></div>
            </div>
         </div>
         <div class="h-[252px]"></div>
         <div class="absolute inset-0 flex flex-col">
            <div class="-z-20 w-[100%] bg-cover bg-center h-[500px]" style="background-image: url(&quot;https://cdn.beacons.ai/user_content/Yu2phPySAkPGF88Qo7cUWc6u77q1/jeffjola_mediakit_header_background.png?t=1690036921742&quot;);"></div>
            <div aria-label="media kit background color" class="relative -z-10 box-border w-[100%] flex-1" style="background: linear-gradient(rgb(212, 240, 255), rgb(248, 213, 220));">
               <div aria-label="media kit image background overlay color" class="-mt-[16rem] h-64" style="background: linear-gradient(0deg, rgb(212, 240, 255), transparent);"></div>
            </div>
         </div>
         <div class="MuiContainer-root MuiContainer-maxWidthLg !pb-16 relative css-4q4avz" style="padding: 0px 20px 100px; max-width: 1180px;">
            <div data-testid="mediakit-layout">
               <div aria-label="Media Kit Header Section - Webpage Layout" class="flex pt-16">
                  <div class="flex-1">
                     <div>
                        <div data-testid="b5435091-6d76-454f-9c97-6ad58c1a3838">
                           <div class="relative">
                              <div aria-label="Media Kit Header Block Profile Photo - Webpage Layout" class="h-[140px] w-[140px] flex items-center justify-center overflow-hidden rounded-[50%]" style="background: rgb(0, 0, 0);"><img alt="profile" src="https://cdn.beacons.ai/user_content/Yu2phPySAkPGF88Qo7cUWc6u77q1/profile_jeffjola.png" class="rounded-full" style="width: 128px; height: 128px;"></div>
                              <div class="mt-4"></div>
                              <div class="flex items-end !masthead" style="border-radius: 2px; color: rgb(36, 36, 36); font-family: Jost, sans-serif; font-weight: 400;">
                                 <div class="mb-2 mr-4 max-w-[90%] whitespace-wrap overflow-hidden font-bold leading-tight" style="-webkit-line-clamp: 2; -webkit-box-orient: vertical; display: -webkit-box;">@jeffjola</div>
                              </div>
                              <div class="gap-x-1 flex flex-wrap items-center gap-y-0.5">
                                 <div style="border-radius: 0px; color: rgb(36, 36, 36); font-family: Jost, sans-serif; font-weight: 400; font-size: 22px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 16px;">
                                    <div class="gap-2 flex items-center">
                                       <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeInherit !mr-[-1px] css-1cw4hi4" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="LocationOnIcon">
                                          <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7m0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5"></path>
                                       </svg>
                                       <span>Lagos, Nigeria</span>
                                    </div>
                                 </div>
                                 <a href="https://beacons.ai/jeffjola" class="no-underline">
                                    <div class="gap-2 flex items-center" style="border-radius: 0px; color: rgb(36, 36, 36); font-family: Jost, sans-serif; font-weight: 400; font-size: 22px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; padding-right: 16px;">
                                       <svg xmlns="http://www.w3.org/2000/svg" fill="#242424" viewBox="0 0 201 200" height="26" width="26">
                                          <path d="M77.742 98.51c21.592 0 39.095-17.47 39.095-39.017 0-21.549-17.503-39.017-39.095-39.017S38.646 37.945 38.646 59.493 56.15 98.509 77.742 98.509M148 73.63c10.848 0 19.642-8.777 19.642-19.603S158.848 34.424 148 34.424s-19.642 8.777-19.642 19.603S137.152 73.629 148 73.629M36.757 131.494c10.848 0 19.642-8.776 19.642-19.602s-8.794-19.603-19.642-19.603-19.642 8.777-19.642 19.603 8.794 19.602 19.642 19.602M143.09 165.611c22.53 0 40.795-18.228 40.795-40.714S165.62 84.184 143.09 84.184s-40.796 18.228-40.796 40.713c0 22.486 18.265 40.714 40.796 40.714M73.398 179.747c13.56 0 24.553-10.971 24.553-24.503s-10.993-24.503-24.553-24.503-24.553 10.97-24.553 24.503 10.993 24.503 24.553 24.503"></path>
                                       </svg>
                                       <span>jeffjola</span>
                                    </div>
                                 </a>
                              </div>
                              <div class="mr-4 mt-4 gap-2 flex flex-wrap">
                                 <div class="px-5 py-3 text-16 flex items-center justify-center rounded-[40px] border border-solid font-bold uppercase" style="border-color: rgba(36, 36, 36, 0.5);">personal</div>
                                 <div class="px-5 py-3 text-16 flex items-center justify-center rounded-[40px] border border-solid font-bold uppercase" style="border-color: rgba(36, 36, 36, 0.5);">beauty</div>
                                 <div class="px-5 py-3 text-16 flex items-center justify-center rounded-[40px] border border-solid font-bold uppercase" style="border-color: rgba(36, 36, 36, 0.5);">coaching</div>
                                 <div class="px-5 py-3 text-16 flex items-center justify-center rounded-[40px] border border-solid font-bold uppercase" style="border-color: rgba(36, 36, 36, 0.5);">fashion</div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="mt-12"></div>
                     <div>
                        <div class="relative" aria-label="media kit page followers counts block">
                           <div class="text-14 font-semibold uppercase text-gray-600" style="color: rgba(36, 36, 36, 0.5);">Total Followers</div>
                           <div class="masthead mt-2 font-bold" aria-label="total followers count">2.1k</div>
                           <div class="mt-4 flex max-w-[700px] flex-wrap gap-x-8 gap-y-4">
                              <a tabindex="0" role="button" aria-label="profile followers instagram - link" class="flex items-center justify-start rounded-8 text-center no-underline">
                                 <div class="p-2 relative grid place-items-center rounded-4" style="background-color: rgb(223, 77, 108);">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="white" aria-label="profile followers instagram logo" class="h-6 w-6">
                                       <path d="M65 16.667h70c26.667 0 48.333 21.666 48.333 48.333v70A48.334 48.334 0 0 1 135 183.333H65c-26.667 0-48.333-21.666-48.333-48.333V65A48.333 48.333 0 0 1 65 16.667m-1.667 16.666a30 30 0 0 0-30 30v73.334c0 16.583 13.417 30 30 30h73.334a30 30 0 0 0 30-30V63.333c0-16.583-13.417-30-30-30zm80.417 12.5a10.418 10.418 0 1 1 0 20.836 10.418 10.418 0 0 1 0-20.836M100 58.333a41.667 41.667 0 1 1 0 83.334 41.667 41.667 0 0 1 0-83.334M100 75a25 25 0 1 0 0 50 25 25 0 0 0 0-50"></path>
                                    </svg>
                                    <div class="bottom-5 left-3 absolute z-10">
                                       <div class="flex" aria-label="Media kit social stats unverified display">
                                          <button class="MuiButtonBase-root MuiButton-root MuiButton-text MuiButton-textPrimary MuiButton-sizeLarge MuiButton-textSizeLarge MuiButton-colorPrimary MuiButton-root MuiButton-text MuiButton-textPrimary MuiButton-sizeLarge MuiButton-textSizeLarge MuiButton-colorPrimary !p-0 css-hc6gxo" tabindex="0" type="button" aria-describedby="social-stats-unverified">
                                             <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium !rounded-full p-0.5 !text-red-500 css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="InfoIcon">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m1 15h-2v-6h2zm0-8h-2V7h2z"></path>
                                             </svg>
                                             <span class="MuiTouchRipple-root css-w0pj6f"></span>
                                          </button>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ml-3 text-22 font-bold" aria-label="instagram followers count" style="color: rgb(36, 36, 36);">2.1k</div>
                              </a>
                              <a href="https://vm.tiktok.com/ZMh1xLG7R/" aria-label="profile followers tiktok - link" class="cursor-pointer flex items-center justify-start rounded-8 text-center no-underline" rel="noopener noreferrer" target="_blank">
                                 <div class="p-2 relative grid place-items-center rounded-4" style="background-color: rgb(34, 34, 34);">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="white" aria-label="profile followers tiktok logo" class="h-6 w-6">
                                       <path d="M107.867 13.426v118.058c0 13.042-10.575 23.608-23.609 23.608-13.041 0-23.608-10.575-23.608-23.608 0-13.042 10.575-23.608 23.608-23.608V76.392c-30.425 0-55.091 24.667-55.091 55.092s24.666 55.092 55.091 55.092 55.092-24.667 55.092-55.092V76.392l1.658.834a66.6 66.6 0 0 0 29.817 7.041V52.776l-.942-.234c-17.95-4.483-30.541-20.617-30.541-39.116z"></path>
                                    </svg>
                                 </div>
                                 <div class="ml-3 text-22 font-bold" aria-label="tiktok followers count" style="color: rgb(36, 36, 36);">41</div>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="self-center">
                     <div>
                        <div class="relative flex h-full w-[100%]">
                           <div aria-label="Media Kit - content gate container" class="h-full w-[100%]">
                              <div class="z-10 flex items-start justify-center" aria-label="Media Kit Contact Info Block">
                                 <div class="flex w-[328px] flex-col bg-white text-black">
                                    <div class="h-1 w-[100%]" style="background: rgb(0, 0, 0);"></div>
                                    <div class="mx-6 flex flex-col">
                                       <div class="mt-6 text-22 font-semibold">Collaborate with me!</div>
                                       <div class="mt-2 text-16 font-normal">Send me any additional project details through this contact form. I'm excited to work with you!</div>
                                       <div class="mt-4">
                                          <button class="MuiButtonBase-root MuiButton-root MuiButton-contained MuiButton-containedPrimary MuiButton-sizeLarge MuiButton-containedSizeLarge MuiButton-colorPrimary MuiButton-fullWidth MuiButton-root MuiButton-contained MuiButton-containedPrimary MuiButton-sizeLarge MuiButton-containedSizeLarge MuiButton-colorPrimary MuiButton-fullWidth !rounded-full css-1xtxoq0" tabindex="0" type="button" style="background: rgb(0, 0, 0); color: rgb(255, 255, 255);">
                                             <div class="flex flex-col">Work with me</div>
                                             <span class="MuiTouchRipple-root css-w0pj6f"></span>
                                          </button>
                                       </div>
                                       <div class="mt-6"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div>
                  <div class="mt-12">
                     <div class="relative flex h-full w-[100%]">
                        <div aria-label="Media Kit - content gate container" class="h-full w-[100%]">
                           <div aria-label="Media Kit text block">
                              <div class="text-14 font-semibold uppercase text-gray-600" style="color: rgba(36, 36, 36, 0.5);">About me</div>
                              <div class="mt-4 text-22 font-normal"><span>Welcome to my media kit! I'm Jeff Jola, a personal creator. I make content on Instagram. I’d love to work with you! Check out my stats below.</span></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div>
                  <div class="mt-12">
                     <div class="relative flex h-full w-[100%]">
                        <div aria-label="Media Kit - content gate container" class="h-full w-[100%]">
                           <div class="text-14 mb-2 font-semibold uppercase text-gray-600" style="color: rgba(36, 36, 36, 0.5);">Rates card</div>
                           <div aria-label="Media Kit Rates Block Container">
                              <div>
                                 <div class="h-[1px] w-[100%]" style="background: rgb(0, 0, 0);"></div>
                                 <div class="py-6 text-22" aria-label="Media Kit Rates Block Rate Item">
                                    <div class="flex justify-between font-bold">
                                       <div>Instagram Story</div>
                                       <div class="flex items-center rounded-8 bg-clip-text px-3 py-1" aria-label="Media Kit Page Rates Block Price" style="background: rgb(0, 0, 0); color: rgb(255, 255, 255);">Ask me!</div>
                                    </div>
                                    <div class="max-w-[90%] font-normal"><span></span></div>
                                 </div>
                              </div>
                              <div>
                                 <div class="h-[1px] w-[100%]" style="background: rgb(0, 0, 0);"></div>
                                 <div class="py-6 text-22" aria-label="Media Kit Rates Block Rate Item">
                                    <div class="flex justify-between font-bold">
                                       <div>Instagram Post</div>
                                       <div class="flex items-center rounded-8 bg-clip-text px-3 py-1" aria-label="Media Kit Page Rates Block Price" style="background: rgb(0, 0, 0); color: rgb(255, 255, 255);">Ask me!</div>
                                    </div>
                                    <div class="max-w-[90%] font-normal"><span></span></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div>
                  <div class="mt-12">
                     <div class="relative flex h-full w-[100%]">
                        <div aria-label="Media Kit - content gate container" class="h-full w-[100%]">
                           <div class="MuiPaper-root MuiPaper-elevation MuiPaper-rounded MuiPaper-elevation1 MuiCard-root relative transition-all css-czbix3" style="box-shadow: none; border: 0px;">
                              <div style="border-radius: 2px; color: rgb(77, 77, 77); font-family: Roboto, sans-serif; font-weight: 500;">
                                 <div role="button" tabindex="0" class="cursor-pointer flex cursor-pointer items-center justify-center">
                                    <div class="title">HEyyy</div>
                                    <div class="absolute right-4 flex items-center transition-transform">
                                       <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ExpandMoreIcon">
                                          <path d="M16.59 8.59 12 13.17 7.41 8.59 6 10l6 6 6-6z"></path>
                                       </svg>
                                    </div>
                                 </div>
                                 <div class="MuiCollapse-root MuiCollapse-vertical MuiCollapse-hidden css-a0y2e3" style="min-height: 0px;">
                                    <div class="MuiCollapse-wrapper MuiCollapse-vertical css-hboir5">
                                       <div class="MuiCollapse-wrapperInner MuiCollapse-vertical css-8atqhb">
                                          <div class="mt-4 text-center text-16">Bfdgdfgdg</div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="MuiCollapse-root MuiCollapse-vertical MuiCollapse-hidden css-a0y2e3" style="min-height: 0px;">
                              <div class="MuiCollapse-wrapper MuiCollapse-vertical css-hboir5">
                                 <div class="MuiCollapse-wrapperInner MuiCollapse-vertical css-8atqhb">
                                    <div aria-label="Media Kit Links Block Container" class="Links mt-4 flex overflow-x-auto">
                                       <div>
                                          <div class="inline-block h-full w-[252px] shrink-0 break-words rounded-8">
                                             <a href="https://account.beacons.ai/" class="no-underline">
                                                <div class="bg-black  mb-2 h-full overflow-hidden rounded-8 hover:scale-[1.02]" style="background: rgb(0, 0, 0);">
                                                   <div role="figure" aria-label="image  carousel" class="w-[100%] bg-cover bg-center" style="background-image: linear-gradient(rgb(0, 0, 0), rgb(0, 0, 0)); aspect-ratio: 1 / 1; height: auto;"></div>
                                                   <div class="h-full w-[100%] whitespace-pre-line bg-black" style="background: rgb(0, 0, 0);">
                                                      <div class="text-md-bold cursor-pointer px-4 py-3 text-white" style="color: rgb(255, 255, 255);">
                                                         Jeff
                                                         <div class="text-sm-normal mt-2"></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div>
                  <div class="mt-12">
                     <div class="relative flex h-full w-[100%]">
                        <div aria-label="Media Kit - content gate container" class="h-full w-[100%]">
                           <div aria-label="Past Projects Render Block" class="text-14 mb-4 font-semibold uppercase text-gray-600" style="color: rgba(36, 36, 36, 0.5);">Case studies</div>
                           <div class="relative">
                              <div class="absolute inset-y-0 box-border grid place-items-center left-4">
                                 <button class="MuiButtonBase-root Mui-disabled MuiIconButton-root Mui-disabled MuiIconButton-sizeMedium z-30 bg-gray-900 text-white hover:bg-gray-600 hover:opacity-70 hidden css-1yxmbwk" tabindex="-1" type="button" disabled="">
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ChevronLeftIcon">
                                       <path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                                    </svg>
                                 </button>
                              </div>
                              <div class="overflow-x-auto flex gap-4">
                                 <div aria-label="youtube Spotlight Deliverable - jeffjola" class="relative flex shrink-0 justify-center rounded-8 border border-solid" style="height: 430px; width: 240px; border-color: rgba(36, 36, 36, 0.19);">
                                    <div class="h-full w-[100%] pointer-events-none absolute top-0 z-10 h-full overflow-hidden rounded-8"><iframe title="YouTube spotlight embed - https://www.youtube.com/embed/QUlkiLf2obE" class="h-[180px] w-[100%]" src="https://www.youtube.com/embed/QUlkiLf2obE" frameborder="0"></iframe></div>
                                    <div class="absolute bottom-0 left-0 z-20 w-[100%] rounded-b-8" style="background: linear-gradient(rgba(0, 0, 0, 0) 0%, rgb(0, 0, 0) 44.44%); height: 100%;"></div>
                                    <div class="z-30 mt-[140px] flex w-[100%] flex-col items-center justify-start p-6 text-center">
                                       <div class="flex h-full w-[100%] flex-col justify-between">
                                          <div class="flex flex-col items-center">
                                             <div aria-label="Spotlight Brand Logo - jeffjola" class="flex h-[60px] w-[60px] items-center overflow-hidden rounded-[50%] border-[2px] border-solid border-white bg-white">
                                                <div class="flex h-full w-[100%] items-center justify-center">
                                                   <div class="MuiAvatar-root MuiAvatar-circular MuiAvatar-colorDefault css-1jhcdud" aria-label="Brand Placeholder Avatar - j" style="background: linear-gradient(rgb(145, 71, 255), rgb(231, 78, 102)); height: 100%; width: 100%; font-size: 150%;">j</div>
                                                </div>
                                             </div>
                                             <div class="mt-3 text-16 font-semibold text-white">jeffjola</div>
                                             <div class="mt-1 max-h-[80px] overflow-hidden text-12 text-white/[0.5]" style="-webkit-line-clamp: 4; -webkit-box-orient: vertical; display: -webkit-box;">sdffsdf</div>
                                          </div>
                                          <button class="MuiButtonBase-root MuiButton-root MuiButton-contained MuiButton-containedPrimary MuiButton-sizeLarge MuiButton-containedSizeLarge MuiButton-colorPrimary MuiButton-root MuiButton-contained MuiButton-containedPrimary MuiButton-sizeLarge MuiButton-containedSizeLarge MuiButton-colorPrimary !h-10 w-[100%] css-1r3lw78" tabindex="0" type="button" aria-label="View Spotlight Project Info Button - jeffjola" style="background: rgb(0, 0, 0); color: rgb(255, 255, 255);">View Info<span class="MuiTouchRipple-root css-w0pj6f"></span></button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="absolute inset-y-0 box-border grid place-items-center right-4">
                                 <button class="MuiButtonBase-root Mui-disabled MuiIconButton-root Mui-disabled MuiIconButton-sizeMedium z-30 bg-gray-900 text-white hover:bg-gray-600 hover:opacity-70 hidden css-1yxmbwk" tabindex="-1" type="button" disabled="">
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-vubbuv" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ChevronRightIcon">
                                       <path d="M10 6 8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                                    </svg>
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <center class="bg-gray-200">
         <div class="glowing-wrapper glowing-wrapper-active border border-solid border-black bg-white">
            <div class="glowing-wrapper-animations">
               <div class="glowing-wrapper-glow"></div>
               <div class="glowing-wrapper-mask-wrapper">
                  <div class="glowing-wrapper-mask"></div>
               </div>
            </div>
            <div class="glowing-wrapper-borders-masker">
               <div class="glowing-wrapper-borders"></div>
            </div>
            <a href="https://beacons.ai/?referral_type=media_kit&amp;referring_user=jeffjola&amp;utm_source=bUnknown&amp;utm_medium=self_referral&amp;utm_campaign=jeffjola&amp;utm_content=mediakit_footer_logo" class="glowing-wrapper-button w-inline-block relative flex w-[100%] flex-row items-center gap-2 rounded-full p-2 py-1" aria-label="Media Kit Footer Logo" rel="noopener noreferrer" target="_blank">
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 201 200" style="width: 16px; height: 16px; color: black; fill: black; line-height: 1; filter: invert(11%) brightness(1);">
                  <path d="M77.742 98.51c21.592 0 39.095-17.47 39.095-39.017 0-21.549-17.503-39.017-39.095-39.017S38.646 37.945 38.646 59.493 56.15 98.509 77.742 98.509M148 73.63c10.848 0 19.642-8.777 19.642-19.603S158.848 34.424 148 34.424s-19.642 8.777-19.642 19.603S137.152 73.629 148 73.629M36.757 131.494c10.848 0 19.642-8.776 19.642-19.602s-8.794-19.603-19.642-19.603-19.642 8.777-19.642 19.603 8.794 19.602 19.642 19.602M143.09 165.611c22.53 0 40.795-18.228 40.795-40.714S165.62 84.184 143.09 84.184s-40.796 18.228-40.796 40.713c0 22.486 18.265 40.714 40.796 40.714M73.398 179.747c13.56 0 24.553-10.971 24.553-24.503s-10.993-24.503-24.553-24.503-24.553 10.97-24.553 24.503 10.993 24.503 24.553 24.503"></path>
               </svg>
               <div class="button-text whitespace-nowrap font-poppins text-12 font-bold tracking-[-0.03em] text-gray-900">Made with Beacons</div>
            </a>
         </div>
      </center>
      <div class="flex justify-center bg-gray-200"><a href="https://clearbit.com" class="my-6 !mt-2 text-12 font-normal text-gray-600 no-underline" rel="noopener noreferrer" target="_blank">Brand logos provided by Clearbit</a></div>
   </div>
</div>
        <div class="yena-builder-sections">
            <div class="w-[100%]">
               <div>
                  <div>
                     <div>
                        <div class="wire-section section-width-fill banner-box new box section-bg-wrapper focus">
                           <section class="section-content">
                              <div class="banner-box section-bg-wrapper transparent color section-height-fit section-width-fill align-items-center [--spacingLR:calc(var(--unit)_*_8)] [--bg-grayscale:0%] [--background-upper:center] [--background-bottom:center] [--bg-blurscale:1.1] [--bg-blur:0px] [--bg-opacity:1]">
                                 <div class="inner-content section-container align-items-center">
                                    <div>
                                       <div class="banner-layout-2 w-boxed !pt-5">
                                          <div class="banner section-component !pb-0">
                                             <div class="banner-text content-heading">
                                                <section class="subtitle-width-size [text-align:inherit] flex flex-col gap-2">
                                                     <div x-data="{show:false}" x-cloak>
                                                         <h1 class="title pre-line --text-color t-3 [text-align:inherit]" @click="show=true; $nextTick(() => { $root.querySelector('input').focus() });" x-show="!show" x-text="site.name"></h1>

                                                         <div class="flex">
                                                             <div class="input-group mt-0" x-show="show" @click.outside="show=false">
                                                                 <input type="text" class="input-small blur-body" x-model="site.name" name="name" placeholder="{{ __('Add name') }}">
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <div x-data="{show:false}" x-cloak>
                                                         <h1 class="title pre-line --text-color t-1 [text-align:inherit]" @click="show=true; $nextTick(() => { $root.querySelector('input').focus() });" x-show="!show" x-text="'@' + site.address"></h1>


                                                         <div class="flex">
                                                             <div class="input-group mt-0" x-show="show" @click.outside="show=false">
                                                                 <input type="text" class="input-small blur-body" maxlength="20" :value="site.address" @input="checkAddress($event.target.value)" placeholder="{{ __('Site Address') }}">
                                                             </div>
                                                         </div>
                                                     </div>
                                       
                                                   <template x-if="addressError">
                                                      <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                                         <div class="flex items-center">
                                                            <div>
                                                               <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                            </div>
                                                            <div class="flex-grow ml-1 text-xs" x-text="addressError"></div>
                                                         </div>
                                                      </div>
                                                   </template>
                                                </section>
                                                <section class="flex flex-col subtitle-width-size">
                                                   <div x-data="{show:false}" x-cloak>
                                                      <p class="t-2 pre-line subtitle-width-size subtitle --text-color !w-[100%]" @click="show=true; $nextTick(() => { $root.querySelector('textarea').focus() });" x-show="!show" x-text="site.bio"></p>
                                                      <div class="input-group mt-2" x-show="show" @click.outside="show=false">
                                                         <x-builder.textarea class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="site.bio" name="title" placeholder="{{ __('Add text here') }}"/>
                                                      </div>
                                                   </div>

                                                   <div class="mt-2" x-cloak>
                                                      <div class="gallery-box section-width-fill box focus wire-section section-height-fit align-items-center">
                                                         <div class="inner-content">
                                                            <div class="gallery-container w-boxed !pt-0 xpx-0 !pb-0" style="--grid-height: 60px;--grid-height-mobile: 60px;--grid-width: 60px;--grid-width-mobile: 60px;">
                                                               <div class="gallery-container__wrapper">
                                                                  <div class="gallery-container__items !flex !overflow-x-auto !pb-[var(--s-2)]">
                                                                     <a class="gallery-container__item flex-[0_0_var(--grid-width)] !h-[var(--grid-height)]" @click="$dispatch('opensection::social')">
                                                                        <div class="default-image !bg-white border-2 border-dashed border-black">
                                                                          <i class="ph ph-plus text-xl text-black"></i>
                                                                        </div>
                                                                     </a>
                                                                     <template x-if="site.socials && site.socials.length > 0">
                                                                         <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="index">
                                                                            <a class="gallery-container__item flex-[0_0_var(--grid-width)]" @click="$dispatch('opensection::social')">
                                                                             <div class="default-image">
                                                                                <i :class="socials[social.social].icon" class="text-xl"></i>
                                                                             </div>
                                                                            </a>
                                                                         </template>
                                                                     </template>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   {{-- <div style="width: 100%;">
                                                      <div class="mt-2 button-holder subtitle-width-size">
                                                         <div class="flex items-center">
                                                            <a class="btn-1 yena-site-link" @click="navigatePage('section::button')">
                                                            <button class="t-1 shape">{{ __('Edit Button') }}</button>
                                                            </a>
                                                            <a class="btn-2 yena-site-link" href="javascript:void(0)" target="_self">
                                                            <button class="t-1 shape">Button 2</button>
                                                            </a>
                                                         </div>
                                                      </div>
                                                   </div> --}}
                                                </section>
                                             </div>
                                             <div>
                                                 <div>
                               
                                                    <div class="section-item-image banner-image min-shape" @click="openMedia({event: 'siteBanner:change', sectionBack:'navigatePage(\'__last_state\')'})" :class="{
                                                     'default': !site.banner
                                                    }">
                                                       <img :src="$store.builder.getMedia(site.banner)" :class="{
                                                          '!hidden': !site.banner
                                                       }">
                                     
                                                       <template x-if="!site.banner">
                                                          <div>
                                                             {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                                          </div>
                                                       </template>
                                                    </div>
                                                 </div>
                                                <div class="-mt-10 lg:!-mt-28">
                                                   <div class="flex flex-col items-start avatar-image" :class="{
                                                      'default': !site.logo,
                                                      }" @click="openMedia({event: 'siteLogo:change', sectionBack:'navigatePage(\'__last_state\')'})">
                                                      <img :src="$store.builder.getMedia(site.logo)" class="Fit accent banner-image rounded-[100%] mb-[var(--s-2)] object-cover !h-[130px] !w-[130px]" :class="{
                                                         '!hidden': !site.logo
                                                         }">
                                                      <template x-if="!site.logo">
                                                         <div>
                                                            <div class="banner-image section-item-image !h-[130px] !w-[130px]" :class="{'default': !site.logo}">
                                                               <div>
                                                                  {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </template>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                        </section>
                     </div>
                  </div>
               </div>
            </div>
         </div>

        <div>
            
            <div class="p-5">
                <div class="flex flex-col gap-4 sortable_section_wrapper ![--accent:#eee] ![--contrast-color:#000]">
                    <template x-for="(item, index) in getSections()" :key="item.uuid" x-ref="section_template">
                       <div class="flex flex-col border-dashed rounded-xl border-2 border-color--hover m-0 hover-select px-4 py-5">

                            <div class="relative" @click="editSection(item)">
                                <div x-bit="'section-' + item.section" x-data="{section:item}"></div>
                                <div class="screen"></div>
                            </div>
                            <div class="mt-2">
                                <div class="w-[100%] h-[66px] rounded-[15px] bg-[rgb(247,_247,_247)] opacity-100 cursor-pointer" x-data="{
                                    init(){
                                    
                                    {{-- this.$watch('item.published', (value) => {
                                        let data = {
                                            uuid: item.uuid,
                                            published: value
                                        };
            
                                        this.$wire.set_section_staus(data);
                                    }); --}}
            
                                    }
                                }" @click="editSection(item)">
                                    <div x-init="item.jsConfig = sectionConfig[item.section];"></div>
                                    <div class="w-[100%] h-[66px] flex pl-[17px] pr-[15px] py-[0] items-center">
                                    <div class="handle cursor-grab">
                                        {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px] text-[color:#BDBDBD]') !!}
                                    </div>
                
                                    <div class="{{--[box-shadow:0_4px_5.84px_hsla(0,0%,50.2%,.353)]--}} shadow-xl rounded-[10px] w-[36px] h-[36px] ml-[13px] mr-[18px] my-[0] flex items-center justify-center" :style="{
                                        'background': item.jsConfig.color,
                                        'color': $store.builder.getContrastColor(item.jsConfig.color),
                                        }" x-html="item.jsConfig['ori-icon-svg']"></div>
                
                                    <span class="text-[13px] font-semibold tracking-[-0.03em]" x-text="item.content.title ? item.content.title : item.jsConfig.name"></span>
                
                                    <div class="ml-[auto] mr-[2px] my-[0] flex items-center gap-2" @click="$event.stopPropagation();">
                                        <label class="sandy-switch">
                                            <input class="sandy-switch-input" name="settings[published]" x-model="item.published" value="true" :checked="item.published" @input="$dispatch('builder::saveSection', {
                                             section: item
                                          })" type="checkbox">
                                            <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                                    </label>
                                    
                                        <div class="mr-[4px]" @click="$event.stopPropagation();" x-data="{ tippy: {
                                            content: () => $refs.template.innerHTML,
                                            allowHTML: true,
                                            appendTo: $root,
                                            maxWidth: 360,
                                            interactive: true,
                                            trigger: 'click',
                                            animation: 'scale',
                                        } }">
                                            <template x-ref="template">
                                                <div class="yena-menu-list !w-[100%]">
                                                <div class="px-3 pt-1">
                                                    <p class="yena-text font-bold text-lg">{{__('More Options')}}</p>
                                        
                                                    {{-- <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">Created September 18th, 2023</p>
                                                    <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">by Jeff Jola</p> --}}
                                                </div>
                                        
                                                <hr class="--divider">                           
                                                <a @click="editSection(item)" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'pen-edit.7', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Edit Section') }}</span>
                                                </a>
                                        
                                                {{-- <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('Cursor Select Hand', 'Cursor, Select, Hand, Click', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Move Page') }}</span>
                                                </a>
                                        
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'document-text-edit', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Rename') }}</span>
                                                </a> --}}
                                                {{-- <hr class="--divider">
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Duplicate') }}</span>
                                                </a>
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'share-arrow.1', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Copy link') }}</span>
                                                </a> --}}
                                                <hr class="--divider">
                                                <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deleteSection(item)">
                                                    <div class="--icon">
                                                        {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Delete Section') }}</span>
                                                </a>
                                            </div>
                                            </template>
                                            <button type="button" class="yena-button-o !px-0" x-tooltip="tippy">
                                                <span class="--icon !mr-0">
                                                {!! __icon('interface-essential', 'dots', 'w-5 h-5  text-[color:#BDBDBD]') !!}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </template>
                 </div>
            </div>
        </div>

        
        <template x-teleport="body">
            <div class="overlay backdrop delete-overlay" :class="{
                '!block': deleteSectionId
            }" @click="deleteSectionId=false">
                <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
                   <div class="overlay-card-body !rounded-2xl">
                      <h2>{{ __('Delete Section?') }}</h2>
                      <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to delete this section? Once deleted, you will not be able to restore it.') }}</p>
                      <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                         <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)]" type="button" @click="deleteSectionId=false">{{ __('Cancel') }}</button>
        
                         <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]" @click="__delete_section(deleteSectionId)">{{ __('Yes, Delete') }}</button>
                      </div>
                   </div>
                </div>
             </div>
        </template>
    </div>
    
   @script
    <script>
      Alpine.data('builder_bio_build', () => {
         return {
           addressError: false,
           checkAddress(address){
             address = address.toString() // Cast to string
                         .toLowerCase() // Convert the string to lowercase letters
                         .normalize('NFD') // The normalize() method returns the Unicode Normalization Form of a given string.
                         .trim() // Remove whitespace from both sides of a string
                         .replace(/\s+/g, '-') // Replace spaces with -
                         .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                         .replace(/\-\-+/g, '-');
             this.$event.target.value = address;
             let $this = this;

             $this.addressError = false;
             clearTimeout($this.autoSaveTimer);

             $this.autoSaveTimer = setTimeout(function(){
                $this.$store.builder.savingState = 0;
                 
                $this.$wire.checkAddress(address).then(r => {
                   if(r.status === 'error'){
                      $this.addressError = r.response;
                   }
                   if(r.status === 'success'){
                      $this.addressError = false;
                   }
                });

             }, $this.$store.builder.autoSaveDelay);
           },
            currentPage(){
               var page = this.pages[0];

               this.pages.forEach((e, index) => {
                  if(e.uuid == this.site.current_edit_page) page = e;
               });
               return page;
            },
            
            getSections(){
               var sections = [];

               this.sections.forEach((element, index) => {
                  if(this.currentPage().uuid == element.page_id){
                     sections.push(element);
                  }
               });
               return _.sortBy(sections, 'position');
            },
            initSort(){
                var $this = this;

                let $wrapper = this.$root.querySelector('.sortable_section_wrapper');
                let $template = this.$root.querySelector('[x-ref="section_template"]');

                let callback = function(e){
                   let $array = [];

                   e.forEach((item, index) => {
                      let $new = {
                         uuid: item.uuid,
                         position: item.position,
                      };

                      $array.push($new);
                   });

                   let event = new CustomEvent("builder::sort_sections", {
                         detail: {
                            sections: $array,
                         }
                   });

                   window.dispatchEvent(event);
                };






                if($wrapper){
                      window.Sortable.create($wrapper, {
                         ...$this.$store.builder.sortableOptions,
                         onEnd: (event) => {
                            let $array = $this.getSections();
                            let steps = Alpine.raw($array)
                            let moved_step = steps.splice(event.oldIndex, 1)[0]
                            steps.splice(event.newIndex, 0, moved_step);
                            let keys = []
                            steps.forEach((step, i) => {
                               keys.push(step.uuid);

                               $array.forEach((x, _i) => {
                                  if(x.uuid == step.uuid) x.position = i;
                               });
                            });

                            $template._x_prevKeys = keys;

                            callback($array);
                         },
                      });
                }
               },

            init(){
                let $this = this;
                $this.initSort();
    
                window.addEventListener("siteLogo:change", (event) => {
                    $this.site.logo = event.detail.image;
                });
                window.addEventListener("siteBanner:change", (event) => {
                    $this.site.banner = event.detail.image;
                });
            }
         }
      });
   </script>
   @endscript
</div>