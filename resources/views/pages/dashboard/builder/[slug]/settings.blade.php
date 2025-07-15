<?php
   use function Laravel\Folio\name;


   name('dashboard-builder-settings');
?>
@extends('components.layouts.builder')
@section('content')

<div class="application_sidebar-content">
   <div class="flex -align-center -justify-between"><a class="flex -align-center text -small mb-16" href="{{ __s()->toRoute('dashboard-builder-index') }}" @navigate><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="arcs"><path d="m15 18-6-6 6-6"></path></svg>Back</a><button class="button -small" style="opacity: 0; pointer-events: none;">Save</button></div>
   <form>
      <h1 class="mt-24 mb-16">Settings</h1>
      <h2>Site</h2>
      <div class="stack -form mt-16 pb-16">
         <div class="field"><label class="field_label" for="name">Name</label><input class="input" id="name" type="text" name="name"></div>
         <div class="field">
            <label class="field_label" for="icon">Icon</label>
            <div class="flex -gap-16"><label class="button -secondary -smaller">Choose file<input id="icon" accept=".png,.jpeg,.jpg,.gif,.svg" type="file"></label></div>
         </div>
      </div>
      <h2 class="mt-24">Domains</h2>
      <div class="stack -form mt-16 pb-16">
         <div class="field"><label class="field_label" for="subdomain">Default domain</label><label class="field_suffix" for="subdomain">.site.co</label><input class="input" id="subdomain" type="text" name="subdomain"></div>
         <div class="field"><label class="field_label" for="domain">Custom domain</label><input class="input" id="domain" placeholder="yoursite.com" type="text" name="domain"></div>
         <div class="text -smaller stack -gap-8 mt-12"><button class="flex">Show instructions</button></div>
      </div>
      <h2 class="mt-24">Team</h2>
      <div class="stack -form mt-16 pb-16">
         <div class="stack -gap-16">
            <div class="flex -justify-between -align-baseline">
               <div>
                  <p class="text -small -medium"> (you)</p>
                  <p class="text -smaller -light"></p>
               </div>
               <p class="text -light -smaller">Administrator</p>
            </div>
         </div>
         <p class="mt-24 text -small">Share this link to invite people to collaborate on this site:</p>
         <div class="flex -gap-8 mt-8"><input class="input -smaller" style="max-width: 220px;" readonly="" type="text" value="app.pagy.co/join/49a62985"><button class="button -secondary -smaller" type="button">Copy</button></div>
      </div>
      <h2 class="mt-24">Billing</h2>
      <div class="stack -form mt-16 pb-16">
         <div class="text -small">
            <p class="">Your free trial ends in 6 days.</p>
            <button class="button -small mt-16" type="button">Subscribe</button>
         </div>
      </div>
      <h2 class="mt-24">More</h2>
      <div class="flex -gap-16 mt-16 pb-24"><button class="button -secondary -smaller" type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="radix-:r3d:" data-state="closed">Duplicate site</button><button class="button -secondary -smaller" type="button">Copy template link</button><button class="button -destructive -smaller" type="button">Delete site</button></div>
   </form>
   
</div>

<div x-init="sidebarClass = '-medium'"></div>


<script>
   
</script>
@stop