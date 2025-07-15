import OpenAI from 'openai'
import Paragraph from '@editorjs/paragraph'

function debounce(func, timeout = 2000) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => {
      func.apply(this, args)
    }, timeout)
  }
}

class AIText extends Paragraph {
  openai

  static get toolbox() {
    return {
      title: 'zzAI TEXT (experimentalss)',
      icon: `<svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M8 4V20M17 12V20M6 20H10M15 20H19M13 7V4H3V7M21 14V12H13V14" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`
    }
  }

  constructor({ api, block, config, data }) {
    super({
      api,
      block,
      config,
      data
    })

    if (!config.openaiKey) {
      throw new Error('OpenAI key is required for AI Text')
    }

    this.openai = new OpenAI({
      apiKey: config.openaiKey,
      dangerouslyAllowBrowser: true
    })
  }
  disableDefaultTabEvent() {
    return true;
  }
  async getAICompletion(content) {
    if (!content) return

    console.log(this.api)
    this.api.saver.save().then((savedData) =>{
        console.log(savedData);
    })

    console.log(content.length > 100
          ? content
          : content.slice(content.length - 100))
    const _ai = await this.openai.chat.completions
      .create({
        messages: [
            {
              role: 'system',
              content: `Give auto complete suggestion.`
            },
            {
              role: 'system',
              content: `Add (TAB) at the end of each generation.`
            },
          {
            role: 'user',
            content: `${
              content.length > 100
                ? content
                : content.slice(content.length - 100)
            }`
          }
        ],
        max_tokens: 256,
        model: 'gpt-3.5-turbo-1106',
        stream: true,
      })
      
      const aiSuggestions = document.createElement('span');
      aiSuggestions.innerHTML = '';
      aiSuggestions.id = 'ai-suggestions';
      aiSuggestions.style.color = 'lightgray';
      this._element.appendChild(aiSuggestions);

      for await (const chunk of _ai) {
        const suggestion = this._element.querySelector('#ai-suggestions');
        
        const content = chunk.choices[0]?.delta?.content || '';

        suggestion.innerHTML = suggestion.innerHTML + content;
      }

      /*.then((response) => {
        const aiSuggestions = document.createElement('span')
        aiSuggestions.innerHTML = ''
        aiSuggestions.id = 'ai-suggestions'
        aiSuggestions.style.color = 'lightgray'
        aiSuggestions.innerHTML = response.choices[0].message.content

        this._element.appendChild(aiSuggestions)

        this._element.querySelector('#ai-suggestions-loader')?.remove()
      })*/
  }

  onInput = debounce((e) => {
    if (
      e.inputType === 'deleteContentBackward' ||
      e.inputType === 'deleteContentForward' ||
      e.inputType === 'insertParagraph' ||
      e.inputType === 'insertFromPaste' ||
      e.inputType === 'insertFromDrop' ||
      !e.target.innerHTML
    ) {
      return
    }

    this.getAICompletion(e.target.innerHTML)
  }, 900)

  closeToolbar() {
    this.api.toolbar.open();

    // then do something else
  }

  onKeyUp(e) {
    const popovers = document.querySelectorAll('.ce-popover');
    this.closeToolbar()
    console.log(this)
    
    if (e.code === 'Escape' || e.code === 'Backspace') {
      this._element.querySelector('#ai-suggestions')?.remove()

      return
    }

    if (e.code === 'Tab') {

      setTimeout(function () {


        popovers.forEach(item => {
          console.log(item)
          item.classList.remove('ce-popover--opened');
        })
      }, 200)
      const aiSuggestionElement = this._element.querySelector('#ai-suggestions')
      const aiSuggestionElementTextContent = aiSuggestionElement?.textContent

      if (!aiSuggestionElementTextContent) return

      const aiSuggestionTextNode = document.createTextNode(
        aiSuggestionElementTextContent
      )

      this._element.appendChild(aiSuggestionTextNode)
      aiSuggestionElement.remove()

      return
    }

    if (e.code !== 'Backspace' && e.code !== 'Delete') {
      return
    }

    const { textContent } = this._element

    if (textContent === '') {
      this._element.innerHTML = ''
    }
  }
    // new method
    onBackslash(e, _this) {
        const isEmpty = !_this.data.text.trim?.() || _this._data.text === '&nbsp;'

    
        if (e.key === '/' && isEmpty) {
            console.log(_this.api)
          // Actually, Editorjs has a method
          _this.api.toolbar.toggleToolbox()
          e.preventDefault()
        }
      }

  drawView() {
    const div = document.createElement('DIV')
    const _this = this;

    div.classList.add(this._CSS.wrapper, this._CSS.block)
    div.contentEditable = false
    div.dataset.placeholder = this.api.i18n.t(this._placeholder)

    if (this._data.text) {
      div.innerHTML = this._data.text
    }

    if (!this.readOnly) {
      div.contentEditable = true
      div.addEventListener('keyup', this.onKeyUp)
      div.addEventListener('input', this.onInput)
      div.addEventListener('keydown', (e) => {
        _this.onKeyUp(e)
        _this.onBackslash(e, _this)
      }) // <---------- added this lime
      div.addEventListener('input', () => {
        const suggestion = this._element.querySelector('#ai-suggestions');
        if(suggestion) suggestion.remove();
      })
    }

    return div
  }
}

export default AIText