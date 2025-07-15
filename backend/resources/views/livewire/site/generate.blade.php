<?php
   use App\Models\SitesLinkerTrack;
   use App\Models\ProductReview;
   use App\Models\Product;
   use App\Models\CoursesEnrollment;
   use App\Models\CoursesLesson;
   use App\Models\CoursesReview;
   use App\Models\Course;
   use function Livewire\Volt\{state, mount, on};

   state([
       'site',
       'domain'
   ]);

   state([
       'siteArray' => [],
       'products' => [],
       'courses' => [],
       'sections' => [],
       'pages' => [],
       'headerLinks' => [],
       'footerGroups' => [],
       'planFeatures' => fn() => $this->site->user()->first()->planJsFeatures(),
   ]);
   on([
     'saveLinker' => function($link = null){
        $this->skipRender();

        if(!$link) return;
        $track = new SitesLinkerTrack;

        $track->track($link, $this->site->id);
     }
   ]);
   mount(function(){
      $this->getSections();
      $this->getPages();
      $this->getHeaderLinks();
      $this->getFooterGroups();
      $this->getCourses();
      $this->getProducts();

    //   dd($this->site->user()->first()->planJsFeatures());

      $this->siteArray = $this->site->toArray();
   });
   
   $saveLinker = function($link = null){
    $this->skipRender();

    if(!$link) return;
    $track = new SitesLinkerTrack;

    $track->track($link, $this->site->id);
   };
   $getProducts = function(){
      $this->products = Product::where('user_id', $this->site->user_id)->get()->map(function($item) {
         $avgRating = ProductReview::where('product_id', $item->id)->avg('rating');

         $item->route = route('out-products-single-page', [
            'slug' => $item->slug,
            'redirect' => $this->site->getAddress()
         ]);

         $item->variants = $item->variant()->where('type', 'color')->get()->toArray();
         $item->avg_rating = number_format($avgRating, 1);
         $item->featured_image = $item->getFeaturedImage();
         $item->price_html = $item->getPrice();

         // Add tag "new" if the product was recently created
         if ($item->created_at >= now()->subDays(5)) {
            $item->tag = 'new';
         }

         // Add tag "hot" if the product has the highest rating
         $highestRatedProduct = Product::withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'DESC')
            ->first();

         if ($highestRatedProduct && $item->id == $highestRatedProduct->id) {
            $item->tag = 'hot';
         }


         return $item;
      })->toArray();
   };

   $getCourses = function(){
      $this->courses = Course::where('user_id', $this->site->user_id)->get()->map(function($item) {
         $avgRating = CoursesReview::where('course_id', $item->id)->avg('rating');
         $item->route = !empty($item->slug) ? route('out-courses-page', [
            'slug' => $item->slug,
            'redirect' => $this->site->getAddress()
         ]) : null;
         $item->avg_rating = number_format($avgRating, 1);
         $item->featured_image = $item->_get_featured_image();
         $item->price_html = $item->getPrice();

         $item->students = CoursesEnrollment::where('course_id', $item->id)->count();
         $item->lessons = CoursesLesson::where('course_id', $item->id)->count();

         $user = $item->user()->first();
         $item->userName = $user->name;
         $item->userAvatar = $user->getAvatar();


         return $item;
      })->toArray();
   };
   // Methods
   $getSections = function(){
    $this->sections = $this->site->sections()->get()->map(function($item){
      $config = $item->getConfig();


      $item->get_media = $item->getMedia();
      $item->editComponent = ao($config, 'components.editComponent');
      //$page_id = "sectionComponent:$item->id";

      return $item;
    })->toArray();
   };

   $getPages = function(){
      $this->pages = $this->site->pages()->orderBy('id', 'asc')->get()->toArray();
   };

   $getHeaderLinks = function(){
      $this->headerLinks = $this->site->header_links()->where('parent_id', '=', null)->get()->toArray();
   };

   $getFooterGroups = function(){
      $this->footerGroups = $this->site->footer_groups()->get()->toArray();
   };

