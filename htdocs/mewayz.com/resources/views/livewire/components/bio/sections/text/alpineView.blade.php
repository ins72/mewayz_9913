<div>
    <div x-data="builder__text_section_view">
       <div class="base-text-o">


         <div class="textarea-content">
            <p x-html="section.content.text"></p>
         </div>
        
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__text_section_view', () => {
          return {

             
             init(){
                var $this = this;
                window.addEventListener('section::' + this.section.uuid, (event) => {
                   $this.section = event.detail;
                });
             }
          }
       });
    </script>
    @endscript
 </div>