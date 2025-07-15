<div>
    <div x-data="builder__timer_section_view">
       <div>

         
         <div class="builder-block -timer-block w-[100%] h-full">
 
             <div class="-item-style !p-4">
                 <div class="--style">
                  <div>
                     
                     <div class="justify-center flex">
                        <div x-ref="timerView" class="flipdown"></div>
                     </div>
                  </div>
                 </div>
                 <div class="--item--bg"></div>
             </div>
         </div>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__timer_section_view', () => {
          return {
            show_modal: false,
            _strtotime(dateString) {
               // Create a new Date object from the date string
               const date = new Date(dateString);
               
               // Check if the date is valid
               if (isNaN(date.getTime())) {
                  return null; // Return null if the date is invalid
               }

               // Return the Unix timestamp (seconds since January 1, 1970)
               return Math.floor(date.getTime() / 1000);
            },
            initTimer(){
               let $el = this.$root.querySelector('[x-ref="timerView"]');
               let $date = this.section.content.date ? this._strtotime(this.section.content.date) : (new Date().getTime() / 1000) + (86400 * 2) + 1;

               let flip = new FlipDown($date, $el, {
                  theme: "light",
               }).start();



               // console.log(this.section.content.date, $el, this._strtotime(this.section.content.date))
            },
            init(){
               var $this = this;

               $this.initTimer();
               window.addEventListener('section::' + this.section.uuid, (event) => {
                  $this.section = event.detail;
               });
            }
          }
       });
    </script>
    @endscript
 </div>