<div>

   <div x-data="builder__linksSectionView">
    <div>
        <template x-for="(item, index) in _.sortBy(section.items, 'position')" :key="item.uuid">
             <div>
                <template x-if="section.settings.style == '-' || !section.settings.style">
                    <x-livewire::components.bio.sections.links.partial.views.style-1/>
                </template>
                <template x-if="section.settings.style && section.settings.style !== '-'">
                    <x-livewire::components.bio.sections.links.partial.views.style-2/>
                </template>
             </div>
        </template>
    </div>
  </div>
  
   @script
     <script>
         Alpine.data('builder__linksSectionView', () => {
            return {
               colClass: 'grid-cols-1',
               
               init(){
                  var $this = this;
                  window.addEventListener('section::' + this.section.uuid, (event) => {
                     $this.section = event.detail;

                     // console.log($this.section);
                  });
               }
            }
         });
     </script>
   @endscript
     
</div>