<div>

   <div class="website-section" x-data="builder__footer_text">
       <div class="design-navbar" x-intersect.once="initSimplemde">
          <ul >
              <li class="close-header !flex">
                 <a @click="__page='-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0">{{ __('Text') }}</li>
             <li></li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
        <form class="form-panel [&_.editor-statusbar]:!hidden [&_.editor-toolbar]:!flex" onsubmit="return false">
           <div class="[&_.CodeMirror]:!min-h-[100px] [&_.CodeMirror]:!h-[150px]">
               <textarea class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="site.footer.text" placeholder="{{ __('Type something') }}" x-ref="footer_text"></textarea>
           </div>
        </form>
       </div>
    </div>
    @script
    <script>
        Alpine.data('builder__footer_text', () => {
           return {
              toolbar: [
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
            editor: null,

            initSimplemde(){

               if(this.editor) return;

              var $this = this;

              let editor = new window.SimpleMDE({
               element: this.$refs.footer_text,
               toolbar: this.toolbar,
              });
              editor.value(this.site.footer.text);

              editor.codemirror.on('change', () => {
               this.site.footer.text = editor.value();
              });

              this.editor = editor;
            },

            init(){
              var $this = this;
            }
           }
         });
    </script>
    @endscript
</div>
