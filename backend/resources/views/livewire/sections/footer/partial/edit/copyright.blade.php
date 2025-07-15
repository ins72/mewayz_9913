<div>

    <div class="website-section" x-data="builder__footer_copyright">
        <div class="design-navbar" x-intersect.once="initSimpleMDE">
           <ul >
               <li class="close-header !flex">
                  <a @click="__page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Copyright') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form class="form-panel [&_.editor-statusbar]:!hidden [&_.editor-toolbar]:!flex" onsubmit="return false">
            
            <div class="[&_.CodeMirror]:!min-h-[100px] [&_.CodeMirror]:!h-[150px]">
                <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="site.footer.copyright_one" name="title" placeholder="{{ __('Left area') }}" x-ref="copyright__one"></textarea>
            </div>
            
            <div class="[&_.CodeMirror]:!min-h-[100px] [&_.CodeMirror]:!h-[150px] mt-2">
                <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="site.footer.copyright_two" name="title" placeholder="{{ __('Right area') }}" x-ref="copyright__two"></textarea>
            </div>
         </form>
        </div>
     </div>
     @script
     <script>
         Alpine.data('builder__footer_copyright', () => {
            return {
               toolbar: [
                     {
                        name: "copyright",
                        action: function customFunction(editor){
                           let value = editor.codemirror.getValue();
                           let newValue = '©' + new Date().getFullYear() + ' ';
                           editor.value(newValue + value);
                        },
                        className: "fi fi-rr-copyright",
                        title: "Copyright",
                     },
                    {
                        name: "bold",
                        action: window.SimpleMDE.toggleBold,
                        className: "fa fa-bold",
                        title: "Bold",
                        default: true
                    },
                    {
                        name: "italic",
                        action: window.SimpleMDE.toggleItalic,
                        className: "fa fa-italic",
                        title: "Italic",
                        default: true
                    },
                    {
                        name: "link",
                        action: window.SimpleMDE.drawLink,
                        className: "fa fa-link",
                        title: "Create Link",
                        default: true
                    },
             ],
 
             initSimpleMDE(){

               var $this = this;

               let editor = new window.SimpleMDE({
                element: this.$refs.copyright__one,
                toolbar: this.toolbar,
               });
               editor.value(this.site.footer.copyright_one);

               editor.codemirror.on('change', () => {
                this.site.footer.copyright_one = editor.value();
               });


               
               let editorRight = new window.SimpleMDE({
                element: this.$refs.copyright__two,
                toolbar: this.toolbar,
               });
               editorRight.value(this.site.footer.copyright_two);

               editorRight.codemirror.on('change', () => {
                this.site.footer.copyright_two = editorRight.value();
               });
             },
             init(){
               var $this = this;
             }
            }
          });
     </script>
     @endscript
</div>
