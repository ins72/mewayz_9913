<?php
    use App\Models\ProductReview;
    use App\Models\Product;
    use App\Models\CoursesEnrollment;
    use App\Models\CoursesLesson;
    use App\Models\CoursesReview;
    use App\Models\Course;
    use App\Models\BookingService;
    use function Livewire\Volt\{state, mount};

    state([
        'site',
    ]);

    state([
        'siteArray' => [],
        'sections' => [],
        'addons' => [],
        'pages' => [],
        'story' => [],
        'products' => [],
        'courses' => [],
        'bookingServices' => [],
    ]);

    state([
        'currency' => fn() => $this->site->user->currency(),
    ]);

    state([    
      'sectionConfig' => fn () => __collect_sectons(),
    ]);

   mount(function(){
      $this->getSections();
      $this->getPages();
      $this->getStory();
      $this->getAddons();
      $this->getCourses();
      $this->getProducts();
      
      $this->getBooking();

      $this->siteArray = $this->site->toArray();
   });
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

   $getBooking = function(){
      $this->bookingServices = BookingService::where('user_id', $this->site->user_id)->get()->map(function($item) {
         $item->route = route('out-booking-service-page', ['slug' => $item->id]);
         $item->price_html = $item->getPrice();
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

   $getAddons = function(){
      $this->addons = $this->site->getAddons()->orderBy('id', 'asc')->get()->toArray();
   };

   $getPages = function(){
      $this->pages = $this->site->pages()->orderBy('id', 'asc')->get()->toArray();
   };

   $getStory = function(){
      $this->story = $this->site->getStory()->orderBy('id', 'asc')->get()->toArray();
   };

?>
<div>
    <div x-data="sandard_buildout_page">
        <livewire:components.bio.index :$site :key="uukey('builder', 'generatesite::index')" />
        <div wire:ignore>
            @if (is_array($s = config("bio.sections")))
            @foreach($s as $key => $item)
                @php
                    if(!$_name = __a($item, 'components.alpineView')) continue;
        
                    $_name = str_replace('/', '.', $_name);
                    $component = "livewire::components.bio.$_name";
        
        
                    $baseName = basename(__a($item, 'components.alpineView'));
        
                    $tag = 'template';
                    $cond = 'bit-component="section-'.$key.'"';
                    
                    // if(str()->startsWith($baseName, '-')) {
                    //    $tag = 'div';
                    //    $cond = 'show';
                    //    $name = str_replace(['-', '+'], '', $baseName);
                    //    $component = "components/bio/sections/$key/$name";
                    // }
        
                @endphp
                <{{ $tag }} {!! $cond !!}>
                    <div wire:ignore>
                        <x-dynamic-component :component="$component"/>
                        {{-- @if(str()->startsWith($baseName, '-'))
                        <livewire:is :component="$component" :key="uukey('f::component', 'component:front-section' . $component)">
                        @else
                        <x-dynamic-component :component="$component"/>
                        @endif --}}
                    </div>
                </{{$tag}}>
                
                @if ($n = __a($item, 'components.alpinePost'))
                    <div class="section-post-{{ $key }}">
                        <livewire:is :component="'components/bio/sections/' . $key . '/' . basename($n)" :redirect="$site->getAddress()" :key="uukey('p::component', 'component:post-section' . $n)">
                    </div>
                @endif
            @endforeach
            @endif
        </div>
        <div wire:ignore>
           @if (is_array($b = config('bio.layout-banners')))
              @foreach ($b as $key => $item)
                 @php
                    $component = "livewire::components.bio.banner.--$key-banner";
                    $livewireComponent = "components.bio.banner.--$key-banner";
                 @endphp
                 <template bit-component="section-banner-{{ $key }}">
                    {{-- <livewire:is :component="$livewireComponent" :$site lazy :key="uukey('builder::index', 'builder\banner\component' . $component)"> --}}
  
                    <x-dynamic-component :component="$component"/>
                 </template>
              @endforeach
           @endif
        </div>
    </div>


    @script
        <script>
            Alpine.data('sandard_buildout_page', () => {
                return {
                  currency:      @entangle('currency'),
                  site:          @entangle('siteArray'),
                  pages:         @entangle('pages'),
                  sections:      @entangle('sections'),
                  sectionConfig: @entangle('sectionConfig'),
                  story:         @entangle('story'),
                  addons:        @entangle('addons'),
                  products:     @entangle('products'),
                  courses:      @entangle('courses'),
                  bookingServices: @entangle('bookingServices'),
                  socials: {!! collect(socials())->toJson() !!},
                  openBooking: false,

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

                  getBookingService(item_id){
                     let booking = [];

                     this.bookingServices.forEach(item => {
                        if(item.id == item_id){
                           booking = item;
                        }
                     });
                     return booking;
                  },
                }
            });
      </script>
    @endscript
</div>