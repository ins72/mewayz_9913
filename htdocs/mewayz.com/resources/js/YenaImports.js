import tippy from 'tippy.js';
import LazyLoad from "vanilla-lazyload";
import ApexCharts from 'apexcharts'

const yena = {


    __staticImports: {

    },

    __livewireImports: {

        /*__sidebarMenu(){

          const menus = document.querySelectorAll('.yena-sidebar .sidebar-item');

          var menuFn = function(pageUrl = null){
            menus.forEach(el => {
              var getUrl = window.location;
              if(pageUrl) getUrl = pageUrl.target.URL;
              //console.log(getUrl)


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
          };

          menuFn();

          document.addEventListener('livewire:navigated', (e) => {
            menuFn(e);

            //console.log(e.target.URL)
          });
        },*/

        apexChart(){
          var blue = '#A0D7E7';
          var blueLight = '#0e97b5';
          var purple = '#6C5DD3';
          var white = '#ffffff';
          var blueOpacity = '#e6efff';
          var blueLight = '#50B5FF';
          var pink = '#FFB7F5';
          var orangeOpacity = '#fff5ed';
          var yellow = '#FFCE73';
          var green = '#7FBA7A';
          var red = '#FF754C';
          var greenOpacity = '#ecfbf5';
          var gray = '#808191';
          var grayOpacity = '#f2f2f2';
          var grayLight = '#E2E2EA';
          var borderColor = "#E4E4E4";

          document.querySelectorAll('.chart-line').forEach(function(chartElement) {
            // Options
            var _self_id = chartElement.getAttribute('data-chart');
            var _get_data = eval(_self_id);
        
            var options = {
                labels: _get_data.labels,
                series: _get_data.series,
                colors: _get_data.colors ? _get_data.colors : [grayOpacity, blue],
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
            };

            new ApexCharts(chartElement, options).render();
          });
        },


        dataHover(){
          const elements = document.querySelectorAll(['[class*="data-[hover=true]"]']);
          const hoverElement = function (el){
            el.addEventListener('mouseenter', () => {
              el.setAttribute('data-hover', 'true');
            });
            el.addEventListener('mouseleave', () => {
              el.removeAttribute('data-hover');
            });
          }

          elements.forEach(item => {
            hoverElement(item);
          });
        },
        
        zMenu(){

            const init_z_menu = () => {
              document.querySelectorAll('.z-menuc').forEach((menu) => {
                let handle = menu;
                let placement = 'bottom-start';
                let classes = null;
                let useElWidth = false;
            
                if (menu.dataset['zClass'] !== undefined) {
                  classes = menu.dataset['zClass'];
                }
            
                if (menu.dataset['handle'] !== undefined) {
                  handle = menu.querySelector(menu.dataset['handle']);
                }
            
                if (menu.dataset['placement'] !== undefined) {
                  placement = menu.dataset['placement'];
                }
            
                let content = 'Nothing here';
            
                if (menu.querySelector('.z-menuc-content-temp')) {
                  content = menu.querySelector('.z-menuc-content-temp').firstElementChild;
                }
            
                let width = menu.dataset['maxWidth'] !== undefined ? parseInt(menu.dataset['maxWidth']) : 137;
                let options = {};
            
                if (menu.dataset['appendsSelf'] === undefined) {
                  options['appendTo'] = document.body;
                }
            
                if (menu.dataset['appendsTo'] !== undefined) {
                  options['appendTo'] = menu.querySelector(menu.dataset['appendsTo']);
                }
            
                if (menu.dataset['appendsOut'] !== undefined) {
                  options['appendTo'] = document.querySelector(menu.dataset['appendsOut']);
                }
                
                if (menu.dataset['useHandleWidth'] !== undefined) {
                  useElWidth = true;
                }
            
                //console.log(width);
                let instance = tippy(handle, {
                  ...options,
                  content: content,
                  allowHTML: true,
                  placement: placement,
                  theme: 'zmenuc',
                  interactive: true,
                  trigger: 'click',
                  arrow: false,
                  maxWidth: width,
                  animation: 'scale',
            
                  popperOptions: {
                    strategy: 'fixed',
                    modifiers: [
                      {
                        name: "sameWidth",
                        enabled: useElWidth,
                        fn: ({ state }) => {
                           state.styles.popper.width = `${state.rects.reference.width}px`;
                        },
                        phase: "beforeWrite",
                        requires: ["computeStyles"],
                        effect: ({ state }) => {
                          state.elements.popper.style.width = `${state.elements.reference.clientWidth}px`;
                        }
                      }
                    ]
                  },
                  onHidden(instance) {},
                  onShow(instance) {
                    let _tippyDoc = instance.popper;
                    if (classes) {
                      instance.popper.querySelector('.tippy-box').className += ' ' + classes;
                    }
            
                    if (menu.dataset['wireUpdates'] !== undefined) {
                      Livewire.on('javascriptRefreshEvent', (data) => {
                        let newContent = menu.querySelector('.z-menuc-content-temp').firstElementChild;
            
                        if (newContent) {
                          instance.setContent(newContent);
                        }
                      });
            
                      Livewire.on('javascriptHideTippy', (data) => {
                        instance.hide();
                      });
                    }
            
                    let close = instance.popper.querySelector('.z-menu-close');
                    if (close) {
                      close.addEventListener('click', (e) => {
                        instance.hide();
                      });
                    }
                  },
                });
              });
            };
            
            if (window.livewire !== undefined) {
              window.livewire.on('javascriptReinitTippys', (data) => {
                init_z_menu();
              });
            }
            
            init_z_menu();
        },

        lazyLoad(){
          const lazy = new LazyLoad({
            /* other options here */
            elements_selector: "[data-src]" // ADD THIS OPTION
          });
        },
    },
};

export default yena