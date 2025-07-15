
<?php

   use App\Yena\VCard\VCard;
   use App\Models\SiteForm;
   use function Livewire\Volt\{state, mount, placeholder, on};

   state([
      'site',
   ]);

   state(['contacts']);

   mount(function(){
      $this->getContacts();
   });

   placeholder('
   <div class="w-full p-5 mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
   
   
   $getContacts = function(){
      $this->contacts = SiteForm::where('site_id', $this->site->id)->orderBy('id', 'DESC')->get()->toArray();
      return $this->contacts;
   };

   $saveContact = function($item){
      $this->skipRender();      
      if(!$form = SiteForm::where('uuid', ao($item, 'uuid'))->where('site_id', $this->site->id)->first()) return;

      $form->fill($item);
      $form->save();
   };

   $deleteContact = function($item){
      $this->skipRender();      
      SiteForm::where('uuid', ao($item, 'uuid'))->where('site_id', $this->site->id)->delete();
   };
   
   $export = function(){
      $contacts = $this->getContacts();
      $name = 'contact';

      $array = [];

      foreach($contacts as $contact){
         $array[] = [
            ...ao($contact, 'content'),
            'email' => ao($contact, 'email'),
            'registration' => \Carbon\Carbon::parse(ao($contact, 'created_at'))->toFormattedDateString()
         ];
      }

      
      $columns = ['email', 'first_name', 'last_name', 'phone', 'message', 'company', 'registration'];
     
      $slug = \Str::random(3);
      $name = slugify($name, '-');
      $date = slugify(\Carbon\Carbon::now()->toFormattedDateString(), '-');
      $fileName = "$name-$slug-$date.csv";

      $headers = array(
          "Content-type"        => "text/csv",
          "Content-Disposition" => "attachment; filename=$fileName",
          "Pragma"              => "no-cache",
          "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
          "Expires"             => "0"
      );

      $callback = function() use($array, $columns) {
          $file = fopen('php://output', 'w');
          fputcsv($file, $columns);
          foreach ($array as $key => $value) {
              $column = [];
              foreach ($columns as $col_key => $col_value) {
                  $column[] = ao($value, $col_value);
              }
              fputcsv($file, $column);
          }
          fclose($file);
      };
      
      return response()->streamDownload($callback, $fileName, [
          'Content-Type' => 'text/csv'
      ]);
   };

   $saveVCard = function($item){

      $vcard = new VCard();
      $additional = '';
      $prefix = '';
      $suffix = '';
      $fullName = true;

      $vcard->addNames(ao($item, 'content.last_name'), ao($item, 'content.first_name'), $additional, $prefix, $suffix, $fullName);
      $vcard->addPhone(ao($item, 'content.phone'), 'HOME');
      $vcard->addJobtitle(ao($item, 'content.company'));
      $vcard->addEmail(ao($item, 'email'));
      $vcard->addNote(ao($item, 'content.message'));
      
      // define output
      $output = $vcard->genVCard();
      return response()
          ->streamDownload(function () use($output, $vcard) {
          echo $output;
      }, 'contact-' .str()->random(3). '.' . $vcard->getFileExtension(), [
          'Content-Type' => 'text/x-vcard'
      ]);
   };
?>

<div>

   {{-- <div wire:poll.visible="getContacts"></div> --}}

   <div x-data="builder__contact">


      <div wire:ignore>
         <template x-for="(item, index) in contacts" :key="item.uuid">
            <div x-show="__page == 'page::contact::' + item.uuid">
                <div>
                   <x-livewire::components.builder.parts.contact.detail />
                 </div>
            </div>
         </template>
      </div>

      <div x-cloak x-show="__page == '-'">
        <div class="settings-section section">
            <div class="settings-section-content">
        
                <div class="top-bar">
                  <div class="page-settings-navbar">
                     <ul >
                        <li class="close-header">
                              
                           <a @click="closePage('pages')">
                              <span>
                                 {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                              </span>
                           </a>
                        </li>
                        <li class="">{{ __('Contact') }}</li>
                        <li class="!flex items-center !justify-center pr-5">
                          <button class="btn btn-save !bg-black !text-[var(--c-light)] !rounded-md p-0 !flex whitespace-nowrap !w-full !px-2" @click="$wire.export()">{{ __('Export') }}</button>
                       </li>
                     </ul>
                 </div>
                 <div class="sticky container-small">
                           
               <template x-if="contacts.length == 0">
                  <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                     {!! __i('Support, Help, Question', 'checklist-user', 'w-14 h-14') !!}
                     <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                        {!! __t('Share your site to start receiving form submissions.') !!}
                     </p>
                  </div>
                </template>
                  <div class="contacts">
                     <div class="contact-list">
                        <template x-for="(item, index) in contacts" :key="item.uuid">
                           <div class="card" @click="__page='page::contact::' + item.uuid">
                              <a name="edit">
                                 <div class="card-body">
                                    <h2 class="card-title truncate" x-text="item.content.first_name ? item.content.first_name : item.email"></h2>
                                    <p class="card-description truncate" x-text="item.content.first_name ? item.email : ''"></p>
                                 </div>
                              </a>
                              <div class="card-option">
                                 <div class="contact-option" @click="">
                                    <span>
                                       {!! __i('--ie', 'eye.2') !!}
                                    </span>
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
    
      @script
      <script>
          Alpine.data('builder__contact', () => {
             return {
               contacts: @entangle('contacts').live,
               __page: '-',
               init(){
                  var $this = this;
               }
             }
          });
      </script>
      @endscript
    </div>
</div>