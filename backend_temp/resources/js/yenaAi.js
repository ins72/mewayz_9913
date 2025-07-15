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
                $this.done();
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

    image(callback = null){
        let $this = this;
        if(this.prompt.generateImages == 'none'){
            return;
        }

        let query = this.prompt.category;
        if(this.prompt.imageQuery){
            query = this.prompt.imageQuery;
        }
        this.$wire.generate_image(query, this.prompt.generateImages).then(r => {
            if(callback && r){
                callback(r);
            }
            
            $this.done();
        });
    }

    done(){
        var event = new CustomEvent('stopAiLoader');
        window.dispatchEvent(event);

        var event = new CustomEvent('aiStopEvent', {
           detail: this.section,
        });
        window.dispatchEvent(event);
    }
    run(callback = null){
        let $this = this;
        $this.__send(callback);

    }
}

export default Ai;