<?php
   use function Laravel\Folio\name;
   name('console-community-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Booking') }}</x-slot>

   <style>
     .media-section{
       top: 0 !important;
       position: relative !important;
     }
 
     .yena-container, .yena-root-main{
       padding: 0 !important;
     }
 
     .yena-container{
       max-width: 100% !important;
     }
     .yena-sidebar{
        display: none !important;
     }
   </style>
    <div class="has-bottom-banner is-signed-in has-community-switcher is-standard-layout-v2 bg-[#f7f9fa] is-standard-layout-v2--v3-enabled">
        <div class="flex">
            <div data-testid="community-switcher-presentation" class="w-18 community__switcher min-h-screen !bg-c-switcher border-[#e4e7eb] border-r" id="community-switcher">
                <div data-testid="community-switcher-presentation-body" class="flex flex-col items-center gap-4 min-h-screen p-4">
                    <div class="flex flex-col items-center gap-2">
                        <a data-testid="community-switcher-link" class="switcher__icon hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover !m-0 flex items-center justify-center rounded-xl border-2 border-transparent transition-colors duration-150" href="https://jeffs-community-f7be8d.circle.so/?automatic_login=true&amp;token=Pf2Tn7depWHgNn3D%2FzzLSDAK6m6ecQOS2BsktvrdW0d%2F%2FUje7jTUA8jyY53wBMP13ErxIWsGaMcaMRE6idymanbWh%2F1LvZJeT9YPLqVIGiYerAEZeEjhLta1x%2FWxagWPron7lOyaHcaDDpG8Kzq7%2Bk%2BUA47%2F%2FzOvpSc1hXdhG%2B9Hq4CtEftpPN40a%2F0pmZejIIHGog%2FqMh4kojDWw2UfNG%2BhxqyxaUrENy7xMrljQ%2F%2FaKe9eQp23wU6GEzUY8EAnYwZw6x6H0%2FLYGgGkMQ%3D%3D--9BUpMNaYJmb4KNSz--KW8cFGLLNWSEPUqYTUrvQw%3D%3D" aria-label="Jeff's Community">
                            <div data-testid="community-switcher-item" class="">
                            <span data-testid="">
                                <div>
                                    <div class="brand-icon brand-icon__initial !h-8 !w-8 !rounded-lg !outline-none" style="color: rgb(255, 255, 255); background-color: rgb(43, 46, 51);">J</div>
                                </div>
                            </span>
                            </div>
                        </a>
                        <a data-testid="community-switcher-link" class="switcher__icon active hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover !m-0 flex items-center justify-center rounded-xl border-2 border-transparent transition-colors duration-150 border-secondary" href="https://jeffs-community-be415d.circle.so/?automatic_login=true&amp;token=Pf2Tn7depWHgNn3D%2FzzLSDAK6m6ecQOS2BsktvrdW0d%2F%2FUje7jTUA8jyY53wBMP13ErxIWsGaMcaMRE6idymanbWh%2F1LvZJeT9YPLqVIGiYerAEZeEjhLta1x%2FWxagWPron7lOyaHcaDDpG8Kzq7%2Bk%2BUA47%2F%2FzOvpSc1hXdhG%2B9Hq4CtEftpPN40a%2F0pmZejIIHGog%2FqMh4kojDWw2UfNG%2BhxqyxaUrENy7xMrljQ%2F%2FaKe9eQp23wU6GEzUY8EAnYwZw6x6H0%2FLYGgGkMQ%3D%3D--9BUpMNaYJmb4KNSz--KW8cFGLLNWSEPUqYTUrvQw%3D%3D" aria-label="Jeff's Community">
                            <div data-testid="community-switcher-item" class="">
                            <span data-testid="">
                                <div>
                                    <div class="brand-icon brand-icon__initial !h-8 !w-8 !rounded-lg !outline-none" style="color: rgb(255, 255, 255); background-color: rgb(43, 46, 51);">J</div>
                                </div>
                            </span>
                            </div>
                        </a>
                    </div>
                    <span data-testid="">
                        <a class="hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover text-c-sidebar hover:text-c-sidebar focus:text-c-sidebar flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg transition-colors duration-150" href="/communities/new" aria-label="Create a new community">
                            <svg class="icon icon-20-plus-v3" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                            <use xlink:href="#icon-20-plus-v3" class=""></use>
                            </svg>
                        </a>
                    </span>
                </div>
            </div>
            <div class="w-full">

                <nav data-testid="navigation-bar-wrapper" aria-label="Main navigation bar" class="border-[#e4e7eb] bg-c-header grid-cols-header sticky top-0 z-50 grid min-h-[64px] border-b max-h-16 px-9 py-2" id="root-header-v2_1">
                    <div class="absolute left-5">
                       <div class="top-0 z-30 flex h-16 min-h-[4rem] items-center justify-between gap-2 sticky" data-testid="top_section_container">
                          <div class="h-full w-full" data-testid="dropdown" data-headlessui-state="">
                             <div class="flex w-full h-full px-4 py-3" data-testid="dropdown-button-wrapper">
                                <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 w-full focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 rounded-md group" data-testid="community-menu" type="button" id="headlessui-menu-button-6" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                   <div class="flex w-full items-center justify-between">
                                      <div class="w-full min-w-0">
                                         <div class="flex items-center gap-2 text-c-header">
                                            <div class="w-full min-w-0">
                                               <h1 data-testid="title-name" class="truncate text-left text-xl font-semibold normal-case leading-7 tracking-normal text-c-header">Jeff's Community</h1>
                                            </div>
                                         </div>
                                      </div>
                                      <div class="group-hover:bg-c-header-active text-c-header group-hover:text-c-header-active ml-3 flex h-5 w-5 flex-col items-center justify-center rounded-full transition-colors duration-150">
                                         <svg class="icon icon-12-chevron-down !transition-none" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12">
                                            <use xlink:href="#icon-12-chevron-down" class=""></use>
                                         </svg>
                                      </div>
                                   </div>
                                </button>
                                <div class="z-10 w-full mt-1 lg:-mt-2 !w-[16.25rem] !left-[1.65rem] lg:!-left-2.5" style="position: absolute; inset: 0px auto auto 0px; transform: translate3d(0px, 64px, 0px);" data-popper-reference-hidden="false" data-popper-escaped="false" data-popper-placement="bottom-start"></div>
                             </div>
                          </div>
                       </div>
                    </div>
                    <ul class="absolute inset-1/2 top-0 flex h-full w-full -translate-x-1/2 list-none items-center justify-center gap-2" data-testid="header-navigation-bar">
                       <li class="flex"><a class="text-xs-plus h-8.5 flex items-center px-4 py-2 font-medium transition-colors duration-150 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 hover:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active focus:bg-c-header-hover !rounded-full !bg-c-header-active hover:!bg-c-header-active !text-c-header-active hover:!text-c-header-active focus:!text-c-header-active" tabindex="0" title="Home" href="/feed">Home</a></li>
                       <li class="flex"><a class="text-xs-plus h-8.5 flex items-center px-4 py-2 font-medium transition-colors duration-150 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 hover:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active focus:bg-c-header-hover !rounded-full" tabindex="0" title="Courses" href="/courses">Courses</a></li>
                       <li class="flex"><a class="text-xs-plus h-8.5 flex items-center px-4 py-2 font-medium transition-colors duration-150 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 hover:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active focus:bg-c-header-hover !rounded-full" tabindex="0" title="Events" href="/events">Events</a></li>
                       <li class="flex"><a class="text-xs-plus h-8.5 flex items-center px-4 py-2 font-medium transition-colors duration-150 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 hover:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active focus:bg-c-header-hover !rounded-full" tabindex="0" title="Members" href="/members">Members</a></li>
                       <li class="flex"><a class="text-xs-plus h-8.5 flex items-center px-4 py-2 font-medium transition-colors duration-150 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 hover:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active focus:bg-c-header-hover !rounded-full" tabindex="0" title="Leaderboard" href="/leaderboard">Leaderboard</a></li>
                    </ul>
                    <div class="flex flex-row items-center space-x-px z-30 gap-4 space-x-0 justify-self-end" data-testid="right-action-block">
                       <div class="group relative justify-self-center">
                          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                             <svg class="icon icon-20-flux-search text-c-header group-hover:text-c-header-active group-focus-within:text-c-header-active transition-colors duration-150" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                <use xlink:href="#icon-20-flux-search" class=""></use>
                             </svg>
                          </div>
                          <button type="button" class="border-[#e4e7eb] hover:bg-c-header-hover focus-visible:bg-c-header-hover text-c-header hover:text-c-header-active focus:text-c-header-active hover:border-[#e4e7eb] focus-visible:!outline-secondary text-xs-plus flex h-9 items-center rounded-full border py-2 pl-10 font-medium transition-colors duration-150 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 w-32">Search</button>
                       </div>
                       <div class="flex items-center">
                          <div data-headlessui-state="">
                             <div class="w-full" id="headlessui-popover-button-7" aria-expanded="false" data-headlessui-state="">
                                <div class="relative">
                                   <span data-testid="">
                                      <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-9 w-9 hover:!bg-c-header-hover focus:!bg-c-header-hover !rounded-md p-1.5 duration-150 !text-c-header hover:!text-c-header-active focus:!text-c-header-active" aria-label="Notifications" data-testid="notifications-menu-popover-button">
                                         <svg class="icon icon-20-bell-v3 text-default !text-inherit !transition-none h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                            <use xlink:href="#icon-20-bell-v3" class="!fill-current"></use>
                                         </svg>
                                      </button>
                                   </span>
                                </div>
                             </div>
                          </div>
                          <div data-headlessui-state="">
                             <div class="w-full" id="headlessui-popover-button-12" aria-expanded="false" data-headlessui-state="">
                                <div class="relative">
                                   <span data-testid="">
                                      <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-9 w-9 hover:!bg-c-header-hover focus:!bg-c-header-hover !rounded-md p-1.5 duration-150 !text-c-header hover:!text-c-header-active focus:!text-c-header-active" aria-label="Direct messages" data-testid="direct-messages-popover-button">
                                         <svg class="icon icon-20-message-v3 text-default !text-inherit !transition-none h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                            <use xlink:href="#icon-20-message-v3" class="!fill-current"></use>
                                         </svg>
                                      </button>
                                   </span>
                                </div>
                             </div>
                          </div>
                          <div data-headlessui-state="">
                             <div class="w-full" id="headlessui-popover-button-17" aria-expanded="false" data-headlessui-state="">
                                <div class="relative">
                                   <span data-testid="">
                                      <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-9 w-9 hover:!bg-c-header-hover focus:!bg-c-header-hover !rounded-md p-1.5 duration-150 !text-c-header hover:!text-c-header-active focus:!text-c-header-active" aria-label="Bookmarks" data-testid="bookmarks-popover-button">
                                         <svg class="icon icon-20-flux-bookmark text-default !text-inherit !transition-none h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                            <use xlink:href="#icon-20-flux-bookmark" class="!fill-current"></use>
                                         </svg>
                                      </button>
                                   </span>
                                </div>
                             </div>
                          </div>
                       </div>
                       <div class="" data-testid="dropdown" data-headlessui-state="">
                          <div class="flex items-center" data-testid="user-profile">
                             <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 !rounded-full" data-testid="dropdown-button" type="button" aria-label="User menu options" id="headlessui-menu-button-22" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                <div class="relative inline-flex h-fit w-fit shrink-0 grow-0 overflow-hidden rounded-full" data-testid="user-image-container">
                                   <div aria-hidden="true" class="flex select-none items-center justify-center font-medium text-white w-8 h-8 text-sm" data-testid="user-image-initials" style="background-color: rgb(22, 106, 99);">JJ</div>
                                </div>
                             </button>
                             <div class="z-10 z-30" style="position: fixed; left: 0px; top: 0px;"></div>
                          </div>
                       </div>
                    </div>
                 </nav>

                 <div class="bg-[#f7f9fa]">
                    <!-- Mobile menu -->
                    
                    <div class="bg-[#f7f9fa] flex w-full flex-col lg:h-[calc(100vh-64px)] h-screen">         
                        {{-- <div id="trial-banner" class="bg-banner text-badge z-[3] hidden h-12 w-full items-center justify-center p-3 text-center font-semibold leading-5 lg:flex left-[60px]">
                            <div>
                                <div>Your trial ends in 14 days on 10/01.<a class="hover:!text-badge text-badge ml-1 cursor-pointer underline" href="/settings/plans">Upgrade now</a> and take advantage of our annual discounts with up to 20% off!</div>
                            </div>
                        </div> --}}

                        <div class="community__content trix-v2 bg-[#f7f9fa] h-[calc(100vh-48px)] w-full flex-1 overflow-y-auto print:overflow-visible slideout-panel slideout-panel-left">
                            <div class="standard-layout-v2 bg-[#f7f9fa] standard-layout-v2--has-sidebar standard-layout-v2--has-sidebar-v3 standard-layout-v2--no-right-sidebar">
                                
                                <div class="standard-layout-v2__sidebar">
                                    <nav>
                                        <div class="bg-c-sidebar fixed flex h-full max-h-full flex-col w-sidebar-v3 overflow-y-auto">
                                            <div class="h-full w-full">
                                                <div class="h-full w-full flex flex-col">
                                                    <div class="w-full">
                                                        <ul class="bg-c-sidebar mt-4 flex list-none flex-col gap-y-1 px-3 lg:mt-6 lg:px-6">
                                                        <li class="relative">
                                                            <a aria-current="page" class="h-8.5 flex w-full items-center gap-2 !rounded-lg px-4 py-1.5 transition-colors duration-200 bg-brand rounded-md text-brand-button focus:text-brand-button hover:text-brand-button cursor-default active" title="Getting Started" aria-disabled="false" href="/getting-started">
                                                                <svg class="icon icon-20-todo-list-v3" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                    <use xlink:href="#icon-20-todo-list-v3" class=""></use>
                                                                </svg>
                                                                <h4 class="text-xs-plus max-w-full truncate font-medium text-current">Getting Started</h4>
                                                            </a>
                                                            <button class="text-brand-button hover:bg-[#f7f9fa] focus:bg-[#f7f9fa] hover:text-dark focus:text-dark focus-visible:outline-secondary absolute right-2 top-1/2 flex h-5 w-5 -translate-y-1/2 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" type="button">
                                                                <svg class="icon icon-16-close" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-16-close" class=""></use>
                                                                </svg>
                                                            </button>
                                                        </li>
                                                        <li class="relative">
                                                            <a class="h-8.5 flex w-full items-center gap-2 !rounded-lg px-4 py-1.5 transition-colors duration-200 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md" title="Feed" aria-disabled="false" href="/feed">
                                                                <svg class="icon icon-20-feed-v3" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                    <use xlink:href="#icon-20-feed-v3" class=""></use>
                                                                </svg>
                                                                <h4 class="text-xs-plus max-w-full truncate font-medium text-current">Feed</h4>
                                                            </a>
                                                        </li>
                                                        </ul>
                                                    </div>
                                                    
                                                    <div class="bg-c-sidebar mt-4 flex flex-col px-3 gap-y-6 lg:mt-6 lg:px-6">
                                                        <div draggable="false" id="get-started" class="group relative">
                                                        <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus-visible:text-c-sidebar focus-visible:bg-c-sidebar-hover focus-visible:outline-secondary mb-1 flex w-full select-none items-center justify-between text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-grab rounded-lg px-4 py-1.5 transition-colors duration-200" data-testid="space-group" aria-expanded="true" aria-controls="get-started-list">
                                                            <div class="flex w-full space-x-px group-hover:max-w-[calc(100%-56px)]">
                                                                <h3 class="truncate text-sm font-semibold leading-5 text-current" data-id="515725" data-testid="spaces-left-sidebar" title="Get Started">Get Started</h3>
                                                                <div class="ml-1 mr-auto flex h-5 w-5 rounded py-0.5 transition-transform hidden group-hover:flex hover:bg-c-sidebar-hover rotate-90" aria-label="Collapse space">
                                                                    <svg class="icon icon-cheveron-right" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-cheveron-right" class=""></use>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </button>
                                                        <div class="text-c-sidebar absolute right-[1rem] top-[0.375rem] ml-1 flex gap-1 invisible group-focus-within:visible group-hover:visible" data-testid="space-group-actions">
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="spacegroup-options-button" type="button" aria-label="View space group options" id="headlessui-menu-button-494" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-menu-dots-horizontal" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" aria-label="Add space or space group" id="headlessui-menu-button-495" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-add-new" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-y-1" id="get-started-list">
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442685" href="/c/start-here/">
                                                                    <span aria-label="🏠" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">🏠</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Start Here</h4>
                                                                </a>
                                                            </div>
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442686" href="/c/say-hello/">
                                                                    <span aria-label="👋" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">👋</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Say Hello</h4>
                                                                </a>
                                                            </div>
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442687" href="/c/resources/">
                                                                    <span aria-label="📖" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">📖</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Resources</h4>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div draggable="false" id="product" class="group relative">
                                                        <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus-visible:text-c-sidebar focus-visible:bg-c-sidebar-hover focus-visible:outline-secondary mb-1 flex w-full select-none items-center justify-between text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-grab rounded-lg px-4 py-1.5 transition-colors duration-200" data-testid="space-group" aria-expanded="true" aria-controls="product-list">
                                                            <div class="flex w-full space-x-px group-hover:max-w-[calc(100%-56px)]">
                                                                <h3 class="truncate text-sm font-semibold leading-5 text-current" data-id="515726" data-testid="spaces-left-sidebar" title="Product">Product</h3>
                                                                <div class="ml-1 mr-auto flex h-5 w-5 rounded py-0.5 transition-transform hidden group-hover:flex hover:bg-c-sidebar-hover rotate-90" aria-label="Collapse space">
                                                                    <svg class="icon icon-cheveron-right" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-cheveron-right" class=""></use>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </button>
                                                        <div class="text-c-sidebar absolute right-[1rem] top-[0.375rem] ml-1 flex gap-1 invisible group-focus-within:visible group-hover:visible" data-testid="space-group-actions">
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="spacegroup-options-button" type="button" aria-label="View space group options" id="headlessui-menu-button-496" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-menu-dots-horizontal" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" aria-label="Add space or space group" id="headlessui-menu-button-497" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-add-new" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-y-1" id="product-list">
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442688" href="/c/give-feedback/">
                                                                    <span aria-label="💬" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">💬</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Give Feedback</h4>
                                                                </a>
                                                            </div>
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442689" href="/c/ask-for-help/">
                                                                    <span aria-label="💬" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">💬</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Ask for Help</h4>
                                                                </a>
                                                            </div>
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442690" href="/c/announcements/">
                                                                    <span aria-label="📣" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">📣</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Announcements</h4>
                                                                </a>
                                                            </div>
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442691" href="/c/knowledge-base/">
                                                                    <span aria-label="📚" aria-hidden="true" class="flex !h-5 !max-h-5 !w-5 items-center leading-none text-sm justify-center" data-testid="">📚</span>
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">Knowledge Base</h4>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div draggable="false" id="choco-dashz" class="group relative">
                                                        <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus-visible:text-c-sidebar focus-visible:bg-c-sidebar-hover focus-visible:outline-secondary mb-1 flex w-full select-none items-center justify-between text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-grab rounded-lg px-4 py-1.5 transition-colors duration-200" data-testid="space-group" aria-expanded="true" aria-controls="choco-dashz-list">
                                                            <div class="flex w-full space-x-px group-hover:max-w-[calc(100%-56px)]">
                                                                <h3 class="truncate text-sm font-semibold leading-5 text-current" data-id="515732" data-testid="spaces-left-sidebar" title="Choco Dashz">Choco Dashz</h3>
                                                                <div class="ml-1 mr-auto flex h-5 w-5 rounded py-0.5 transition-transform hidden group-hover:flex hover:bg-c-sidebar-hover rotate-90" aria-label="Collapse space">
                                                                    <svg class="icon icon-cheveron-right" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-cheveron-right" class=""></use>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </button>
                                                        <div class="text-c-sidebar absolute right-[1rem] top-[0.375rem] ml-1 flex gap-1 invisible group-focus-within:visible group-hover:visible" data-testid="space-group-actions">
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="spacegroup-options-button" type="button" aria-label="View space group options" id="headlessui-menu-button-498" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-menu-dots-horizontal" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" aria-label="Add space or space group" id="headlessui-menu-button-499" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-add-new" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-y-1" id="choco-dashz-list">
                                                            <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover my-px flex cursor-pointer items-center justify-start rounded-md px-2 py-1.5 text-sm">
                                                                <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-16-add-new" class=""></use>
                                                                </svg>
                                                                <span data-testid="add-space" class="mx-2">Create space</span>
                                                            </button>
                                                        </div>
                                                        </div>
                                                        <div draggable="false" id="spaces" class="group relative">
                                                        <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus-visible:text-c-sidebar focus-visible:bg-c-sidebar-hover focus-visible:outline-secondary mb-1 flex w-full select-none items-center justify-between text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-grab rounded-lg px-4 py-1.5 transition-colors duration-200" data-testid="space-group" aria-expanded="true" aria-controls="spaces-list">
                                                            <div class="flex w-full space-x-px group-hover:max-w-[calc(100%-56px)]">
                                                                <h3 class="truncate text-sm font-semibold leading-5 text-current" data-id="515724" data-testid="spaces-left-sidebar" title="Spaces">Spaces</h3>
                                                                <div class="ml-1 mr-auto flex h-5 w-5 rounded py-0.5 transition-transform hidden group-hover:flex hover:bg-c-sidebar-hover rotate-90" aria-label="Collapse space">
                                                                    <svg class="icon icon-cheveron-right" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-cheveron-right" class=""></use>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </button>
                                                        <div class="text-c-sidebar absolute right-[1rem] top-[0.375rem] ml-1 flex gap-1 invisible group-focus-within:visible group-hover:visible" data-testid="space-group-actions">
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="spacegroup-options-button" type="button" aria-label="View space group options" id="headlessui-menu-button-500" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-menu-dots-horizontal" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center" data-testid="dropdown" data-headlessui-state="">
                                                                <div data-testid="dropdown-button-wrapper">
                                                                    <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 focus-visible:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" aria-label="Add space or space group" id="headlessui-menu-button-501" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                    <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                        <use xlink:href="#icon-16-add-new" class="!fill-current"></use>
                                                                    </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-y-1" id="spaces-list">
                                                            <div draggable="false">
                                                                <a class="group flex items-center py-1.5 bg-transparent text-c-sidebar focus:text-c-sidebar hover:text-c-sidebar cursor-pointer hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover rounded-md h-8.5 !rounded-lg px-4 transition-colors duration-75" draggable="false" data-id="1442693" href="/c/li/">
                                                                    <img loading="lazy" src="https://jeffs-community-be415d.circle.so/assets/emoji-picker-v3/shapes/circle/shape-circle-blue-65c4a6035acf9fa79fa588f789d2f8033ed911cc1f7c90ffbd8e7525e9050611.png" alt="" aria-hidden="true" class="max-h-6 max-w-full flex !h-5 !max-h-5 !w-5 items-center leading-none">
                                                                    <h4 class="ml-2 max-w-full truncate text-current text-xs-plus font-medium" data-testid="space-name">li</h4>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- <div id="links-section" class="mb-8 px-3 mt-6 flex flex-col gap-1 lg:px-6">
                                                        <button type="button" class="text-c-sidebar hover:bg-c-sidebar-hover focus-visible:bg-c-sidebar-hover group flex w-full items-center space-x-px py-1.5 text-sm font-semibold transition-colors rounded-lg px-4 transition-colors duration-200">
                                                        <span>Links</span>
                                                        <div class="flex rounded py-0.5 transition-transform hidden group-hover:flex rotate-90" aria-label="Expand links">
                                                            <svg class="icon icon-cheveron-right" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                <use xlink:href="#icon-cheveron-right" class=""></use>
                                                            </svg>
                                                        </div>
                                                        </button>
                                                        <div class="flex flex-col gap-y-1">
                                                        <a href="https://play.google.com/store/apps/details?id=so.circle.circle&amp;utm_source=community_nav" target="_blank" rel="noreferrer" draggable="false" title="Download the Android app" class="text-c-sidebar hover:text-c-sidebar focus:text-c-sidebar active:text-c-sidebar hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover group flex w-full items-center gap-2 px-2 text-xs-plus h-8.5 !m-0 rounded-lg px-4 font-medium transition-colors duration-200">
                                                            <span class="flex !h-5 !w-5 items-center justify-center">
                                                                <svg class="icon icon-16-external-link !h-4 !w-4" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-16-external-link" class=""></use>
                                                                </svg>
                                                            </span>
                                                            <h4 class="truncate text-current text-xs-plus flex-1 font-medium">Download the Android app</h4>
                                                            <button type="button" class="flex gap-0.5 hidden group-hover:flex" aria-label="View sidebar link options">
                                                                <div class="inline-block text-left" data-testid="dropdown" data-headlessui-state="">
                                                                    <div data-testid="dropdown-button-wrapper">
                                                            <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" id="headlessui-menu-button-502" aria-haspopup="true" aria-expanded="false" data-headlessui-state=""><svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icon-16-menu-dots-horizontal" class=""></use></svg></button></div></div></button>
                                                        </a>
                                                        <a href="https://apps.apple.com/us/app/circle-communities/id1509651625?pt=121043132&amp;ct=Sidebar%20Navigation&amp;mt=8" target="_blank" rel="noreferrer" draggable="false" title="Download the iOS app" class="text-c-sidebar hover:text-c-sidebar focus:text-c-sidebar active:text-c-sidebar hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover group flex w-full items-center gap-2 px-2 text-xs-plus h-8.5 !m-0 rounded-lg px-4 font-medium transition-colors duration-200">
                                                            <span class="flex !h-5 !w-5 items-center justify-center">
                                                                <svg class="icon icon-16-external-link !h-4 !w-4" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                    <use xlink:href="#icon-16-external-link" class=""></use>
                                                                </svg>
                                                            </span>
                                                            <h4 class="truncate text-current text-xs-plus flex-1 font-medium">Download the iOS app</h4>
                                                            <button type="button" class="flex gap-0.5 hidden group-hover:flex" aria-label="View sidebar link options">
                                                                <div class="inline-block text-left" data-testid="dropdown" data-headlessui-state="">
                                                                    <div data-testid="dropdown-button-wrapper">
                                                            <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 flex py-0.5 w-5 h-5 rounded hover:bg-black/10 transition-colors" data-testid="dropdown-button" type="button" id="headlessui-menu-button-503" aria-haspopup="true" aria-expanded="false" data-headlessui-state=""><svg class="icon icon-16-menu-dots-horizontal" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icon-16-menu-dots-horizontal" class=""></use></svg></button></div></div></button>
                                                        </a>
                                                        <button type="button" class="text-c-sidebar hover:text-c-sidebar hover:bg-c-sidebar-hover focus:bg-c-sidebar-hover flex w-full items-center px-2 text-xs-plus h-8.5 rounded-lg px-4 font-medium transition-colors duration-200">
                                                            <svg class="icon icon-16-add-new" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                <use xlink:href="#icon-16-add-new" class=""></use>
                                                            </svg>
                                                            <span class="ml-2">Add link</span>
                                                        </button>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>

                                            <div class="fixed bottom-0 flex w-[inherit] flex-col items-center bg-transparent sm:max-w-[21.25rem] md:max-w-[18.5rem]"><div class="border-c-sidebar bg-c-sidebar flex w-full flex-col gap-2 border-t px-6 pb-3.5 pt-4"><div class="flex w-full items-center justify-between  bg-c-sidebar"><button type="submit" class="focus-visible:!outline-secondary font-bold transition-colors duration-200 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 disabled:cursor-not-allowed px-[18px] py-[6px] text-sm w-full rounded-full border-hover bg-white text-darkest hover:bg-white disabled:text-light border !border-dark hover:bg-[#f7f9fa] px-4 py-[8.5px] font-medium transition-colors duration-150 ease-in-out"><div class="text-selector-active flex items-center justify-center"><svg class="icon icon-16-go-live text-default mr-2" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icon-16-go-live" class=""></use></svg>Go live</div></button></div><a href="https://circle.so/?utm_source=powered-by&amp;community=jeffs-community-be415d" target="_blank" rel="noopener noreferrer" class="flex w-full items-center gap-1 px-4 py-2.5 opacity-60 bg-c-sidebar justify-center !p-0"><span class="text-xs font-medium leading-4 tracking-normal normal-case  text-c-sidebar">Powered by Circle</span></a></div></div>
                                        </div>
                                    </nav>
                                </div>

                                <div class="standard-layout-v2__content-wrapper !bg-[#f7f9fa] lg:!min-h-[calc(100vh-112px)]">
                                                                        
                                    <header id="standard-layout-header" aria-label="Page Header" class="z-20 sm:relative sm:top-0 lg:sticky top-0">
                                    <div id="standard-layout-header-child" class="bg-white w-full lg:sticky lg:top-0 lg:z-10">
                                        <div class="flex items-center px-6 border-primary border-b h-18 lg:px-9" data-testid="space-header">
                                            <div class="flex-1" data-testid="space-settings">
                                                <div class="hidden lg:block">
                                                <div class="text-dark">
                                                    <h1 class="text-xl font-semibold leading-7 tracking-normal normal-case  text-dark" data-testid="space-title-name"><span class="">Feed</span></h1>
                                                </div>
                                                </div>
                                                <div class="lg:hidden">
                                                <div class="inline-block text-left" data-testid="dropdown" data-headlessui-state="">
                                                    <div data-testid="dropdown-button-wrapper">
                                                        <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" data-testid="dropdown-button" type="button" id="headlessui-menu-button-39" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                            <div class="group flex w-full cursor-pointer items-center justify-center gap-1.5 py-1">
                                                            <div class="text-dark">
                                                                <h1 class="text-xl font-semibold leading-7 tracking-normal normal-case  text-dark" data-testid="space-title-name"><span class="">Feed</span></h1>
                                                            </div>
                                                            <svg class="icon icon-caret-down-bold text-dark" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                <use xlink:href="#icon-caret-down-bold" class=""></use>
                                                            </svg>
                                                            </div>
                                                        </button>
                                                        <div class="z-10" style="position: fixed; left: 0px; top: 0px;"></div>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="react-form hidden">
                                                <form>
                                                    <input type="hidden" name="authenticity_token" value="DKapwimTfZLi8E1+YTPucbDmB0YzIArHljB0pdPEl9kWqeV75aiFb1+eQYWPWBnrVUvLftGWX7ynktNL4UbX2Q==">
                                                    <div class="react-image-input form-input">
                                                        <div class="editor-modal"></div>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                            <div class="relative flex">
                                                <div class="hidden items-center gap-6 md:flex">
                                                <div class="inline-block text-left" data-testid="dropdown" data-headlessui-state="">
                                                    <div data-testid="dropdown-button-wrapper">
                                                        <button class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" data-testid="dropdown-button" type="button" id="headlessui-menu-button-40" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                            <div class="group flex w-full cursor-pointer items-center justify-center gap-1.5 py-1 focus-visible:bg-tertiary hover:bg-tertiary !gap-1 !rounded-lg py-2 !pl-3 !pr-1.5 transition-colors duration-200">
                                                            <span class="text-sm font-medium leading-5 tighter normal-case  text-dark">Latest</span>
                                                            <svg class="icon icon-caret-down h-5 w-5 text-dark" aria-hidden="true" viewBox="0 0 8 4">
                                                                <use xlink:href="#icon-caret-down" class="!stroke-current"></use>
                                                            </svg>
                                                            </div>
                                                        </button>
                                                        <div class="z-10" style="position: fixed; left: 0px; top: 0px;"></div>
                                                    </div>
                                                </div>
                                                <button id="sidebar--right__btn-quick-post" aria-haspopup="true" type="button" class="focus-visible:!outline-secondary font-bold transition-colors duration-200 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 disabled:cursor-not-allowed px-6 py-2 rounded-full bg-brand text-brand-button disabled:bg-disabled transition-opacity hover:opacity-90 flex h-10 items-center justify-center text-sm">New post</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </header>

                                    <main aria-label="Main Content" class="standard-layout-v2__content bg-[#f7f9fa] standard-layout-v2__content--full">
                                        <div class="bg-[#f7f9fa]">
                                           <div class="main h-full bg-[#f7f9fa]">
                                              <div class="main__wrapper h-full sm:mx-6 md:pt-6 lg:mx-9">
                                                 <div class="lg:mx-auto lg:flex lg:gap-6 lg:max-w-5xl">
                                                    <div class="w-full">
                                                       <div class="mb-6 hidden md:block">
                                                          <button type="button" class="border-primary bg-white focus-visible:!outline-secondary flex w-full cursor-pointer items-center justify-between border p-3.5 transition-shadow hover:shadow-md focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 rounded-2xl">
                                                             <div class="flex items-center gap-4">
                                                                <div class="relative inline-flex h-fit w-fit shrink-0 grow-0 overflow-hidden rounded-full" data-testid="user-image-container">
                                                                   <div aria-hidden="true" class="flex select-none items-center justify-center font-medium text-white w-8 h-8 text-sm" data-testid="user-image-initials" style="background-color: rgb(22, 106, 99);">JJ</div>
                                                                </div>
                                                                <span class="text-base font-normal leading-5 tighter normal-case  text-light">Start a post</span>
                                                             </div>
                                                             <div class="bg-tertiary flex h-8 w-8 justify-center rounded-full">
                                                                <svg class="icon icon-20-plus-v2 text-dark" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                   <use xlink:href="#icon-20-plus-v2" class=""></use>
                                                                </svg>
                                                             </div>
                                                          </button>
                                                       </div>
                                                       <div class="space__posts space__posts--posts !pb-16 lg:!pb-6" data-testid="post_section">
                                                          <div class="infinite-scroll-component__outerdiv">
                                                             <div class="infinite-scroll-component " style="height: auto; overflow: initial;">
                                                                <div class="post post--parent bg-white border-primary mb-5 md:mb-6 rounded-none border first:mt-5 first:md:mt-0 md:rounded-2xl post-name--empty">
                                                                   <div class="post__post">
                                                                      <div class="post__content text-dark pt-4 md:pt-5">
                                                                         <div class="px-5 md:px-6">
                                                                            <div class="flex items-start justify-between gap-3">
                                                                               <div class="flex grow flex-col items-start justify-between space-y-5">
                                                                                  <div class="post__user flex items-stretch mt-1" data-testid="post-meta-info-regular">
                                                                                     <div class="post__avatar">
                                                                                        <div data-state="closed">
                                                                                           <a href="/u/292aec4e" aria-label="View Jeff Jola profile" class="focus-visible:outline-secondary hover:text-dark inline-block focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 mr-3" data-testid="post-avatar">
                                                                                              <div class="relative inline-flex h-fit w-fit shrink-0 grow-0 overflow-hidden rounded-full" data-testid="user-image-container">
                                                                                                 <div aria-hidden="true" class="flex select-none items-center justify-center font-medium text-white w-10 h-10 text-base" data-testid="user-image-initials" style="background-color: rgb(22, 106, 99);">JJ</div>
                                                                                              </div>
                                                                                           </a>
                                                                                        </div>
                                                                                     </div>
                                                                                     <button type="button" class="post__bio focus-visible:outline-secondary flex flex-col justify-between self-center text-left focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                                                                                        <div class="author flex flex-wrap items-center gap-x-2 gap-y-px">
                                                                                           <div class="author__name inline-flex"><a href="/u/292aec4e" aria-label="View Jeff Jola profile" class="focus-visible:outline-secondary hover:text-dark inline-block focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 !text-dark overflow-anywhere text-sm font-semibold leading-5" data-testid="post-avatar">Jeff Jola</a></div>
                                                                                           <div class="author__time inline-flex"><a class="ago text-default hover:text-dark focus-visible:outline-secondary focus-visible:text-dark text-xs focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" href="/c/choco/5cd5b3"><span data-testid="">2h</span></a></div>
                                                                                        </div>
                                                                                        <div class="post__meta inline-flex flex-wrap items-center gap-x-2">
                                                                                     <button type="button" class="author__credentials text-default focus-visible:outline-secondary focus-visible:text-dark text-left text-xs focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"><span class="space-x-2"><span class="overflow-anywhere md:line-clamp-1">Posted in Choco</span></span></button></div></button>
                                                                                  </div>
                                                                               </div>
                                                                               <div class="post__actions-container text-dark flex items-center gap-0.5" data-testid="post-header-actions">
                                                                                  <span data-testid="">
                                                                                     <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-7 w-7 group" aria-label="Bookmarks" data-testid="bookmark-button">
                                                                                        <svg class="icon icon-20-bookmark text-default text-dark group-hover:text-darkest !w-5 !h-5 !text-default group-hover:!text-dark h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                                           <use xlink:href="#icon-20-bookmark" class="!fill-current"></use>
                                                                                        </svg>
                                                                                     </button>
                                                                                  </span>
                                                                                  <div class="z-10" data-testid="dropdown" data-headlessui-state="">
                                                                                     <div data-testid="dropdown-button-wrapper">
                                                                                        <div class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" data-testid="dropdown-button" aria-label="Post actions" id="headlessui-menu-button-37" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                                           <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-7 w-7 group" aria-label="Post actions" data-testid="action-more">
                                                                                              <svg class="icon icon-20-menu-dots-horizontal text-default !text-default group-hover:!text-dark !w-5 !h-5 h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                                                 <use xlink:href="#icon-20-menu-dots-horizontal" class="!fill-current"></use>
                                                                                              </svg>
                                                                                           </button>
                                                                                        </div>
                                                                                     </div>
                                                                                  </div>
                                                                                  <div class="react-form">
                                                                                     <form control="[object Object]" formstate="[object Object]">
                                                                                        <input type="hidden" name="authenticity_token" value="DKapwimTfZLi8E1+YTPucbDmB0YzIArHljB0pdPEl9kWqeV75aiFb1+eQYWPWBnrVUvLftGWX7ynktNL4UbX2Q==">
                                                                                        <div class="react-image-input form-input">
                                                                                           <div class="editor-modal"></div>
                                                                                        </div>
                                                                                     </form>
                                                                                  </div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                         <div class="group relative w-full cursor-pointer px-0 pt-5">
                                                                            <div class="group relative flex items-center overflow-hidden" style="height: 678px;">
                                                                               <div class="relative flex h-full w-full shrink-0 grow-0 basis-full items-center justify-center transition-transform duration-300 z-[1]" style="transform: translateX(0px);">
                                                                                  <div class="carousel-image absolute inset-0 scale-110 bg-cover bg-center blur-lg" style="background-image: url(&quot;https://app.circle.so/rails/active_storage/representations/redirect/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBCQmJCWWdNPSIsImV4cCI6bnVsbCwicHVyIjoiYmxvYl9pZCJ9fQ==--ddbcb69bd569e10d8d6920ed2af71ac5af9c5613/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaDdDRG9MWm05eWJXRjBTU0lKYW5CbFp3WTZCa1ZVT2hSeVpYTnBlbVZmZEc5ZmJHbHRhWFJiQjJrQzBBZHBBdEFIT2dwellYWmxjbnNHT2dwemRISnBjRlE9IiwiZXhwIjpudWxsLCJwdXIiOiJ2YXJpYXRpb24ifX0=--55aee7a1af0e44d97a366265c7c42865f843dbcc/scout_bubblegum_by_pjam18_di1ju6c.png&quot;);"></div>
                                                                                  <img class="carousel-image relative select-none h-full" draggable="false" loading="lazy" alt="" src="https://app.circle.so/rails/active_storage/representations/redirect/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBCQmJCWWdNPSIsImV4cCI6bnVsbCwicHVyIjoiYmxvYl9pZCJ9fQ==--ddbcb69bd569e10d8d6920ed2af71ac5af9c5613/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaDdDRG9MWm05eWJXRjBTU0lKYW5CbFp3WTZCa1ZVT2hSeVpYTnBlbVZmZEc5ZmJHbHRhWFJiQjJrQzBBZHBBdEFIT2dwellYWmxjbnNHT2dwemRISnBjRlE9IiwiZXhwIjpudWxsLCJwdXIiOiJ2YXJpYXRpb24ifX0=--55aee7a1af0e44d97a366265c7c42865f843dbcc/scout_bubblegum_by_pjam18_di1ju6c.png">
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                      </div>
                                                                   </div>
                                                                   <div class="mt-auto">
                                                                      <div class="post__actions post__actions-visible border-primary flex items-center gap-1 border-t px-3 py-2.5 md:px-4" data-testid="post-engagement-actions">
                                                                         <button type="button" aria-label="Like the  post" class="action-link post__actions--like hover:!text-dark focus-visible:outline-secondary flex items-center text-sm transition-all duration-200 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 text-default font-medium scale-100 px-1 py-0.5">
                                                                            <svg class="icon icon-24-heart-outline" aria-hidden="true" viewBox="0 0 24 24">
                                                                               <use xlink:href="#icon-24-heart-outline" class=""></use>
                                                                            </svg>
                                                                         </button>
                                                                         <button type="button" aria-label="Comment on this post" class="action-comment text-default hover:!text-dark focus-visible:outline-secondary flex items-center text-sm font-medium transition-colors duration-150 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 px-1 py-0.5">
                                                                            <span class="action__icon">
                                                                               <svg class="icon icon-24-comment" aria-hidden="true" viewBox="0 0 24 24">
                                                                                  <use xlink:href="#icon-24-comment" class=""></use>
                                                                               </svg>
                                                                            </span>
                                                                         </button>
                                                                         <div class="engagement__comments ml-auto mr-0 flex items-center gap-2" data-testid="engagement-comments"><button type="button" class="hover:!text-dark focus-visible:outline-secondary text-default text-sm font-medium transition-colors duration-150 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 p-0" data-testid="post-comment-count">0 comments</button></div>
                                                                      </div>
                                                                   </div>
                                                                </div>
                                                                <div class="post post--parent bg-white border-primary mb-5 md:mb-6 rounded-none border first:mt-5 first:md:mt-0 md:rounded-2xl">
                                                                   <div class="px-5 pt-5 md:p-0"><a class="post__cover focus-visible:outline-secondary focus-visible:text-dark block focus-visible:rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:md:rounded-b-none" href="/c/start-here/jeffrey"><img loading="lazy" src="https://app.circle.so/rails/active_storage/representations/redirect/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBCQysvWWdNPSIsImV4cCI6bnVsbCwicHVyIjoiYmxvYl9pZCJ9fQ==--1b7130c9886ad47f5eb716288f37f20a17d246a5/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaDdDRG9MWm05eWJXRjBTU0lKYW5CbFp3WTZCa1ZVT2hSeVpYTnBlbVZmZEc5ZmJHbHRhWFJiQnpCcEF0QUNPZ3B6WVhabGNuc0dPZ3B6ZEhKcGNGUT0iLCJleHAiOm51bGwsInB1ciI6InZhcmlhdGlvbiJ9fQ==--5533b93506344ba9e00796027dd66c5a290a0506/quqxj" alt="post cover image" class="w-full object-cover md:rounded-b-none rounded-2xl"></a></div>
                                                                   <div class="post__post">
                                                                      <div class="post__content text-dark px-5 pb-5 pt-4 md:px-6 md:py-5">
                                                                         <div class="mb-5">
                                                                            <div class="flex items-start justify-between gap-3">
                                                                               <div class="flex grow flex-col items-start justify-between space-y-5">
                                                                                  <div class="post__user flex items-stretch mt-1" data-testid="post-meta-info-regular">
                                                                                     <div class="post__avatar">
                                                                                        <div data-state="closed">
                                                                                           <a href="/u/292aec4e" aria-label="View Jeff Jola profile" class="focus-visible:outline-secondary hover:text-dark inline-block focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 mr-3" data-testid="post-avatar">
                                                                                              <div class="relative inline-flex h-fit w-fit shrink-0 grow-0 overflow-hidden rounded-full" data-testid="user-image-container">
                                                                                                 <div aria-hidden="true" class="flex select-none items-center justify-center font-medium text-white w-10 h-10 text-base" data-testid="user-image-initials" style="background-color: rgb(22, 106, 99);">JJ</div>
                                                                                              </div>
                                                                                           </a>
                                                                                        </div>
                                                                                     </div>
                                                                                     <button type="button" class="post__bio focus-visible:outline-secondary flex flex-col justify-between self-center text-left focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                                                                                        <div class="author flex flex-wrap items-center gap-x-2 gap-y-px no-headline">
                                                                                           <div class="author__name inline-flex"><a href="/u/292aec4e" aria-label="View Jeff Jola profile" class="focus-visible:outline-secondary hover:text-dark inline-block focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 !text-dark overflow-anywhere text-sm font-semibold leading-5" data-testid="post-avatar">Jeff Jola</a></div>
                                                                                           <div class="author__time inline-flex"><a class="ago text-default hover:text-dark focus-visible:outline-secondary focus-visible:text-dark text-xs focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" href="/c/start-here/jeffrey"><span data-testid="">2h</span></a></div>
                                                                                        </div>
                                                                                        <div class="post__meta inline-flex flex-wrap items-center gap-x-2">
                                                                                     <button type="button" class="author__credentials text-default focus-visible:outline-secondary focus-visible:text-dark text-left text-xs focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"><span class="space-x-2"><span class="overflow-anywhere md:line-clamp-1">Posted in Start Here</span></span></button></div></button>
                                                                                  </div>
                                                                                  <div class="post__header">
                                                                                     <h1 class="post__title break-words text-2xl font-bold leading-7"><a class="!text-darkest focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" href="/c/start-here/jeffrey">Jeffrey</a></h1>
                                                                                  </div>
                                                                               </div>
                                                                               <div class="post__actions-container text-dark flex items-center gap-0.5" data-testid="post-header-actions">
                                                                                  <span data-testid="">
                                                                                     <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-7 w-7 group" aria-label="Bookmarks" data-testid="bookmark-button">
                                                                                        <svg class="icon icon-20-bookmark text-default text-dark group-hover:text-darkest !w-5 !h-5 !text-default group-hover:!text-dark h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                                           <use xlink:href="#icon-20-bookmark" class="!fill-current"></use>
                                                                                        </svg>
                                                                                     </button>
                                                                                  </span>
                                                                                  <div class="z-10" data-testid="dropdown" data-headlessui-state="">
                                                                                     <div data-testid="dropdown-button-wrapper">
                                                                                        <div class="focus-visible:outline-secondary rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" data-testid="dropdown-button" aria-label="Post actions" id="headlessui-menu-button-38" aria-haspopup="true" aria-expanded="false" data-headlessui-state="">
                                                                                           <button type="button" class="flex justify-center rounded p-1 transition-colors duration-200 hover:bg-tertiary focus-visible:outline-secondary focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 h-7 w-7 group" aria-label="Post actions" data-testid="action-more">
                                                                                              <svg class="icon icon-20-menu-dots-horizontal text-default !text-default group-hover:!text-dark !w-5 !h-5 h-5 w-5" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20">
                                                                                                 <use xlink:href="#icon-20-menu-dots-horizontal" class="!fill-current"></use>
                                                                                              </svg>
                                                                                           </button>
                                                                                        </div>
                                                                                     </div>
                                                                                  </div>
                                                                                  <div class="react-form">
                                                                                     <form control="[object Object]" formstate="[object Object]">
                                                                                        <input type="hidden" name="authenticity_token" value="DKapwimTfZLi8E1+YTPucbDmB0YzIArHljB0pdPEl9kWqeV75aiFb1+eQYWPWBnrVUvLftGWX7ynktNL4UbX2Q==">
                                                                                        <div class="react-image-input form-input">
                                                                                           <div class="editor-modal"></div>
                                                                                        </div>
                                                                                     </form>
                                                                                  </div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                         <div class="post__body" data-testid="post-body">
                                                                            <div class="post__inside trix-v2 w-full" data-testid="post-body-inside">
                                                                               <div class="flex w-full flex-col gap-2">
                                                                                  <div data-testid="tip-tap-editor-content">
                                                                                     <div class="tiptap ProseMirror z-0 max-w-none whitespace-pre-wrap bg-transparent border-none ring-0 circle-block-editor text-dark" contenteditable="false" translate="no" focus="" data-readonly="true">
                                                                                        <p>Post</p>
                                                                                     </div>
                                                                                  </div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                      </div>
                                                                   </div>
                                                                   <div class="mt-auto">
                                                                      <div class="post__actions post__actions-visible border-primary flex items-center gap-1 border-t px-3 py-2.5 md:px-4" data-testid="post-engagement-actions">
                                                                         <button type="button" aria-label="Like the Jeffrey post" class="action-link post__actions--like hover:!text-dark focus-visible:outline-secondary flex items-center text-sm transition-all duration-200 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 text-default font-medium scale-100 px-1 py-0.5">
                                                                            <svg class="icon icon-24-heart-outline" aria-hidden="true" viewBox="0 0 24 24">
                                                                               <use xlink:href="#icon-24-heart-outline" class=""></use>
                                                                            </svg>
                                                                         </button>
                                                                         <button type="button" aria-label="Comment on Jeffrey" class="action-comment text-default hover:!text-dark focus-visible:outline-secondary flex items-center text-sm font-medium transition-colors duration-150 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 px-1 py-0.5">
                                                                            <span class="action__icon">
                                                                               <svg class="icon icon-24-comment" aria-hidden="true" viewBox="0 0 24 24">
                                                                                  <use xlink:href="#icon-24-comment" class=""></use>
                                                                               </svg>
                                                                            </span>
                                                                         </button>
                                                                         <div class="engagement__comments ml-auto mr-0 flex items-center gap-2" data-testid="engagement-comments"><button type="button" class="hover:!text-dark focus-visible:outline-secondary text-default text-sm font-medium transition-colors duration-150 ease-in-out focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 p-0" data-testid="post-comment-count">0 comments</button></div>
                                                                      </div>
                                                                   </div>
                                                                </div>
                                                             </div>
                                                          </div>
                                                       </div>
                                                    </div>
                                                    <aside aria-label="Right sidebar" class="sidebar sidebar--right" data-testid="sidebar-right">
                                                       <div class="sidebar__content" data-draggable="false">
                                                          <div class="sidebar__content">
                                                             <div class="homepage__sidebar">
                                                                <div class="sidebar__block sidebar__right trending-posts !bg-white !border-primary block !rounded-2xl">
                                                                   <div class="block__title">
                                                                      <h4 class="text-xl md:text-2xl font-bold leading-8 tracking-normal normal-case  text-dark">Trending Posts</h4>
                                                                   </div>
                                                                   <div class="block__content">
                                                                      <ul class="trending-posts__post-list">
                                                                         <li>
                                                                            <div class="trending-posts__post">
                                                                               <div data-state="closed">
                                                                                  <div class="trending-posts__avatar">
                                                                                     <a href="/u/292aec4e" aria-label="View user profile">
                                                                                        <div class="relative inline-flex h-fit w-fit shrink-0 grow-0 overflow-hidden rounded-full" data-testid="user-image-container">
                                                                                           <div aria-hidden="true" class="flex select-none items-center justify-center font-medium text-white w-10 h-10 text-base" data-testid="user-image-initials" style="background-color: rgb(22, 106, 99);">JJ</div>
                                                                                        </div>
                                                                                     </a>
                                                                                  </div>
                                                                               </div>
                                                                               <div class="trending-posts__content"><a class="trending-posts__post-name !text-dark" href="/c/start-here/jeffrey">Jeffrey</a><a href="/u/292aec4e" aria-label="View user profile" class="trending-posts__author-name !text-dark">Jeff Jola</a></div>
                                                                            </div>
                                                                         </li>
                                                                      </ul>
                                                                   </div>
                                                                </div>
                                                             </div>
                                                          </div>
                                                       </div>
                                                    </aside>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        <div id="rail-bar-content"></div>
                                     </main>
                                    {{-- <main aria-label="Main Content" class="standard-layout-v2__content bg-[#f7f9fa] standard-layout-v2__content--full h-full">
                                        <div class="bg-[#f7f9fa] h-full">
                                           <div class="main h-full bg-[#f7f9fa]">
                                              <div class="main__wrapper h-full sm:mx-6 md:pt-6 pt-0 lg:mx-9">
                                                 <div class="main__content mx-auto h-full max-w-5xl">
                                                    <div class="onboarding">
                                                       <div class="onboarding__img-banner-wrapper">
                                                          <div class="img-banner">
                                                             <img loading="lazy" src="/packs/static/components/CommunityOnboarding/v3background-1b4a72a1dab5cf883709.jpg" alt="Getting started" class="img-banner__img lg:!rounded-2xl">
                                                             <div class="img-banner__title">Getting started</div>
                                                          </div>
                                                       </div>
                                                       <div class="onboarding__content">
                                                          <div class="bg-white mb-6 block space-y-3 rounded-lg p-4 shadow-md lg:hidden">
                                                             <h5 class="text-xl font-semibold leading-7 tracking-normal normal-case  text-dark">Switch to desktop for a better community setup experience</h5>
                                                             <p class="text-base font-normal leading-6 tracking-tighter normal-case  text-dark">To create your community, we recommend using our desktop experience. Due to mobile constraints, administrative features may be limited on this device.</p>
                                                          </div>
                                                          <div class="onboarding__checklist-wrapper hidden md:block">
                                                             <div class="checklist-segment !rounded-2xl">
                                                                <div class="checklist-segment__header">
                                                                   <h3 class="checklist-segment__title">
                                                                      <svg class="icon icon-checklist" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24">
                                                                         <use xlink:href="#icon-checklist" class=""></use>
                                                                      </svg>
                                                                      <div>Setup checklist</div>
                                                                   </h3>
                                                                   <button type="button" class="checklist-segment__step-title checklist-segment__step-title--active">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Basics</div>
                                                                   </button>
                                                                   <button type="button" class="checklist-segment__step-title">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Set up your spaces</div>
                                                                   </button>
                                                                   <button type="button" class="checklist-segment__step-title">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Setup a paywall</div>
                                                                   </button>
                                                                   <button type="button" class="checklist-segment__step-title">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Invite your first members</div>
                                                                   </button>
                                                                   <button type="button" class="checklist-segment__step-title">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Kickstart engagement</div>
                                                                   </button>
                                                                   <button type="button" class="checklist-segment__step-title">
                                                                      <svg class="icon icon-circle-empty checklist-segment__step-title-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                         <use xlink:href="#icon-circle-empty" class=""></use>
                                                                      </svg>
                                                                      <div>Join the Circle community</div>
                                                                   </button>
                                                                </div>
                                                                <div class="checklist-segment__content">
                                                                   <div class="accordion-section">
                                                                      <div class="accordion-section--title-section">
                                                                         <div class="accordion-section--title-section--title">Get your community started</div>
                                                                         <div class="accordion-section--title-section--subtitle">Hit the ground running with the basics of your new Circle community.</div>
                                                                      </div>
                                                                      <div class="accordion">
                                                                         <div class="accordion__item accordion__item--shown">
                                                                            <button type="button" class="accordion__item-title">
                                                                               <div class="flex items-center">
                                                                                  <svg class="icon icon-circle-empty accordion__item-check-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                                     <use xlink:href="#icon-circle-empty" class=""></use>
                                                                                  </svg>
                                                                                  <span class="text-left">Complete your profile</span>
                                                                               </div>
                                                                               <svg class="icon icon-cheveron-down accordion__item-title-icon accordion__item-title-icon--rotated" aria-hidden="true" viewBox="0 0 24 24">
                                                                                  <use xlink:href="#icon-cheveron-down" class=""></use>
                                                                               </svg>
                                                                            </button>
                                                                            <div class="accordion__item-content accordion__item-content--shown">
                                                                               <div class="accordion-content">
                                                                                  <span class="accordion-content__text"><span>Tell your members about yourself by setting your headline, bio, location, and other information.</span></span>
                                                                                  <div class="accordion-content__button-container"><button type="submit" class="focus-visible:!outline-secondary font-bold transition-colors duration-200 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 disabled:cursor-not-allowed px-[18px] py-[6px] text-sm rounded-full bg-brand text-brand-button disabled:bg-disabled transition-opacity hover:opacity-90">Continue</button></div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                         <div class="accordion__item">
                                                                            <button type="button" class="accordion__item-title">
                                                                               <div class="flex items-center">
                                                                                  <svg class="icon icon-circle-empty accordion__item-check-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                                     <use xlink:href="#icon-circle-empty" class=""></use>
                                                                                  </svg>
                                                                                  <span class="text-left">Customize your branding</span>
                                                                               </div>
                                                                               <svg class="icon icon-cheveron-down accordion__item-title-icon" aria-hidden="true" viewBox="0 0 24 24">
                                                                                  <use xlink:href="#icon-cheveron-down" class=""></use>
                                                                               </svg>
                                                                            </button>
                                                                            <div class="accordion__item-content">
                                                                               <div class="accordion-content">
                                                                                  <span class="accordion-content__text"><span>It's time to make your community your own! Pick your brand color, set a logo, and finalize your domain. <a href="https://help.circle.so/c/getting-started/customize-your-branding">Learn more about customizing your branding.</a></span></span>
                                                                                  <div class="accordion-content__button-container"><button type="submit" class="focus-visible:!outline-secondary font-bold transition-colors duration-200 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 disabled:cursor-not-allowed px-[18px] py-[6px] text-sm rounded-full bg-brand text-brand-button disabled:bg-disabled transition-opacity hover:opacity-90">Continue</button></div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                         <div class="accordion__item">
                                                                            <button type="button" class="accordion__item-title">
                                                                               <div class="flex items-center">
                                                                                  <svg class="icon icon-circle-empty accordion__item-check-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16">
                                                                                     <use xlink:href="#icon-circle-empty" class=""></use>
                                                                                  </svg>
                                                                                  <span class="text-left">Confirm community access</span>
                                                                               </div>
                                                                               <svg class="icon icon-cheveron-down accordion__item-title-icon" aria-hidden="true" viewBox="0 0 24 24">
                                                                                  <use xlink:href="#icon-cheveron-down" class=""></use>
                                                                               </svg>
                                                                            </button>
                                                                            <div class="accordion__item-content">
                                                                               <div class="accordion-content">
                                                                                  <span class="accordion-content__text">
                                                                                     <div class="mb-2">There are two types of Circle communities:</div>
                                                                                     <div class="mb-4">
                                                                                        1) <span class="text-base font-medium leading-5 tighter normal-case  text-dark">Public</span>
                                                                                        <div class="mt-2">Public communities are open for anyone to see. Optionally, you can restrict signups and/or gate certain spaces to restrict access to parts of your community.</div>
                                                                                     </div>
                                                                                     <div class="mb-4">
                                                                                        2) <span class="text-base font-medium leading-5 tighter normal-case  text-dark">Private</span>
                                                                                        <div class="mt-2">Private communities are only visible to their members. Members have to receive an invite from you or follow a special invitation link to join.</div>
                                                                                     </div>
                                                                                     <div>You can review these settings in <a href="/settings">Settings → General.</a></div>
                                                                                  </span>
                                                                                  <div class="accordion-content__button-container"><button type="submit" class="focus-visible:!outline-secondary font-bold transition-colors duration-200 focus-visible:!outline focus-visible:!outline-2 focus-visible:!outline-offset-2 disabled:cursor-not-allowed px-[18px] py-[6px] text-sm rounded-full bg-brand text-brand-button disabled:bg-disabled transition-opacity hover:opacity-90">Mark complete</button></div>
                                                                               </div>
                                                                            </div>
                                                                         </div>
                                                                      </div>
                                                                   </div>
                                                                </div>
                                                             </div>
                                                          </div>
                                                          <div class="bg-white relative flex flex-col space-x-2 space-y-2 overflow-hidden rounded-lg py-6 shadow-md md:flex-row md:rounded-lg !rounded-2xl">
                                                             <button type="button">
                                                                <div class="relative w-full px-6 hover:opacity-90 md:w-80"><img loading="lazy" alt="" src="/packs/static/components/OnboardingPage/CommunityOnboardingPage/V3Banner/introducing_circle_v3-7fe6579356d93d6f296c.png" class="h-full w-full object-cover"></div>
                                                             </button>
                                                             <div class="flex flex-col justify-center px-4">
                                                                <h5 class="text-xl font-semibold leading-7 tracking-normal normal-case  text-dark">Introducing Circle 3.0</h5>
                                                                <p class="text-base font-normal leading-6 tracking-tighter normal-case  text-dark">
                                                                <div class="inline">Take a look at what’s new in <a href="https://circle.so/v3">Circle 3.0</a>. <br> <br> If you have questions, you can visit our <a href="https://help.circle.so/">knowledge base</a>, see <a href="https://circle.so/pricing">pricing options</a>, or reach out to</div>
                                                                <button type="button" class="text-link">support</button>.</p>
                                                             </div>
                                                          </div>
                                                       </div>
                                                    </div>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        <div id="rail-bar-content"></div>
                                     </main> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>

    </div>
</x-layouts.app>