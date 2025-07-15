class Banner {
    constructor(section){
        this.section = section;

        let element = document.querySelector('.ai-livewire');

        
        this.$wireAlpine = Alpine.$data(element);
        this.$wire = Livewire.find(element.getAttribute('wire:id'));

        // this.staticSection = this.section;
    }

    setPrompt(prompt){
        this.prompt = prompt;
    }

    streamContent(event, callback){
        Livewire.hook('stream', ({ name, content, replace }) => {
            if(event == name) {
                // console.log(content)
                callback(content);
            }
        });

    }

    runTitle(){
        let $this = this;
        let event = 'ai::sectionTitle:'+this.section.uuid;

        // console.log(this.section.content)

        this.$wireAlpine._section(event, this.section, this.prompt, 'generateTitle').then(r => {
            console.log('end')
            // this.section.content.title = '';
        });
        // this.section.content.title = '';
        this.streamContent(event, function(content){
            if(content === '--ai-start '){
                $this.section.content.title = '--';
                console.log('start')
            }

            // console.log(content)
            if(content !== '--ai-start '){
                $this.section.content.title += content;
            }

            // console.log($this.section.content.title)
        });
    }

    runSubTitle(){
        let $this = this;
        let event = 'ai::sectionSubTitle:'+this.section.uuid;
        this.$wireAlpine._section(event, this.section, this.prompt);
        this.section.content.subtitle = '';
        this.streamContent(event, function(content){
            $this.section.content.subtitle += content;
        });
    }

    runImage(){

    }

    run(){

        this.runTitle();
        // this.runSubTitle();
        
        // this.$wire.generate('Heyyy');


        // window.addEventListener('')
        // this.section.content.title = 'Brother man'

        // console.log(this.$wireAlpine.heyy)

        // console.log(this.section)

    }
}

export default Banner;