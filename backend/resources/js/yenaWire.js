import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Tooltip from "@ryangjchandler/alpine-tooltip";
import Sortable from 'sortablejs';
import masonry from 'alpinejs-masonry'
// import component from '@vimesh/ui'
import md5 from 'crypto-js/md5';
 
import collapse from '@alpinejs/collapse'
import SimpleMDE from "SimpleMDE";
import { marked } from "marked";
import moment from 'moment';
import Navigo from 'navigo';
import Sections from './sections'
import navigate from './navigate'
import RateYo from "rateyo";

import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header'; 
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import Warning from '@editorjs/warning';
import CodeTool from '@editorjs/code';
import RawTool from '@editorjs/raw';
import Checklist from '@editorjs/checklist';
import Paragraph from '@editorjs/paragraph';
import ImageTool from '@editorjs/image';
import { Calendar } from '@fullcalendar/core';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import Color from 'color';

window.Color = Color
window.Calendar = Calendar;
window.interactionPlugin = interactionPlugin;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.listPlugin = listPlugin;
window.EditorJS = EditorJS;
window.EditorTools = {
    Header,
    List,
    Quote,
    Delimiter,
    Warning,
    CodeTool,
    RawTool,
    Checklist,
    ImageTool,
    Paragraph,
};

window.Navigo = Navigo;
window.moment = moment;
window.md5 = md5;
window.marked = marked
window.SimpleMDE = SimpleMDE;
window.Sortable = Sortable;

window.markdownRender = function(markdown){

    return window.marked(markdown);
};


window.simplemdeInit = function(el){
    return new SimpleMDE({
        element: el,
        toolbar: [
            {
                name: "bold",
                action: SimpleMDE.toggleBold,
                className: "fa fa-bold",
                title: "Bold",
                default: true
            },
            {
                name: "italic",
                action: SimpleMDE.toggleItalic,
                className: "fa fa-italic",
                title: "Italic",
                default: true
            },
            {
                name: "strikethrough",
                action: SimpleMDE.toggleStrikethrough,
                className: "fa fa-strikethrough",
                title: "Strikethrough"
            },
            {
                name: "heading",
                action: SimpleMDE.toggleHeadingSmaller,
                className: "fa fa-header",
                title: "Heading",
                default: true
            },
            {
                name: "unordered-list",
                action: SimpleMDE.toggleUnorderedList,
                className: "fa fa-list-ul",
                title: "Generic List",
                default: true
            },
            {
                name: "ordered-list",
                action: SimpleMDE.toggleOrderedList,
                className: "fa fa-list-ol",
                title: "Numbered List",
                default: true
            },
            {
                name: "clean-block",
                action: SimpleMDE.cleanBlock,
                className: "fa fa-eraser fa-clean-block",
                title: "Clean block"
            },
            {
                name: "link",
                action: SimpleMDE.drawLink,
                className: "fa fa-link",
                title: "Create Link",
                default: true
            },
            {
                name: "image",
                action: SimpleMDE.drawImage,
                className: "fa fa-picture-o",
                title: "Insert Image",
                default: true
            },
            {
                name: "horizontal-rule",
                action: SimpleMDE.drawHorizontalRule,
                className: "fa fa-minus",
                title: "Insert Horizontal Line"
            },
        ],
    });
}