?>
<div>


    <div x-data="sandard_buildout_page">
        <livewire:components.builder.generate :$site :key="uukey('builder', 'generatesite::index')" />
    </div>


    @script
        <script>
            Alpine.data('sandard_buildout_page', () => {
                return {
                  renderView: 'normal',
                  renderMobile: false,
                  domain:        @entangle('domain'),
                  site:          @entangle('siteArray'),
                  pages:         @entangle('pages'),
                  sections:      @entangle('sections'),
                  products:     @entangle('products'),
                  courses:      @entangle('courses'),
                  socials:       {!! collect(socials())->toJson() !!},
                  siteheader:{
                     links: @entangle('headerLinks')
                  },
                  planFeatures: @entangle('planFeatures'),
                  footerGroups: @entangle('footerGroups'),
                  current_viewing_page: null,

                  currentSections: [],
                  router: null,

                  getProduct(item_id){

                     let product = [];

                     this.products.forEach(item => {
                        if(item.id == item_id){
                           product = item;
                        }
                     });
                     return product;
                  },
                  getCourse(item_id){
                     let course = [];

                     this.courses.forEach(item => {
                        if(item.id == item_id){
                           course = item;
                        }
                     });
                     return course;
                  },
                  changePage(page){
                    this.current_viewing_page = page.uuid;
                  },
                  __o_feature(code){
                     let feature = false;
                     this.planFeatures.forEach((e) => {
                        if(e.code == code){
                           feature = e.type == 'limit' ? e.limit : e.enable;
                        }
                     });

                     return feature;
                  },
                  defaultPage: function(){
                      var page = this.pages[0];

                      this.pages.forEach((e, index) => {
                        if(e.default) page = e;
                      });

                      return page;
                  },
                  currentPage: function(){
                      var page = this.defaultPage();

                      this.pages.forEach((e, index) => {
                        if(e.uuid == this.current_viewing_page) page = e;
                      });

                      return page;
                  },
                  onVisible(element, callback) {
                    new IntersectionObserver((entries, observer) => {
                        entries.forEach(entry => {
                        if(entry.intersectionRatio > 0) {
                            callback(element);
                            observer.disconnect();
                        }
                        });
                    }).observe(element);
                    if(!callback) return new Promise(r => callback=r);
                  },

                  isAppended(element){
                    while (element.parentNode)
                        element = element.parentNode;
                    return element instanceof Document;
                  },

                  onceAppended(element, callback) {
                    if (this.isAppended(element)) {
                        callback();
                        return;
                    }

                    const MutationObserver =
                        window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

                    if (!MutationObserver)
                        return useDeprecatedMethod(element, callback);

                    const observer = new MutationObserver((mutations) => {
                        if (mutations[0].addedNodes.length === 0)
                            return;
                        if (Array.prototype.indexOf.call(mutations[0].addedNodes, element) === -1)
                            return;
                        observer.disconnect();
                        callback();
                    });

                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });

                  },
                  resolveLinks(){

                  },
                  resolvePage(page){
                    let $this = this;

                    return new Promise((resolve, reject) => {

                        $this.current_viewing_page = page.uuid;
                        var sections = [];

                        this.sections.forEach((element, index) => {
                            if($this.currentPage().uuid == element.page_id){
                                sections.push(element);
                            }
                        });

                        sections = window._.sortBy(sections, 'position');
                            
                        sections.forEach((element, index) => {
                            if($this.site.header.sticky || $this.site.header._float){
                            if(index == 0){
                                element.first_section = true;
                            }
                            }
                        });
                        $this.currentSections = sections;
                        setTimeout(() => {
                            let footer = document.querySelector('.yena-footer');
                            if(footer){
                                footer.classList.remove('hidden');
                            }
                            resolve();
                        }, 100);
                    });
                  },
                  queryAllLinks(){
                    let $this = this;
                    document.querySelectorAll('[\\:link]').forEach(element => {
                        let href = element.getAttribute(':link');

                        // console.log(href)

                        // if(!$this.$store.builder.isValidUrl(href)){
                        //     element.classList.add('yena-site-link');
                        // }
                    });
                    // document.querySelectorAll('a').forEach(element => {
                    //     let href = element.getAttribute('href');

                    //     if(!$this.$store.builder.isValidUrl(href)){
                    //         element.classList.add('yena-site-link');
                    //     }
                    // });
                    // $this.router.updatePageLinks();
                  },
                  __save_linker(link){
                    this.$wire.saveLinker(link);
                  },
                  __section_loaded($el){
                    let $this = this;
                    $el.querySelectorAll('.screen').forEach((e) => {
                        e.remove();
                    });
                    if($this.router){
                        $this.router.updatePageLinks();
                    }
                  },
                  init(){
                    let $this = this;

                    let getPages = {
                        '/': {
                            uses: () => {
                                $this.resolvePage($this.defaultPage()).then((e) => {

                                });
                                // $this.current_viewing_page = $this.defaultPage().uuid;
                            }
                        }
                    };

                    this.pages.forEach((e, index) => {
                        getPages[`/${e.slug}`] = {
                            uses: () => {
                                $this.resolvePage(e).then((e) => {
                                    
                                });
                                $this.changePage(e);
                            }
                        };
                    });

                    if(!window.initNavigate){
                        $this.resolvePage($this.defaultPage());
                    }

                    if(window.initNavigate){
                        let url = $this.domain ? '/' : window.initNavigate;
                        let router = new Navigo(url, {
                            hash: false,
                            linksSelector: ".yena-site-link",
                            strategy: 'ONE'
                        });
                        
                        router.hooks({
                            leave(done, match){
                                done();
                            },
                            before(done, match) {
                                let footer = document.querySelector('.yena-footer');
                                if(footer){
                                    footer.classList.add('hidden');
                                }




                                // setTimeout(() => {
                                //     // router.updatePageLinks();
                                // }, 300);

                                done();
                            }
                        });
                        router.on(getPages);
                        router.resolve();
                        setTimeout(() => {
                            router.updatePageLinks();
                        }, 500);

                        $this.router = router;
                    }
                  }
                }
            });
      </script>
    @endscript
</div>