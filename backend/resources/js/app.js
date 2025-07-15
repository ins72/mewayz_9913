import './bootstrap';
// import './moreUtils';
import yena from './YenaImports';
import _, { map } from 'underscore';
import "@phosphor-icons/web/regular";
import "@phosphor-icons/web/fill";
import tippy from "tippy.js"
import Ai from './yenaAi';
import html2canvas from 'html2canvas';
import JSZip from 'jszip';
import { saveAs } from 'file-saver';
import ApexCharts from 'apexcharts'
import Quill from 'quill';
import FlipClock from 'flipclock';
import Swiper from 'swiper/bundle';
import Pickr from '@simonwep/pickr';

window.Pickr = Pickr;
window.Swiper = Swiper;
window.FlipClock = FlipClock;
window.ApexCharts = ApexCharts;

window.saveAs = saveAs
window.html2canvas = html2canvas;

window.Quill = Quill;
window.Ai = Ai;
window.tippy = tippy
window._ = _;
window._navigo = function(route, data){
    console.log(route, data);
};
window.pickrOptions = {
    theme: 'nano', // or 'monolith', or 'nano'

    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    components: {

        // Main components
        preview: false,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: true,
            rgba: true,
            hsla: false,
            hsva: false,
            cmyk: false,
            input: true,
            clear: false,
            save: false
        }
    }
};

window.yenaImport = yena;
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
                // preventDefault()
        })
    })
})
// Initialize livewire imports
const callYenaImport = function () {
    Object.values(yena.__livewireImports).filter(s => typeof s === 'function').forEach(s => s());
}

window.yenaZip = function(){
    return new JSZip;
}
window.callYenaImport = callYenaImport;

//callYenaImport();
document.addEventListener('livewire:initialized', () => {
    callYenaImport();
});

document.addEventListener('alpine:navigated', (e) => {
    //console.log('lmaooo')

    
});
document.querySelectorAll('.app-sidebar [href]').forEach(element => {
    element.addEventListener('click', (e) => {
        element.classList.remove('--active');
    });
});

document.addEventListener('livewire:navigated', (e) => {
    //console.log('lmaooo')
    callYenaImport();
    document.querySelectorAll('.app-sidebar [href]').forEach(element => {
        element.classList.remove('--active');

        setTimeout(() => {
            if(e.target.location.href == element.href) {
                element.classList.add('--active');
            }
        }, 100);
    });


    window.reInitWireTemplate();
});
window.reInitWireTemplate = function() {
    //if(instance === undefined) return;
    //let el = instance.el;
    let elements = document.querySelectorAll('.lazy-load-template');

    elements.forEach(function(el){

        if(el.classList.contains('lazy-load-template')){
            let alreadyExists = [...document.querySelectorAll('body > div')]
            .some(node => el.id && node.id === el.id);
            
            if (alreadyExists) {
                return;
            }
            let script = document.createElement('div');
            script.innerHTML = el.innerHTML;
            if (el.hasAttributes()) {
                for (const attribute of el.attributes) {
                    script.setAttribute(attribute.name, attribute.value);
                }
            }
            
            let body = document.querySelector('body');
            body.appendChild(script);
            //el.remove();
        }

        return;
        if (el.tagName === 'SCRIPT') {
            let alreadyExists = [...document.querySelectorAll('body > script')]
            .some(node => el.id && node.id === el.id);
            
            if (alreadyExists) {
            return;
            }
            let script = document.createElement('script');
            script.innerHTML = el.innerHTML;
            if (el.hasAttributes()) {
            for (const attribute of el.attributes) {
                script.setAttribute(attribute.name, attribute.value);
            }
            }
            
            let body = document.querySelector('body');
            body.appendChild(script);
            el.remove();
        }
    });
};

window.__livewirelazy = function(instance) {
    let el = instance.el;
    let elements = el.querySelectorAll('script');

    elements.forEach(function(el){
        if (el.tagName === 'SCRIPT') {
            let alreadyExists = [...document.querySelectorAll('body > script')]
            .some(node => el.id && node.id === el.id);
            
            if (alreadyExists) {
                return;
            }
            let script = document.createElement('script');
            script.innerHTML = el.innerHTML;
            if (el.hasAttributes()) {
                for (const attribute of el.attributes) {
                    script.setAttribute(attribute.name, attribute.value);
                }
            }
            
            let body = document.querySelector('body');
            body.appendChild(script);
            el.remove();
        }
    });
};

window.updateOrgColor = function(background, color){
    const elements = document.querySelectorAll('.update-current-org--colors');

    elements.forEach(item => {
        item.style.background = background;
        item.style.color = color;
    });
};

window.initComponents = function(){
    document.querySelectorAll('[yena-component]').forEach(component => {
        const componentName = `yena-${component.getAttribute('yena-component')}`;
        var call = document.querySelector(`[${componentName}]`);
        if(!call) return;
    
        call.innerHTML = component.content.cloneNode(true);
        console.log(componentName, call)
    });
    // document.querySelectorAll('[o-component]').forEach(component => {
    //     const componentName = `o-${component.getAttribute('o-component')}`
    
    //     console.log(componentName)
    //     class Component extends HTMLElement {
    //         connectedCallback() {
    //             this.append(component.content.cloneNode(true))
    //         }
    
    //         data() {
    //             const attributes = this.getAttributeNames()
    //             const data = {}
    //             attributes.forEach(attribute => {
    //                 data[attribute] = this.getAttribute(attribute)
    //             })
    //             return data
    //         }
    //     }
    //     customElements.define(componentName, Component)
    // });
};

window.turboNavigate = {
    turboAreaSelector: '#yenaApp',
    applyOverlaySelector: '.turbo',
    overlayClass: 'loading-overlay',
    simpleTurboEnabled: false,

    runNavigateFunction: function(){
        callYenaImport();
    },

    runFunctionInit: function(){
        callYenaImport();
    },
}

window.apexOptions = {

    chart: {
       height: 144,
       type: 'area',
       toolbar: {
        show: false,
       },
       animations: {
       enabled: true,
       easing: 'easeinout',
       speed: 800,
       animateGradually: {
        enabled: true,
        delay: 150
       },
        dynamicAnimation: {
            enabled: true,
            speed: 350
        }
        }
    },
    dataLabels: {
       enabled: false
    },
    legend: {
       show: false
    },
    stroke: {
       curve: 'smooth'
    },
    xaxis: {
       show: false,
       labels: {
       show: false
       },
       axisBorder: {
       show: false
       },
       axisTicks: {
       show: false
       }
    },
    tooltip: {
       x: {
        format: 'dd/MM/yy HH:mm'
       },
    },
}


if(window.zzzconsoleAuth){

    document.addEventListener('navigateTurbo:ready', () => {
        navigateTurbo.init({

           ...window.turboNavigate,
    
          // Routes to work on:
          routes: [
            '/console',
            '/console/sites',
            '/console/settings',
            '/console/upgrade',
            '/console/upgrade/view/{id}',
            '/console/upgrade/success',
            // '/console/trash',
            // '/console/folders/{slug}',
            // '/console/settings/billings',
            // '/console/sites',
          ],
    
          // You can define urls to be prefetched on initial page load:
          prefetch: [
            '/console',
            '/console/sites',
            '/console/settings',
            '/console/upgrade',
            '/upgrade/view/{id}',
            '/upgrade/success',
          ]
        })
    })
}