Alpine.magic('clipboard', () => subject => {
    var input = document.createElement('input');
    input.setAttribute('value', subject);
    input.style.opacity = "0";
    document.body.appendChild(input);
    input.select();
    var result = document.execCommand('copy');
    document.body.removeChild(input);
    return result;
    // navigator.clipboard.writeText(subject)
});
Alpine.directive('pickr', (el, { value, modifiers, expression }, { Alpine, effect, evaluate, evaluateLater, cleanup }) => {
    // let input = evaluate(expression);
    let ev = evaluate(expression);

    let pickr = Pickr.create({
        el: el,
        default: ev,
        ...window.pickrOptions,
    });
    console.log(expression)
    pickr.on('changestop', (source, instance) => {
        let event = new CustomEvent('pickr:' + expression, {
           detail: instance._color.toHEXA().toString(),
        });
        window.dispatchEvent(event);
        pickr.applyColor();
    });
});
Alpine.directive('rating', (el, { value, modifiers, expression }, { Alpine, effect, evaluate, evaluateLater, cleanup }) => {
    // let input = evaluate(expression);
    let _rating = evaluate(expression);
    let evRating = evaluateLater(expression);

    !_rating || _rating == null ? 0 : _rating;

    // console.log(_rating,expression)

    let options = {
        rating: _rating,
        numStars: 5,
        precision: 2,
        minValue: 1,
        maxValue: 5,
        fullStar: true,
        readOnly: true,
        ratedFill: "#ffd05b",
      };
      if(value == 'input'){
        options.readOnly = false;
      }

      if(el.getAttribute('data-size')){
        options.starWidth = el.getAttribute('data-size');
      }

    
    let rateyo = RateYo(el, options).on("rateyo.set", function (rating, data) {
        if(value == 'input'){
            evaluate(`${expression} = '${data.rating}'`);
        }
    }).on("rateyo.change", function (e, data) {
        
    });
    effect(() => {
        evRating(value => {
            rateyo.option('rating', value)
        });
    });
});
Alpine.directive('outlink', (el, { value, modifiers, expression }, { Alpine, effect, evaluate, evaluateLater, cleanup }) => {
    let tab = evaluate(expression + '_tab');
    let link = evaluate(expression);
    let _alpine = Alpine.$data(Alpine.closestRoot(el));

    let target = tab ? '_blank' : '_self';
    let _link = _alpine.$store.builder.linker(link);
    Alpine.bind(el, {'href': _link});
    Alpine.bind(el, {'target': target});
    if(value == 'navigate'){
        Alpine.bind(el, {
            'x-link.prefetch': '',
        })
    }


    Alpine.bind(el, {'@click': function(){
        if(_link == 'javascript:void(0)') return;
        _alpine.$dispatch('saveLinker', {
           link: link
        });
    }});

    if(!_alpine.$store.builder.isValidUrl(_link)){
        el.classList.add('yena-site-link');
        if(_alpine.router){
            _alpine.router.updatePageLinks();
        }
    }
});
Alpine.directive('bit', (el, { value, modifiers, expression }, { Alpine, effect, evaluate, evaluateLater, cleanup }) => {

    var component = value;
    if(!value){
        var options = evaluate(expression);
        component = options;
    }
    
    var tpl = document.querySelector('[bit-component="'+ component +'"]');
    if(!tpl) return;
    // var wrapper = document.createElement('div');
    // wrapper.innerHTML = tpl.innerHTML;
    // tpl = wrapper;
    // console.log(tpl, tpl.querySelectorAll('.screen'));
    // tpl.querySelectorAll('.screen').forEach((e) => {
    //     console.log(e)
    //     e.remove();
    // });
    // if(modifiers.includes('clean')){
    // }
    var tpl = tpl.innerHTML;
    // console.log(modifiers, tpl, modifiers.includes('clean'))

    var data =  {
        init(){
            Alpine.effect(() => {
                // this.checklist = this._checklist.slice();
            });
        },
        html: tpl
    };
    var reactiveData = Alpine.reactive(data);
    var destroyScope = Alpine.addScopeToNode(el, reactiveData);

    //reactiveData['init'] && evaluate(el, reactiveData['init'])
    // Alpine.bind(el, {'x-modelable': '_checklist'});
    // Alpine.bind(el, {'@click': 'togglePanel'});
    // Alpine.bind(el, {':class': '{active: checklist.length > 0}'});

    Alpine.nextTick( () => {
        Alpine.bind(el, {'x-html': 'html'});
    });

    evaluate(reactiveData['init']);
    
    cleanup(() => {
        destroyScope();
    })
});

Alpine.directive('sortable', (el, { expression }, { evaluate }) => {
  let options = evaluate(expression)
  Sortable.create(el, {
    animation: 150,
    //forceFallback: true,
    sort: true,
    scroll: true,
    scrollSensitivity: 100,
    delay: 100,
    delayOnTouchOnly: true,
    group: false,
    swapThreshold: 5,
    filter: ".disabled",
    preventOnFilter: true,
    containment: "parent",
    ...options.options,
    onUpdate: (e) => {
       var data = [];
       el.querySelectorAll('.sortable-item').forEach(function(elm, i) {
          var items = {
                id: elm.getAttribute('data-id'),
                position: i
          };
          data.push(items)
       });

    //    console.log(options._site)
       options.callback(data);
    },
  });
});
Alpine.data('confirmDotsHandler', () => {
   return {
        dotsArray: ['.', '.', '.', '.', '.'],
        init() {

            var timer = setInterval(() => {
                if(this.dotsArray.length > 0){
                    this.dotsArray.splice(0, 1);
                }else{
                    clearInterval(timer)
                }
            }, 1000);
        }
    }
});

Alpine.data("alpineToasted", () => ({
    show: false,
    type: '',
    message: '',
    width: 100,
    duration: 4,
    animateWidth() {
        let $this = this;
        $this.width = 100;
        const interval = 1000; // 20ms interval
        const decrement = (100 / (this.duration * 1000 / interval));
        
        const intervalId = setInterval(() => {
            if (this.width > 0) {
                this.width -= decrement;
            } else {
                clearInterval(intervalId);
                this.width = 0; // Ensure it ends exactly at 0
            }
        }, 0);
    },
    toggle() {
      this.show = !this.show;
    },

    close() {
        this.show = false;
    },

    init(){
        let $this = this;
        window.addEventListener('_alpine_toast', function(event){
            $this.type = event.detail.type;
            $this.message = event.detail.message;
            $this.toggle();
            $this.animateWidth();

            setTimeout(() => {
                $this.close();
            }, 4000);
        });
    }
}));

const toastUp = function(type, message){
    // const el = document.querySelector('.livewire_toast');
    // el._x_dataStack[0].type = type;
    // el._x_dataStack[0].message = message;

    // el._x_dataStack[0].toggle();

    var event = new CustomEvent('_alpine_toast', {
        detail: {
            type: type,
            message: message
        }
    });
    window.dispatchEvent(event);
}

window.runToast = toastUp;

window.isValidUrl = function(urlString) {
    var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
  '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator


    return !!urlPattern.test(urlString);
};

window.getCookie = function(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
};
window.setCookie = function(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    const expires = "expires="+ d.toUTCString();
    document.cookie = `${name}=${value};${expires};path=/`;
};

Alpine.data('timelineComponent', (id = null) => {
   return {
        showTimeline: getCookie('showTimelinec' + id) === 'true',

        toggleTimeline() {
            this.showTimeline = !this.showTimeline;
            setCookie('showTimelinec' + id, this.showTimeline, 7); // Store for 7 days
        }
    }
});

