<?php
   use function Laravel\Folio\name;

   use function Livewire\Volt\{state, mount, on};

   state(['site' => fn() => __s()]);

   name('console-builder-pages');
?>
@extends('components.layouts.builder')
@section('content')

<div>
   @volt
   <div class="website-section" x-data="builder__pages">
      <div>
         <template x-if="createPage">
            <livewire:components.builder.pages.new-page :$site lazy />
         </template>
      </div>
   
      <template x-if="!createPage">
         <div>
            <div class="design-navbar">
               <ul >
                  <li></li>
                  <li >{{ __('Pages') }}</li>
                  <li></li>
               </ul>
            </div>
            <div class="container-small">
               <div class="website-pages mt-2">
                  <ul class="mb-1 add-new-page" @click="createPage=true">
                     <li >
                        <span >
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M12 5V19" stroke="var(--background)"></path>
                              <path d="M5 12H19" stroke="var(--background)"></path>
                           </svg>
                        </span>
                        {{ __('New Page') }}
                     </li>
                  </ul>
                  <ul >
                     <li class="page-list-section active">
                        <span class="home-icon page-list-item">
                           <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M3 9.83887L12 2.83887L21 9.83887V22.8389H3V9.83887Z" stroke="var(--foreground)" stroke-miterlimit="10"></path>
                           </svg>
                           <!---->Home
                        </span>
                        <span class="page-list-option">
                           <svg class="page-options" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5 11C5.6 11 6 11.4 6 12C6 12.6 5.6 13 5 13C4.4 13 4 12.6 4 12C4 11.4 4.4 11 5 11ZM19 11C19.6 11 20 11.4 20 12C20 12.6 19.6 13 19 13C18.4 13 18 12.6 18 12C18 11.4 18.4 11 19 11ZM12 11C12.6 11 13 11.4 13 12C13 12.6 12.6 13 12 13C11.4 13 11 12.6 11 12C11 11.4 11.4 11 12 11Z" stroke="var(--foreground)" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                           </svg>
                        </span>
                        <!---->
                     </li>
                     <!---->
                     <li class="page-list-section">
                        <span class="page-list-item">
                           <!----><!---->About
                        </span>
                        <span class="page-list-option">
                           <svg class="page-options" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5 11C5.6 11 6 11.4 6 12C6 12.6 5.6 13 5 13C4.4 13 4 12.6 4 12C4 11.4 4.4 11 5 11ZM19 11C19.6 11 20 11.4 20 12C20 12.6 19.6 13 19 13C18.4 13 18 12.6 18 12C18 11.4 18.4 11 19 11ZM12 11C12.6 11 13 11.4 13 12C13 12.6 12.6 13 12 13C11.4 13 11 12.6 11 12C11 11.4 11.4 11 12 11Z" stroke="var(--foreground)" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                           </svg>
                        </span>
                        <!---->
                     </li>
                     <!---->
                     <li class="page-list-section">
                        <span class="page-list-item">
                           <!----><!---->Blog
                        </span>
                        <span class="page-list-option">
                           <svg class="page-options" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5 11C5.6 11 6 11.4 6 12C6 12.6 5.6 13 5 13C4.4 13 4 12.6 4 12C4 11.4 4.4 11 5 11ZM19 11C19.6 11 20 11.4 20 12C20 12.6 19.6 13 19 13C18.4 13 18 12.6 18 12C18 11.4 18.4 11 19 11ZM12 11C12.6 11 13 11.4 13 12C13 12.6 12.6 13 12 13C11.4 13 11 12.6 11 12C11 11.4 11.4 11 12 11Z" stroke="var(--foreground)" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                           </svg>
                        </span>
                        <!---->
                     </li>
                     <!---->
                  </ul>
               </div>
            </div>
         </div>
      </template>
   </div>
   @endvolt
</div>
@stop