class Ai {
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

    setTake(take){
        this.take = take;
    }

    setEvent(event){
        this.event = event;
    }

    streamContent(event, callback){
        Livewire.hook('stream', ({ name, content, replace }) => {
            if(event == name) {
                // console.log(content)
                callback(content);
            }
        });
    }

    // runTitle(){
    //     let $this = this;
    //     let event = 'ai::sectionTitle:'+this.section.uuid;

    //     // console.log(this.section.content)

    //     this.$wireAlpine._section(event, this.section, this.prompt, 'generateTitle').then(r => {
    //         console.log('end')
    //         // this.section.content.title = '';
    //     });
    //     // this.section.content.title = '';
    //     this.streamContent(event, function(content){
    //         if(content === '--ai-start '){
    //             $this.section.content.title = '--';
    //             console.log('start')
    //         }

    //         // console.log(content)
    //         if(content !== '--ai-start '){
    //             $this.section.content.title += content;
    //         }

    //         // console.log($this.section.content.title)
    //     });
    // }

    // runSubTitle(){
    //     let $this = this;
    //     let event = 'ai::sectionSubTitle:'+this.section.uuid;
    //     this.$wireAlpine._section(event, this.section, this.prompt);
    //     this.section.content.subtitle = '';
    //     this.streamContent(event, function(content){
    //         $this.section.content.subtitle += content;
    //     });
    // }

    // runImage(){

    // }

    static async interceptStreamAndReturnFinalResponse(response, callback) {
        let reader = response.body.getReader();
        let remainingResponse = "";
        while (true) {
          let { done, value: chunk } = await reader.read();
          let decoder = new TextDecoder();
          let output = decoder.decode(chunk);
          let [streams, remaining] = this.extractStreamObjects(remainingResponse + output);
          streams.forEach((stream) => {
            callback(stream);
          });
          remainingResponse = remaining;
          if (done)
            return remainingResponse;
        }
    }

    extractStreamObjects(raw2) {
      let regex = /({"stream":true.*?"endStream":true})/g;
      let matches2 = raw2.match(regex);
      let parsed = [];
      if (matches2) {
        for (let i = 0; i < matches2.length; i++) {
          parsed.push(JSON.parse(matches2[i]).body);
        }
      }
      let remaining = raw2.replace(regex, "");
      return [parsed, remaining];
    }

    
    __send(callback = false){
        var $this = this;
        let $objects = {
            ...this.prompt,
            section_id: this.section.uuid,
            take: this.take,
        };
        
        let $prompts = new URLSearchParams($objects).toString();
        var base = "/console/builder/ai";
        var route = base + '?' + $prompts;
        var eventSource = new EventSource(route);
        let isFirstEvent = true;
        
        eventSource.onmessage = function (e) {
            if (e.data == "[DONE]") {
                eventSource.close();
            } else {
                // console.log(JSON.parse(e.data))
                // let txt = JSON.parse(e.data).choices[0].text;
                // if (txt !== undefined) {
                //     if (isFirstEvent) {
                //         txt = '--ai-start-'+txt;
                //         isFirstEvent=false;
                //     }
                //     if(callback){
                //         callback(txt);
                //     }
                // }

                try {
                    let txt = JSON.parse(e.data).choices[0].delta.content
                    if (txt !== undefined) {
                        if (isFirstEvent) {
                            txt = '--ai-start-'+txt;
                            isFirstEvent=false;
                        }
    
                        // txt.replace(/(?:\r\n|\r|\n)/g, '<br>');
                        if(callback){
                            callback(txt);
                        }
                    }
                } catch (error) {
                    
                }
            }
        };

        eventSource.onerror = function (e) {
            eventSource.close();
        };
    }

    run(callback = null){
        let $this = this;
        // let event = 'ai::sectionSubTitle:'+this.section.uuid;


        $this.__send(callback);
        return;
        this.$wireAlpine._section(event, this.section, this.prompt, 'title');
        // this.section.content.subtitle = '';
        
        Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
            // Runs after commit payloads are compiled, but before a network request is sent...
        
            respond(({ status, response }) => {
                // if(response.headers.has('X-Yena-Ai')){
                //     alert('zz')
                // }
                // if(!response.headers.has('X-Yena-Ai')){
                //     alert('zz')
                // }
                // let response = mutableObject.response;
                if (!response.headers.has("X-Yena-Ai")) return;
                response = {
                  ok: true,
                  redirected: false,
                  status: 200,
                  async text() {
                    let finalResponse = await $this.interceptStreamAndReturnFinalResponse(response, (streamed) => {
                        console.log(streamed)
                    });
                    // if (contentIsFromDump(finalResponse)) {
                    //   this.ok = false;
                    // }
                    return finalResponse;
                  }
                };
                console.log(response, response.headers);
                // Runs when the response is received...
                // "response" is the raw HTTP response object
                // before await response.text() is run...
            })
        
            succeed(({ status, json }) => {
                // Runs when the response is received...
                // "json" is the JSON response object...
            })
        
            fail(({ status, content, preventDefault }) => {
                // Runs when the response has an error status code...
                // "preventDefault" allows you to disable Livewire's
                // default error handling...
                // "content" is the raw response content...
            })
        });

        window.addEventListener(event, function(e){
            console.log(e);
        });
        // this.streamContent(event, function(content){
        //     $this.section.content.subtitle += content;
        // });

        console.log('baby')
        // this.runTitle();
        // this.runSubTitle();
        
        // this.$wire.generate('Heyyy');


        // window.addEventListener('')
        // this.section.content.title = 'Brother man'

        // console.log(this.$wireAlpine.heyy)

        // console.log(this.section)

    }
}

export default Ai;