Alpine.store('app', {
    layoutSidebar: false,
    isShortSidebar: false,

    randomAvatar(seed){
        let avatar = 'https://api.dicebear.com/8.x/initials/svg?seed=' + seed;
        return avatar;
    },
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    },
    setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        const expires = "expires="+ d.toUTCString();
        document.cookie = `${name}=${value};${expires};path=/`;
    },
    getRandomString(length) {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        const charactersLength = characters.length;
        
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }

        return result;
    },

    toggleSidebar(){
        this.layoutSidebar =! this.layoutSidebar;
    },
    addHttps(url, scheme = 'https') {
        const hasScheme = url.startsWith('http://') || url.startsWith('https://');
        return hasScheme ? url : `${scheme}://${url}`;
    },
    isValidUrl(urlString) {
        var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
      '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator
    
    
        return !!urlPattern.test(urlString);
    },

    init(){
        let $this = this;
        document.addEventListener('livewire:navigated', (e) => {
            $this.layoutSidebar = false;
        });
    }
});

Alpine.store('site', {
    router: null,
    initNavigate(){
        var routes = new Navigo('/untitlednvvvt4yv', {
            hash: true,
            linksSelector: "a",
            strategy: 'ALL'
        });
        routes.on({
            '/foo/bar': {
                as: 'routeA',
                uses: () => {
                    console.log('lollll')
                }
            }
        });
        routes.resolve();
        this.router = routes;
        routes.updatePageLinks()
        
        setTimeout(() => {
            // routes.updatePageLinks()
        }, 1000);
    },
    navigate(link, $event){
        $event.stopPropagation();
        this.router.navigate("/foo/bar");
    },
    link(link){
        if(link.startsWith('/') && !window.isValidUrl(link)){

        }

        console.log(Alpine.$store)
        
    },

    linkTarget(tab = false){
        let target = '_self';
        if(tab){
            target = '_blank';
        }

        

        return target;
    },
});
Alpine.store('bioBackground', {
    site: null,
    get gradient1RGB() {
        return this.hex2rgb(this.site.background.color_1);
    },
    get gradient2RGB() {
        return this.hex2rgb(this.site.background.color_2);
    },
    get animateAngle() {
        return this.site.background.gradient_angle ? this.site.background.gradient_angle : 45;
    },
    get noneanimateAngle() {
       return this.site.background.gradient_angle ? this.site.background.gradient_angle : 15.92;
    },
    get animateCss() {
       return `rgba(0, 0, 0, 0) linear-gradient(${this.animateAngle}deg, ${this.gradient2RGB}, ${this.gradient1RGB}, ${this.gradient2RGB}, ${this.gradient1RGB}) repeat scroll 0% 0% / 400% 400%`;
    },
    get noneAnimateCss() {
       return `rgba(0, 0, 0, 0) linear-gradient(${this.noneanimateAngle}deg, ${this.site.background.color_1} 7.76%, ${this.site.background.color_2} 94.18%) repeat scroll 0% 0%`;
    },
    backgroundStyle() {
       return `background: ${this.site.background.gradient_animate ? this.animateCss : this.noneAnimateCss}`;
    },
    gradientClass() {
       return this.site.background.gradient_animate ? 'gradient-color animate' : 'gradient-color';
    },
    hex2rgb(hex) {
        const bigint = parseInt(hex.replace('#', ''), 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgb(${r}, ${g}, ${b})`;
    },
});
Alpine.store('bio', {

    linker(link){
        let _link = link;
        if(link && link !== '/' && link.startsWith('/')){
            _link = link.substring(1);
        }

        if(!link) {
            _link = 'javascript:void(0)';
        }

        return _link;
    },

    linkTarget(site, link){

        return '_self';
    },

    generateSiteLink(site){
        let link = window.builderObject.currentBaseUrl +'/'+ window.builderObject.sitePrefix + site.address;


        return link;
    },
    isValidUrl(urlString) {
        var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
      '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator


        return !!urlPattern.test(urlString);
    },
    gs(media){
        var _url = window.builderObject.baseUrl;

        return _url + '/' + media;
    },
    getMedia(media, location = null){
        if(!media) return;

        if(media.includes('://') && this.isValidUrl(media)) return media;

        var _url = window.builderObject.mediaUrl;

        if(location){
            _url = window.builderObject.baseUrl + '/' + location;
        }

        if(media.includes('-yena')){
            _url = window.builderObject.mediaUrl;
        }
        return _url + '/' + media;
    },

    getContrastColor(hexColor) {
        if(!hexColor) hexColor = '#000000';

        if(!hexColor.startsWith('#')) hexColor = '#' + hexColor;
        // Convert hex color to RGB
        var r = parseInt(hexColor.substr(1, 2), 16);
        var g = parseInt(hexColor.substr(3, 2), 16);
        var b = parseInt(hexColor.substr(5, 2), 16);
    
        // Calculate luminance
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
        // Choose black or white based on luminance
        return luminance > 0.5 ? "#000000" : "#ffffff";
    },
    generateBodyFont(site){
        if(site.settings.fontSettings === undefined) return;
        var element = document.head.querySelector('[id="google-site-font-body"]');
        if(element) element.remove();

        var href = `https://fonts.googleapis.com/css?family=${site.settings.fontName}:${site.settings.fontSettings.variants}`;
        var styles = document.createElement('link');
        styles.rel = 'stylesheet';
        styles.type = 'text/css';
        styles.href = href;
        styles.id = 'google-site-font-body';
        document.getElementsByTagName('head')[0].appendChild(styles);
    },
    generateHeadFont(site){
        if(site.settings.fontHeadSettings === undefined) return;
        var element = document.head.querySelector('[id="google-site-font-head"]');
        if(element) element.remove();

        var href = `https://fonts.googleapis.com/css?family=${site.settings.fontHeadName}:${site.settings.fontHeadSettings.variants}`;
        var styles = document.createElement('link');
        styles.rel = 'stylesheet';
        styles.type = 'text/css';
        styles.href = href;
        styles.id = 'google-site-font-head';
        document.getElementsByTagName('head')[0].appendChild(styles);
    },
    generateSiteDesign(site){
        this.generateBodyFont(site);
        this.generateHeadFont(site);
        var shape = '', min_shape = '', sublink_shape = '';

        var background_color = site.settings.color;
        // if(site.background.color == 'default'){
        //     background_color = site.settings.color_two;
        // }
        var contrast_color = this.getContrastColor(background_color);

        let bg_contrast_color = '#000';

        if(site.background.color == 'accent' || site.background.color == 'default'){
            if(site.settings.text_color == 'white'){
                bg_contrast_color = '#fff';
            }
        }

        return {
            '--accent': background_color,
            '--standalone': site.settings.color_two,
            '--shape': shape,
            '--min-shape': min_shape,
            '--sublinks-shape': sublink_shape,
            '--contrast-color': contrast_color,
            '--bg-contrast-color': bg_contrast_color,
            '--design-headFont': site.settings.fontHeadName,
            '--design-bodyFont': site.settings.fontName,
        }
    },
    generateSectionClass(site){
        var object = {
            'section-height-fill': site.background.height == 'fill',
            'section-height-fit': site.background.height == 'fit',

            'standalone': site.background.color == 'default',
            'accent': site.background.color == 'accent' || site.background.color == 'default',
            'parallax': site.background.parallax,


            '[--spacingTB:var(--s-1)]': site.background.spacing == 's',
            '[--spacingLR:var(--s-1)]': site.background.spacing == 's',

            '[--spacingTB:calc(var(--unit)_*_2)]': site.background.spacing == 'm',
            '[--spacingLR:calc(var(--unit)_*_2)]': site.background.spacing == 'm',

            '[--spacingTB:calc(var(--unit)_*_5)]': site.background.spacing == 'l',
            '[--spacingLR:calc(var(--unit)_*_5)]': site.background.spacing == 'l',

            '[--spacingTB:calc(var(--unit)_*_8)]': site.background.spacing == 'xl',
            '[--spacingLR:calc(var(--unit)_*_8)]': site.background.spacing == 'xl',

            '[--bg-accent-color:--accent]': site.background.color == 'accent',
            '[--bg-color:--standalone]': site.background.color == 'default',

            'media': site.background.image,
            'image-selected': site.background.image,

            '[--bg-grayscale:0%]': !site.background.greyscale,
            '[--bg-grayscale:100%]': site.background.greyscale,

            '[--bg-image:var(--section-image)]': site.background.image,
            '[--background-upper:center]': true,
            '[--background-bottom:center]': true,


            // BLur
            '[--bg-blurscale:1.1]':true,
            '[--bg-blur:0px]': !site.background.blur,
            '[--bg-blur:5px]': site.background.blur && site.background.blur_size == 's',
            '[--bg-blur:20px]': site.background.blur && site.background.blur_size == 'm',
            '[--bg-blur:40px]': site.background.blur && site.background.blur_size == 'l',

            // Overlay
            
            '[--bg-opacity:1]': !site.background.overlay,
            '[--bg-opacity:0.75]': site.background.color !== 'transparent' && site.background.overlay && site.background.overlay_size == 's',
            '[--bg-opacity:0.5]': site.background.color !== 'transparent' && site.background.overlay && site.background.overlay_size == 'm',
            '[--bg-opacity:0.25]': site.background.color !== 'transparent' && site.background.overlay && site.background.overlay_size == 'l',
        };


        return object; 
    },
});

Alpine.store('builder', {
    autoSaveDelay: 2000,
    savingState: 2,

    sortableOptions: {
        animation: 150,
        sort: true,
        scroll: true,
        scrollSensitivity: 100,
        delay: 100,
        delayOnTouchOnly: true,
        group: false,
        swapThreshold: 5,
        filter: ".disabled",
        preventOnFilter: true,
        containment: "parent",
    },
    setColorLightness(textColor = '#000000', lightness = 0.1, alt = 0.8) {
        let color = Color(textColor).hsl().lightness(lightness);
    
        if (this.getContrastColor(textColor, true)) {
            color = color.lightness(alt);
        }
    
        return color.rgb().hex();
    },
    hexToRgba(hex, alpha) {
        if(!hex) return;
        // Remove the hash at the start if it's there
        hex = hex.replace(/^#/, '');

        // Parse r, g, b values
        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);

        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    },
    removeAlpineAttributes(html) {
        const alpineAttributes = [
            'x-data', 'x-init', 'x-bind', 'x-on', 'x-model', 'x-show', 'x-if', 'x-for', 'x-text', 'x-html', 'x-bit', 'x-outlink'
            // Add more Alpine.js attributes as needed
        ];

        const otherAttributes = [
            'x-bit.clean', 'wire:ignore', '@click', 'wire:id', 'wire:snapshot', 'wire:effects'
        ];
    
        // Create a DOM parser
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
    
        // Remove template tags
        doc.querySelectorAll('template').forEach(template => {
            template.remove();
        });
    
        // Remove Alpine attributes and :class, :style, etc.
        alpineAttributes.forEach(attr => {
            doc.querySelectorAll(`[${attr}]`).forEach(element => {
                element.removeAttribute(attr);
            });
    
            // Remove x-bind: and shorthand syntax
            doc.querySelectorAll(`[${attr}\\:]`).forEach(element => {
                element.getAttributeNames().forEach(name => {
                    if (name.startsWith(`${attr}:`)) {
                        element.removeAttribute(name);
                    }
                });
            });
        });

        doc.querySelectorAll('link[rel="preload"], link[rel="modulepreload"]').forEach(element => {
            element.remove();
        });
    
        // Remove shorthand bindings like :class, :style, etc.
        doc.querySelectorAll('*').forEach(element => {
            element.getAttributeNames().forEach(name => {

                otherAttributes.forEach(attr => {
                    if(name == attr){
                        element.removeAttribute(attr);
                    }
                });
                if (name.startsWith(':')) {
                    element.removeAttribute(name);
                }
            });
        });

        let cleanedHtml = doc;
        // let cleanedHtml = doc.body.innerHTML;
    
        // Use regex to replace all instances of the base URL with a leading slash
        // const regex = new RegExp(window.builderObject.baseUrl.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
        // cleanedHtml = cleanedHtml.replace(regex, '/');
        return cleanedHtml;
    },
    selectRandomArray(arrays) {
        const randomIndex = Math.floor(Math.random() * arrays.length);
        return arrays[randomIndex];
    },
    shuffleArray(array) {
        const shuffledArray = array.slice();
        for (let i = shuffledArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffledArray[i], shuffledArray[j]] = [shuffledArray[j], shuffledArray[i]];
        }
        return shuffledArray;
    },
    getTwoRandomValues(array) {
        const randomIndex1 = Math.floor(Math.random() * array.length);
        let randomIndex2 = Math.floor(Math.random() * array.length);
        
       // Ensure randomIndex2 is different from randomIndex1
        while (randomIndex2 === randomIndex1) {
            randomIndex2 = Math.floor(Math.random() * array.length);
        }
    
        return [array[randomIndex1], array[randomIndex2]];
    },
    waitForElm(selector) {
        return new Promise(resolve => {
            if (document.querySelector(selector)) {
                return resolve(document.querySelector(selector));
            }
    
            const observer = new MutationObserver(mutations => {
                if (document.querySelector(selector)) {
                    observer.disconnect();
                    resolve(document.querySelector(selector));
                }
            });
    
            // If you get "parameter 1 is not of type 'Node'" error, see https://stackoverflow.com/a/77855838/492336
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    },
    generateAi(section, content){
        this.waitForElm('.builder--page').then((elm) => {
            setTimeout(() => {
                let $el = document.querySelector('[data-id="'+ section.uuid +'"]');
                let $alpine = Alpine.$data($el.querySelector('.wire-section'));
                $alpine.regenerateAi(content);
             }, 500);
        });
    },
    insertAndReposition(array, newPosition, newItem, page) {
        // Find the index where the new item should be inserted
        const index = array.findIndex(item => item.position >= newPosition);
        let newArray = [];
        array.forEach((item, index) => {
            if(item.page_id == page.uuid){
                newArray.push(item);
            }
        });
    

        for (let i = index + 1; i < array.length; i++) {

            // console.log(array[i - 1].position);
            // array[i].position = array[i - 1].position + 1;
        }
        return;
        if (index === -1) {
            // If position does not exist, add at the end
            array.push({ ...newItem, position: array.length + 1 });
        } else {
            // Insert new item
            array.splice(index, 0, { ...newItem, position: newPosition });
    
            // Reassign positions for all following items
            for (let i = index + 1; i < array.length; i++) {
                array[i].position = array[i - 1].position + 1;
            }
        }

        // console.log(newArray, index)

        return array;
    },

    linker(link){
        let _link = link;
        if(link && link !== '/' && link.startsWith('/')){
            _link = link.substring(1);
        }

        if(!link) {
            _link = 'javascript:void(0)';
        }

        return _link;
    },

    linkTarget(site, link){

        return '_self';
    },

    rescaleDiv(scale__section){
       let $this = this;
       var he = scale__section;


       var objects = {
          getDeviceWidth() {
             var de = he == null ? void 0 : he.querySelector(".page-type-item");
             var me = de == null ? void 0 : de.clientWidth;
            //  console.log(me, window.innerWidth, de.clientWidth);

             var scale = me / window.innerWidth;

             return scale;
          },
          updateAllScale(scale){
             he.querySelectorAll('.edit-board').forEach((el) => {
                el.style.transform = 'scale(' + scale + ')';
             })
          },
       };
       
       setTimeout(() => {
            objects.updateAllScale(objects.getDeviceWidth());
       }, 200);
       
       window.addEventListener('resize', function(){
          let scale = objects.getDeviceWidth();
          objects.updateAllScale(scale);
       });
    },

    generateSiteLink(site){
        let link = window.builderObject.currentBaseUrl +'/'+ window.builderObject.sitePrefix + site.address;


        return link;
    },

    getRandomHexColor() {
        // Generate random R, G, and B values
        var r = Math.floor(Math.random() * 256);
        var g = Math.floor(Math.random() * 256);
        var b = Math.floor(Math.random() * 256);
    
        // Convert decimal to hexadecimal
        var hexR = r.toString(16).padStart(2, '0');
        var hexG = g.toString(16).padStart(2, '0');
        var hexB = b.toString(16).padStart(2, '0');
    
        // Concatenate the hexadecimal values
        var hexColor = '#' + hexR + hexG + hexB;
    
        return hexColor;
    },
    countTotalLetters(str) {
        // Initialize count to 0
        var totalCount = 0;
    
        // Loop through the string
        for (var i = 0; i < str.length; i++) {
            var char = str.charAt(i);
    
            // Check if the character is a letter
            if (/[a-zA-Z]/.test(char)) {
                // Increment the count
                totalCount++;
            }
        }
    
        return totalCount;
    },

    generatePageSections($sections){
        let $this = this;
        let $getSections = [];
        let count = $sections.length + 1;



        $sections.forEach((item, index) => {
            let $items = [];
            let $sectionUUID = $this.generateUUID();
            let $sectionID = window.md5(item.section + $sectionUUID).toString();

            // console.log($sectionID)
    
            if(item.items.length > 0){
                item.items.forEach((_i, index) => {
                    let $itemUUID = $this.generateUUID();
                    let $itemID = window.md5(item.section + $sectionUUID + $itemUUID).toString();

                    let $newItem = {
                        id: $itemID,
                        uuid:  $itemID,
                        ..._i
                    };
    
                    $items.push($newItem);
                });

                item.items = $items;
            }

            let $new = {
                id: $sectionID,
                uuid: $sectionUUID,
                published: 1,
                position: count,
                settings: {
                 silence: 'golden',
                },
                form: {
                 email: 'Email',
                 button_name: 'Signup',
                },
                ...item,
                section_settings: {
                 height: 'fit',
                 width: 'fill',
                 spacing: 'l',
                 ...item.section_settings
                },
            };
            $getSections.push($new);
        });


        // console.log($getSections, $sections);

        return $getSections;
    },
    
    generalSortable($wrapper, $options = {}, $template, $array, $callback){


        window.Sortable.create($wrapper, {
            ...this.sortableOptions,
            ...$options,
            onEnd: (event) => {
               let steps = Alpine.raw(window._.sortBy($array, 'position'))
               let moved_step = steps.splice(event.oldIndex, 1)[0]
               steps.splice(event.newIndex, 0, moved_step)
               
               // HACK update prevKeys to new sort order
               let keys = []
               steps.forEach((step, i) => {
                  keys.push(step.uuid);

                  $array.forEach((x, _i) => {
                     if(x.uuid == step.uuid) x.position = i;
                  });
               });

               $template._x_prevKeys = keys;

               $callback();
            },
         });
    },
    
    changeSavingState(state){
        //this.savingState = 
    },

    createSection(){

    },
    getBase64Images(htmlString) {
        let $this = this;
        // Create a new DOM parser
        const parser = new DOMParser();
        // Parse the HTML string into a document
        const doc = parser.parseFromString(htmlString, 'text/html');
        // Get all image elements
        const images = doc.querySelectorAll('img');
    
        // Function to convert image to base64
        function toBase64(img) {
            return new Promise((resolve, reject) => {
                let canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                let ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                
                resolve(canvas.toDataURL());
            });
        }
    
        // Convert all images to base64
        return Promise.all(Array.from(images).map(img => {
            return new Promise((resolve, reject) => {
                let src = img.getAttribute('src');
                if(src !== null && src.includes('media/site/images') || src !== null && src.includes('assets/image')){
                    // Create a new image element
                    const newImg = new Image();
                    newImg.crossOrigin = 'Anonymous'; // To avoid CORS issues
                    newImg.onload = () => {
                        toBase64(newImg).then(base64 => {
                            resolve({ src, base64 });
                        }).catch(reject);
                    };
                    newImg.onerror = reject;
                    newImg.src = src;
                }
            });
        }));
    },
    generateUUID() {
        var d = new Date().getTime();//Timestamp
        var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now()*1000)) || 0;//Time in microseconds since page-load or 0 if unsupported
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16;//random number between 0 and 16
            if(d > 0){//Use timestamp until depleted
                r = (d + r)%16 | 0;
                d = Math.floor(d/16);
            } else {//Use microseconds since page-load if supported
                r = (d2 + r)%16 | 0;
                d2 = Math.floor(d2/16);
            }
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    },
    isValidUrl(urlString) {
        var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
      '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator


        return !!urlPattern.test(urlString);
    },
    gs(media){
        var _url = window.builderObject.baseUrl;

        return _url + '/' + media;
    },
    getMedia(media){
        if(!media) return;

        if(media.includes('://') && this.isValidUrl(media)) return media;

        var _url = window.builderObject.mediaUrl;

        return _url + '/' + media;
    },
    generateBodyFont(site){
        if(site.settings.fontSettings === undefined) return;
        var element = document.head.querySelector('[id="google-site-font-body"]');
        if(element) element.remove();

        var href = `https://fonts.googleapis.com/css?family=${site.settings.fontName}:${site.settings.fontSettings.variants}`;
        var styles = document.createElement('link');
        styles.rel = 'stylesheet';
        styles.type = 'text/css';
        styles.href = href;
        styles.id = 'google-site-font-body';
        document.getElementsByTagName('head')[0].appendChild(styles);
    },
    generateHeadFont(site){
        if(site.settings.fontHeadSettings === undefined) return;
        var element = document.head.querySelector('[id="google-site-font-head"]');
        if(element) element.remove();

        var href = `https://fonts.googleapis.com/css?family=${site.settings.fontHeadName}:${site.settings.fontHeadSettings.variants}`;
        var styles = document.createElement('link');
        styles.rel = 'stylesheet';
        styles.type = 'text/css';
        styles.href = href;
        styles.id = 'google-site-font-head';
        document.getElementsByTagName('head')[0].appendChild(styles);
    },
    generateSiteDesign(site){
        this.generateBodyFont(site);
        this.generateHeadFont(site);
        var shape = '', min_shape = '', sublink_shape = '';

        var background_color = site.settings.color;
        var contrast_color = this.getContrastColor(background_color);


        if(site.settings.corner == 'straight'){
            shape = 'var(--r-none)';
            min_shape = 'var(--min-r-none)';
            sublink_shape = 'var(--min-shape)';
        }
        if(site.settings.corner == 'round'){
            shape = 'var(--r-small)';
            min_shape = 'var(--min-r-small)';
            sublink_shape = 'var(--min-shape)';
        }
        if(site.settings.corner == 'rounded'){
            shape = 'var(--r-full)';
            min_shape = 'var(--min-r-full)';
            sublink_shape = 'calc(var(--min-shape) / 2)';
        }



        return {
            '--site-width': (site.settings.page_width ? site.settings.page_width : 1200) + 'px',
            '--accent': '#' + background_color,
            '--shape': shape,
            '--min-shape': min_shape,
            '--sublinks-shape': sublink_shape,
            '--contrast-color': contrast_color,
            '--design-headFont': site.settings.fontHeadName,
            '--design-bodyFont': site.settings.fontName,
            '--logo-height': site.header.logo_width + 'px',
        }
    },
    getContrastColor(hexColor, bool = false) {
        if(!hexColor) return '#000000';

        if(!hexColor.startsWith('#')) hexColor = '#' + hexColor;
        // Convert hex color to RGB
        var r = parseInt(hexColor.substr(1, 2), 16);
        var g = parseInt(hexColor.substr(3, 2), 16);
        var b = parseInt(hexColor.substr(5, 2), 16);
    
        // Calculate luminance
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

        if(bool){
            if(luminance > 0.5){
                return true;
            }else{
                return false;
            }
        }
    
        // Choose black or white based on luminance
        return luminance > 0.5 ? "#000000" : "#ffffff";
    },
    // getContrastColor(hexColor) {
    //     if(hexColor === undefined || hexColor == null) return;
    //     // hexColor RGB
    //     var R1 = parseInt(hexColor.substring(1, 3), 16);
    //     var G1 = parseInt(hexColor.substring(3, 5), 16);
    //     var B1 = parseInt(hexColor.substring(5, 7), 16);
    
    //     // Black RGB
    //     var blackColor = "#000000";
    //     var R2BlackColor = parseInt(blackColor.substring(1, 3), 16);
    //     var G2BlackColor = parseInt(blackColor.substring(3, 5), 16);
    //     var B2BlackColor = parseInt(blackColor.substring(5, 7), 16);
    
    //     // Calc contrast ratio
    //     var L1 = 0.2126 * Math.pow(R1 / 255, 2.2) +
    //              0.7152 * Math.pow(G1 / 255, 2.2) +
    //              0.0722 * Math.pow(B1 / 255, 2.2);
    
    //     var L2 = 0.2126 * Math.pow(R2BlackColor / 255, 2.2) +
    //              0.7152 * Math.pow(G2BlackColor / 255, 2.2) +
    //              0.0722 * Math.pow(B2BlackColor / 255, 2.2);
    
    //     var contrastRatio = 0;
    //     if (L1 > L2) {
    //         contrastRatio = Math.floor((L1 + 0.05) / (L2 + 0.05));
    //     } else {
    //         contrastRatio = Math.floor((L2 + 0.05) / (L1 + 0.05));
    //     }
    
    //     // If contrast is more than 5, return black color
    //     if (contrastRatio > 5) {
    //         return '#000000';
    //     } else { 
    //         // if not, return white color.
    //         return '#FFFFFF';
    //     }
    // },
    detectMobile() {
        return ( ( window.innerWidth <= 800 ) );
    },
    generateSectionClass(site, section){
        var $this = this;
        var bg_transparent_fit = function(){
            if(window.dark_theme || !section.section){
                return false;
            }

            var matches = section.section.match( /accordion|banner|text|list/g );
            if(!matches && section.section_settings.width == 'fit'){
                return true;
            }

            return false;
        };

        var light_color = function(){
            let $color = false;

            if(section.section_settings.color == 'accent'){
                if($this.getContrastColor(site.settings.color) == '#000000'){
                    $color = false;
                }

                if($this.getContrastColor(site.settings.color) == '#ffffff'){
                    $color = true;
                }
            }

            // console.log(section.section_settings)
            if(section.section_settings.color == 'default'){
                $color = false;
            }

            if(section.section_settings.image && section.section_settings.text_color == 'light'){
                $color = true;
            }

            return $color;
        };

        var dark_color = function(){
            let $color = false;
            // if(section.section_settings.color == 'default') return false;

            if(section.section_settings.color == 'accent' && !section.section_settings.image){
                if($this.getContrastColor(site.settings.color) == '#000000'){
                    $color = true;
                }
            }

            if(section.section_settings.image && section.section_settings.text_color == 'dark'){
                $color = true;
            }

            return $color;
        };

        var object = {
            '-light-color': light_color(),
            '-dark-color': dark_color(),

            // 'section-bg-wrapper': !window.dark_theme,

            'section-height-fill': section.section_settings.height == 'fill',
            'section-height-fit': section.section_settings.height == 'fit',

            'section-width-fill': section.section_settings.width == 'fill',

            'section-width-fit': section.section_settings.width == 'fit',
            'min-shape': section.section_settings.width == 'fit',
            '!bg-transparent': bg_transparent_fit(),
            
            'align-items-start': section.section_settings.align == 'top',
            'align-items-center': section.section_settings.align == 'center',
            'align-items-end': section.section_settings.align == 'bottom',

            'grey': section.section_settings.color == 'default',
            'accent': section.section_settings.color == 'accent',


            '[--spacingTB:var(--s-1)]': section.section_settings.spacing == 's',
            '[--spacingLR:var(--s-1)]': section.section_settings.spacing == 's',

            '[--spacingTB:calc(var(--unit)_*_2)]': section.section_settings.spacing == 'm',
            '[--spacingLR:calc(var(--unit)_*_2)]': section.section_settings.spacing == 'm',

            '[--spacingTB:calc(var(--unit)_*_5)]': section.section_settings.spacing == 'l',
            '[--spacingLR:calc(var(--unit)_*_5)]': section.section_settings.spacing == 'l',

            '[--spacingTB:calc(var(--unit)_*_8)]': section.section_settings.spacing == 'xl',
            '[--spacingLR:calc(var(--unit)_*_8)]': section.section_settings.spacing == 'xl',

            '[--bg-accent-color:--accent]': section.section_settings.color == 'accent',
            '[--bg-color:--c-mix-1]': section.section_settings.color == 'default',

            'media': section.section_settings.image,
            'image-selected': section.section_settings.image,

            '[--bg-grayscale:0%]': !section.section_settings.greyscale,
            '[--bg-grayscale:100%]': section.section_settings.greyscale,

            '[--bg-image:var(--section-image)]': section.section_settings.image,
            '[--background-upper:center]': true,
            '[--background-bottom:center]': true,


            // BLur
            '[--bg-blurscale:1.1]':true,
            '[--bg-blur:0px]': !section.section_settings.blur,
            '[--bg-blur:5px]': section.section_settings.blur && section.section_settings.blur_size == 's',
            '[--bg-blur:20px]': section.section_settings.blur && section.section_settings.blur_size == 'm',
            '[--bg-blur:40px]': section.section_settings.blur && section.section_settings.blur_size == 'l',

            // Overlay
            
            '[--bg-opacity:1]': !section.section_settings.overlay,
            '[--bg-opacity:0.75]': section.section_settings.color !== 'transparent' && section.section_settings.overlay && section.section_settings.overlay_size == 's',
            '[--bg-opacity:0.5]': section.section_settings.color !== 'transparent' && section.section_settings.overlay && section.section_settings.overlay_size == 'm',
            '[--bg-opacity:0.25]': section.section_settings.color !== 'transparent' && section.section_settings.overlay && section.section_settings.overlay_size == 'l',
            'floating-header': section.first_section,
        };


        return object;
    },
    generateSectionStyles(section){
        
        return object;
    }
});

Alpine.data("templateIframeResize", () => ({
    _re(){
        const container = this.$root.querySelector('.iframe-container');
        const iframe = this.$root.querySelector('iframe');

        const containerWidth = container.clientWidth;
        const desktopWidth = 1280; // Set the desired desktop width

        const scale = containerWidth / desktopWidth;

        // console.log(containerWidth, scale)

        iframe.style.width = `${desktopWidth}px`;
        iframe.style.height = `${800}px`; // Set the desired desktop height
        iframe.style.transform = `scale(${scale})`;
    },

    init(){
        let $this = this;
        

        $this._re();
        
        window.addEventListener('resize', function(e){
            $this._re();
        });
    }
}));

Alpine.data("appData", () => ({
    
    init(){
        let $this = this;

        document.addEventListener('keydown', function(e){

            if (e.keyCode == 75 && e.ctrlKey){
                $this.$dispatch('open-modal', 'search-sites-modal');
                e.preventDefault();
                return false;
            }
        });

    }
}));

Alpine.data("sidebarMenu", () => ({
    
    queryLinks(){
        const menus = this.$root.querySelectorAll('.sidebar-item');
        menus.forEach(el => {
          var getUrl = window.location.href;

          const url = el.getAttribute('href');
      
          el.classList.remove('active');
          if(getUrl == url) el.classList.add('active');
      
          el.addEventListener('click', (e) => {
            menus.forEach(box => {
              box.classList.remove('active');
            });
      
            el.classList.add('active');
          });
        });
    },

    init(){
        let $this = this;
        $this.queryLinks();



        document.addEventListener('alpine:navigated', (e) => {
            
            $this.$nextTick(function(){
                $this.queryLinks();
            })
          
            //console.log(e.target.URL)
        });
    }
}));

Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
    // Runs after commit payloads are compiled, but before a network request is sent...
 
    respond(({ status, response }) => {
        // Runs when the response is received...
        // "response" is the raw HTTP response object
        // before await response.text() is run...
    })
 
    succeed(({ status, json }) => {
        callYenaImport();
    })
 
    fail(({ status, content, preventDefault }) => {
        // Runs when the response has an error status code...
        // "preventDefault" allows you to disable Livewire's
        // default error handling...
        // "content" is the raw response content...
    })
});
Alpine.plugin(collapse)
Alpine.plugin(Sections)
// Alpine.plugin(component)
Alpine.plugin(masonry)
Alpine.plugin(
    Tooltip.defaultProps({
        onShown(instance){
            instance.popper.querySelectorAll('a').forEach(e => {
                e.addEventListener('click', (event) => {
                    instance.hide();
                });
            });
        },
        onShow(instance) {
            // console.log(instance)
            window.addEventListener('hideTippy', (event) => {
                instance.hide();
            });
        }
    })
);
Alpine.plugin(navigate)
Livewire.start()
// Menu