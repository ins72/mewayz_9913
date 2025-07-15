<?php

use App\Models\BioSite;
use App\Models\MediakitSite;
use App\Models\Page;
use App\Models\User;
use App\QrCode\QRcdrFn;
use InlineSvg\Collection;
use InlineSvg\Transformers\Cleaner;


// if (!function_exists('get_plan_storage')) {
//     function get_plan_storage(){
//         if(!$user = \App\Models\User::find($user)) return false;
//         $total = plan('settings.');
//         $used = get_used_storage($user->id);
//         return round($used/$total * 100, 1);
//     }
// }

if (!function_exists('get_vite_site_resources')) {
    function get_vite_site_resources() {
        if(!file_exists($path = public_path('build/manifest.json'))) return;
        $manifest = file_get_contents($path);
        $manifest = json_decode($manifest, true);


        $data = [];
        foreach(config('yena.viteSiteManifest') as $value){
            if(str()->startsWith($value, '__')){
                $string = str_replace('__', '', $value);

                foreach ($manifest as $key => $v) {
                    if(str()->contains($key, $string)){
                        $data[] = 'build/' . ao($v, 'file');
                    }
                }
            }

            if(!str()->startsWith($value, '__') && array_key_exists($value, $manifest)){
                $data[] = 'build/' . ao($manifest[$value], 'file');
            }
        }

        // dd($manifest);
        return $data;
    }
}

if (!function_exists('logo_branding')) {
    function logo_branding($color = 'light') {
        $image = settings('branding_logo_' . $color);
        $default = gs("assets/image/others/branding-logo-$color.png");
  
        if (empty($image) || !mediaExists('media/site/branding', $image)) {
            return $default;
        }
  
        $image = gs('media/site/branding', $image);
  
        return $image;
    }
}

if (!function_exists('preg_grep_keys_values')) {
    function preg_grep_keys_values($pattern, $input, $flags = 0) {
        return array_merge(
          array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags))),
          preg_grep($pattern, $input, $flags)
       );
    }
}

if (!function_exists('get_plan_storage_bytes')) {
    function get_plan_storage_bytes($site, $readable = false){
        // $type = plan('settings.storage_limit_type');
        $type = 'MB';
        $limit = !empty($storage = __o_feature('consume.upload_storage', $site->user)) ? $storage : config('app.defaultStorage');

        $limit = "$limit{$type}";
        $bytes = convertToBytes($limit);
        if($readable){
            return $limit;
        }

        return $bytes;   
    }
}

if (!function_exists('get_used_storage_percent')) {
    function get_used_storage_percent($site){
        $total = get_plan_storage_bytes($site);
        $used = get_used_storage($site);
        if($total == 0) return '0';
        try {
            return round($used/$total * 100, 1);
        } catch (\Exception $th) {
            return 0;
        }
    }
}

if (!function_exists('get_used_storage')) {
    function get_used_storage($site){

        // $get = \App\Models\SitesUpload::where('site_id', $site->id)->get();
        $sizes = 0;

		$model = $site->uploads()->select(['size'])->get();
		foreach($model as $item){
			$sizes += $item->size;
		}
        return $sizes;

        // foreach ($get as $item) {
        //     $file = basename($item->path);
        //     $directory = dirname($item->path);
        //     $sizes += storageFileSize($directory, $file);
        // }


        // return $sizes;
    }
}

if(!function_exists('convertToBytes')){
    function convertToBytes($from) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        $number = substr($from, 0, -2);
        $suffix = strtoupper(substr($from,-2));
    
        //B or no suffix
        if(is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }
    
        $exponent = array_flip($units)[$suffix] ?? null;
        if($exponent === null) {
            return null;
        }
    
        return $number * (1024 ** $exponent);
    }
}

if(!function_exists('formatBytes')){
    function formatBytes($size, $suffix = false, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array('bytes', 'KB', 'MB', 'GB', 'TB');

            $suffix = $suffix ? ' ' . $suffixes[floor($base)] : '';

            return round(pow(1024, $base - floor($base)), $precision) . $suffix;
        } else {
            return $size;
        }
    }
}

if (!function_exists('__icon')) {
    function __icon($folder, $icon, $class = ''){

        if($folder == '--ie') $folder = 'interface-essential';

        $path = public_path("assets/__icons/$folder");
        $icons = Collection::fromPath($path);
        $icons->addTransformer(new Cleaner());

        //Modify any attribute
        return $icons->get($icon)->withAttribute('class', "yena-svg-icon $class");
    }
}

if(!function_exists('mediakit_in_bio_route')){
    function mediakit_in_bio_route(){
        if(!$user = iam()) return;
        $page = MediakitSite::where('user_id', $user->id)->orderBy('id', 'asc')->first();
        
        if(!$page){
            $generate = new \App\Yena\Mediakit\Generate;
            $page = $generate->setOwner($user)->setName('My new Page')->build();
        }

        return route('console-mediakit-index', ['slug' => $page->_slug]);
    }
}
if(!function_exists('isPreviousMonthPast')){

    function isPreviousMonthPast($date) {
        $givenDate = \Carbon\Carbon::parse($date);
        $previousMonth = $givenDate->subMonthNoOverflow();
        
        $now = \Carbon\Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
    
        // Check if the previous month of the given date is before the current month of the current year
        return $previousMonth->year < $currentYear || ($previousMonth->year == $currentYear && $previousMonth->month < $currentMonth);
    }
}

if(!function_exists('link_in_bio_route')){
    function link_in_bio_route(){
        if(!$user = iam()) return;
        $page = BioSite::where('user_id', $user->id)->orderBy('id', 'asc')->first();
        if($user->_last_edited_bio){
            if($_page = BioSite::where('user_id', $user->id)->where('id', $user->_last_edited_bio)->first()){
                $page = $_page;
            }
        }

        if(!$page){
            $generate = new \App\Yena\Page\Generate;
            $page = $generate->setOwner($user)->setName('My new Page')->build();
        }

        return route('console-bio-index', ['slug' => $page->_slug]);
    }
}

if (!function_exists('__i')) {
    function __i($folder, $icon, $class = ''){
        return __icon($folder, $icon, $class);
    }
}

if (!function_exists('socials')) {
    function socials(){
        $socials = config('yena.socials');
        return $socials;
    }
}

if (!function_exists('validate_date_string')) {
    function validate_date_string($date){
        try {
            $date = (string) $date;
            \Carbon\Carbon::parse($date);
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return false;
        }

        return true;
    }
}
if (!function_exists('url_query')) {
    function url_query($to, array $params = [], array $additional = []) {
        $url = url($to, $additional);

        $params = !empty($params) ? '?' . \Arr::query($params) : '';
        return \Str::finish($url, $params);
    }
}
if (!function_exists('formatNum')) {
    function formatNum($number) {
        // Check if the number is an integer
        if (floor($number) == $number) {
            return number_format($number, 0);
        } else {
            // Format the number to two decimal places
            return number_format($number, 2);
        }
    }
    
}

if (!function_exists('price_with_cur')) {
    function price_with_cur($currency, $price, $delimiter = 1){
        $currency = strtoupper($currency);
        $code = \Currency::symbol($currency);

        $price = (float) $price;
        $price = number_format($price, $delimiter);
        $light = "{$code}{$price}";

        return $light;
    }
}
if(!function_exists('_randAvatar')){
    
    function _randAvatar($key = null, $type = 'bottts-neutral', $extension = 'svg'){


        return "https://api.dicebear.com/6.x/$type/$extension?seed=$key";
    }
}

if (!function_exists('nr')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function nr($n, $nf = false, $precision = 1) {
        if ($nf) {
            return number_format($n);
        }

        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }

      // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
      // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, '', $n_format );
        }

        return $n_format . $suffix;
    }
}

if (!function_exists('__o_feature')) {
    function __o_feature($code = false, $_user = false){
        $user = false;
        if ($_user) {
            if (!$user = $_user) return false;
        }

        if (auth()->check() && !$user) $user = auth()->user();

        if(!$user) return false;
        
        if(!$code) return false;


        if($user->activeSubscription() && !$user->activeSubscription()->plan) return false;
        if($subscription = $user->activeSubscription()){
            $feature = $subscription->features()->code($code)->first();
            if(!$feature) return false;

            return $feature->type == 'limit' ? $feature->limit : $feature->enable;
        }

        return false;
    }
}

if(!function_exists('qrcdr')){
    
    function qrcdr(){
        return QRcdrFn::getInstance();
    }
}

if(!function_exists('bio_site_seo')){
    
    function bio_site_seo($site){
        if(!$site) return;
        $html = '';

        $title = $site->name;
        $description = $site->bio;//$page->bio;
        $image = gs('media/bio/images', $site->logo);//$page->getLogo();
        $block_seo = false;

        // Check for plans
        
        $seo = [
            '<title>'. $title .'</title>',
            '<meta name="description" content="'. $description .'" />',

            '<!-- Twitter Seo Tags -->',
            '<meta property="twitter:type" content="website" />',
            '<meta property="twitter:url" content="'. $site->getAddress() .'" />',
            '<meta property="twitter:title" content="'. $title .'" />',
            '<meta property="twitter:description" content="'. $description .'" />',

            '<!-- Facebook Seo Tags -->',
            '<meta property="og:type" content="website" />',
            '<meta property="og:url" content="'. $site->getAddress() .'" />',
            '<meta property="og:title" content="'. $title .'" />',
            '<meta property="og:description" content="'. $description .'" />',

            '<!-- Google / Search Engine Tags -->',
            '<meta itemprop="name" content="'. $title .'" />',
            '<meta itemprop="description" content="'. $description .'" />',
            
            '<!-- Images -->',
            '<link href="'. $image .'" rel="shortcut icon" type="image/png" />',
            '<meta property="og:image" content="'. $image .'" />',
            '<meta property="twitter:image" content="'. $image .'" />',
            '<meta itemprop="image" content="'. $image .'"/>',
        ];

        // Block SEO
        if ($block_seo) {
            $seo[] = [
                '<!-- Block Seo -->',
                '<meta name="robots" content="noindex">'
            ];
        }


        foreach ($seo as $key => $value) {
            $html .= "\n\n";
            $html .= is_array($value) ? implode(PHP_EOL, $value) : $value;
        }

        return $html;
    }
}
if(!function_exists('site_seo')){
    
    function site_seo($page){
        if(!$page) return;
        $html = '';

        $title = $page->site->name;
        $description = '';//$page->bio;
        $image = gs('media/site/images', $page->site->favicon);//$page->getLogo();
        $block_seo = false;

        if(ao($page->seo, 'title') || ao($page->seo, 'description')){
            $title = !empty(ao($page->seo, 'title')) ? ao($page->seo, 'title') : $title;
            $description = !empty(ao($page->seo, 'description')) ? ao($page->seo, 'description') : $description;

            if (!empty(ao($page->seo, 'image')) && mediaExists('media/site/images', ao($page->seo, 'image'))) $image = gs('media/site/images', ao($page->seo, 'image'));

            $block_seo = ao($page->seo, 'block');
        }

        // Check for plans
        
        $seo = [
            '<title>'. $title .'</title>',
            '<meta name="description" content="'. $description .'" />',

            '<!-- Twitter Seo Tags -->',
            '<meta property="twitter:type" content="website" />',
            '<meta property="twitter:url" content="'. $page->site->getAddress() .'" />',
            '<meta property="twitter:title" content="'. $title .'" />',
            '<meta property="twitter:description" content="'. $description .'" />',

            '<!-- Facebook Seo Tags -->',
            '<meta property="og:type" content="website" />',
            '<meta property="og:url" content="'. $page->site->getAddress() .'" />',
            '<meta property="og:title" content="'. $title .'" />',
            '<meta property="og:description" content="'. $description .'" />',

            '<!-- Google / Search Engine Tags -->',
            '<meta itemprop="name" content="'. $title .'" />',
            '<meta itemprop="description" content="'. $description .'" />',
            
            '<!-- Images -->',
            '<link href="'. $image .'" rel="shortcut icon" type="image/png" />',
            '<meta property="og:image" content="'. $image .'" />',
            '<meta property="twitter:image" content="'. $image .'" />',
            '<meta itemprop="image" content="'. $image .'"/>',
        ];

        // Block SEO
        if ($block_seo) {
            $seo[] = [
                '<!-- Block Seo -->',
                '<meta name="robots" content="noindex">'
            ];
        }


        foreach ($seo as $key => $value) {
            $html .= "\n\n";
            $html .= is_array($value) ? implode(PHP_EOL, $value) : $value;
        }

        return $html;
    }
}

if (!function_exists('parse')) {
    function parse($url, $key = null){
        $array = parse_url($url);
        
        // Serve array without error
        app('config')->set('array-parse-temp', $array);
        $key = !empty($key) ? '.'.$key : null;
        return app('config')->get('array-parse-temp'. $key);
    }
}

if (!function_exists('generate_serial')) {
    function generate_serial(){
        $chars = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        foreach ($chars as $key => $value) {
            $chars[$key] = strtolower($value);
        }
        $sn = '';
        $max = count($chars)-1;
        for($i=0;$i<25;$i++){
          $sn .= (!($i % 7) && $i ? '-' : '').$chars[rand(0, $max)];
        }
        return $sn;
    }
}

if (!function_exists('__s')) {
    function __s(){
        return request()->_site;
    }
}
if (!function_exists('__dashed_svg')) {
    function __dashed_svg($svg, $class = ''){
        $svg = explode('---', $svg);
        $_svg = null;

        if(!empty($svg[0]) && !empty($svg[1])){
            $_svg = __i($svg[0], $svg[1], $class)->__toString();
        }
        return $_svg;
    }
}

if (!function_exists('generate_svg_section')) {
    function generate_svg_section($svg, $class = ''){
        $svg = explode('---', $svg);
        $_svg = null;

        if(!empty($svg[0]) && !empty($svg[1])){
            $_svg = __i($svg[0], $svg[1], $class)->__toString();
        }
        return $_svg;
    }
}
if (!function_exists('_collect_sections')) {
    function _collect_sections(){
        $_collect = collect(config('bio.sections'))->groupBy('category', true)->toArray();
        $collect = [];
        foreach($_collect as $key => $value){
            $header = config("bio.section-head.$key");
            $item = [
               ...$header,
               'items' => $value, 
            ];
    
            $collect[$key] = $item;
        }

        return collect($collect);
    }
}
if (!function_exists('__collect_sectons')) {
    function __collect_sectons(){
        $_collect = collect(config('bio.sections'))->toArray();
        $collect = [];
        foreach($_collect as $key => $value){
            $item = $value;
            $item['ori-icon-svg'] = generate_svg_section(__a($value, 'ori-icon-svg'), 'h-5 w-5');
    
            $collect[$key] = $item;
        }

        return collect($collect);
    }
}

if (!function_exists('generate_section')) {
    function generate_section(){

        $configBlocks = _collect_sections();
        $blocks = [];
        foreach ($configBlocks as $key => $value) {
            $block = $value;
            $block['ori-icon'] = generate_svg_section(__a($value, 'ori-icon'));


            foreach (__a($value, 'items') as $k => $v) {
                $block['items'][$k]['ori-icon-svg'] = generate_svg_section(__a($v, 'ori-icon-svg'), 'h-5 w-5');
            }

            $blocks[$key] = $block;
        }

        return $blocks;
    }
}

if (!function_exists('_generate_addon_config')) {
    function _generate_addon_config(){

        $configBlocks = collect(config('bio.addons'))->toArray();
        $blocks = [];
        foreach ($configBlocks as $key => $value) {
            $block = $value;
            $block['icon']['svg'] = generate_svg_section(__a($value, 'icon.svg'));

            $blocks[$key] = $block;
        }

        return $blocks;
    }
}
if (!function_exists('linkText')) {

	function linkText($text)
	{
	    return preg_replace('/https?:\/\/[\w\-\.!~#?&=+%;:\*\'"(),\/]+/u','<a class="data-link" href="$0" target="_blank">$0</a>', $text);
	}
}
if (!function_exists('checkText')) {

	function checkText($str, $url = null)
	{
		if (mb_strlen($str, 'utf8') < 1) {
			return false;
		}

		$str = str_replace($url, '', $str);
		$str = trim($str);
		$str = nl2br(e($str));
		$str = str_replace('&#039;', "'", $str);

		$str = str_replace(array(chr(10), chr(13)), '' , $str);
		$url = preg_replace('#^https?://#', '', url('').'/');

		// Hashtags and @Mentions
		$str = preg_replace_callback(
        '~([#@])([^\s#@!\"\$\%&\'\(\)\*\+\,\-./\:\;\<\=\>?\[/\/\/\\]\^\`\{\|\}\~]+)~',
        function ($matches) use($url) {
					$url = $matches[1] == "#" ? "".$url."mix/explore?q=%23".$matches[2]."" : $url.$matches[2];
					return "<a href=\"//".$url."\">$matches[0]</a>";
        },
        $str
    );

		$str = stripslashes($str);
		return $str;
	}
}

if(!function_exists('encodeCrc')){
    function encodeCrc($code) {
        $hash = crc32($code);
        return base_convert($code . $hash, 10, 36);
    }
}

if(!function_exists('decodeCrc')){
    function decodeCrc($encoded_string) {
        $decoded = base_convert($encoded_string, 36, 10);
        $length = strlen($decoded) - 8;
        $original_user_id = substr($decoded, 0, $length);
        return $original_user_id;
    }
}

if(!function_exists('getAvatar')){
    function getAvatar($id){
        if(!$user = User::find($id)) return;
        return $user->getAvatar();
    }
}

if(!function_exists('_ai_convert_to_section')){
    function _ai_convert_to_section($array = [], $section){
		$blank = [];
		// $count = count($this->sections) + 1;
        $content = is_array(ao($array, 'content')) ? ao($array, 'content') : [];
        $settings = is_array(ao($array, 'settings')) ? ao($array, 'settings') : [];
        $form = is_array(ao($array, 'form')) ? ao($array, 'form') : [];
        $items = is_array(ao($array, 'items')) ? ao($array, 'items') : [];

        if (count($items) > 0) {
            $_items = [];
            foreach ($items as $_i) {
                $newItem = [
                    'uuid' => str()->uuid(),
                    'section_id' => $section->uuid,
                    ...$_i
                ];

                $_items[] = $newItem;
            }

            $items = $_items;
        }
        
        $blank[] = [
            ...$section->toArray(),
            'content' => [
                ...$section->content,
                ...$content,
            ],
            'settings' => [
                ...$section->settings,
                ...$settings,
            ],
            'form' => [
                ...$section->form,
                ...$form,
            ],
            'items' => [
                ...$section->items,
                ...$items,
            ],
        ];

        return $blank;
	}
}

if(!function_exists('aiConvertToSection')){
    function aiConvertToSection($sections){
		$blank = [];
		// $count = count($this->sections) + 1;
		
		foreach($sections as $section){
			$section_settings = is_array(ao($section, 'section_settings')) ? ao($section, 'section_settings') : [];
			
			if (is_array(ao($section, 'items')) && count(ao($section, 'items')) > 0) {
				$items = [];
				foreach (ao($section, 'items') as $_i) {
					$newItem = [
						'uuid' => str()->uuid(),
						...$_i
					];

					$items[] = $newItem;
				}

				$section['items'] = $items;
			}

			$new = [
				'uuid' => str()->uuid(),
				'published' => 1,
				// 'position' => $count,
				'settings' => [
					'silence' => 'golden',
				],
				'form' => [
					'email' => 'Email',
					'button_name' => 'Signup',
				],
				...$section,
				'section_settings' => [
					'height' => 'fit',
					'width' => 'fill',
					'spacing' => 'l',
					...$section_settings
				],
			];

			$blank[] = $new;
		}

		return $blank;
	}
}

if (!function_exists('iam')) {
    function iam(){
        return auth()->user();
    }
}

if (!function_exists('isJson')) {
    function isJson($string) {
       json_decode($string);
       return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('purify_ai_json')) {
    function purify_ai_json($chat){
        $response = [];
        



        return $response;
    }
}

if (!function_exists('getSectionPreset')) {
    function getSectionPreset($section){
        $getSections = config("sections.$section");

        if(!is_array($getSections)) return [];

        foreach ($getSections as $key => $value) {

            if(is_array($sections = ao($value, 'sections'))){
                foreach ($sections as $k => $section){
                    if(!$_key = ao($section, 'key')) continue;

                    $_key = explode('.', $_key);
                    $config = [
                        ...getPresetKey($_key[0], $_key[1]),
                        ...$section,
                    ];
                    $getSections[$key]['sections'][$k] = $config;
                }
            }
        }

        return $getSections;
    }
}

if (!function_exists('getPresetKey')) {
    function getPresetKey($section, $_key){
        $getPresets = config("presets.$section");

        foreach ($getPresets as $key => $value) {
            if(ao($value, 'key') == $_key) return $value;
        }
    }
}



if (!function_exists('logo')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function logo(){
      $logo = settings('logo');
      // Use text-based Mewayz logo instead of default image
      $default = 'data:image/svg+xml;base64,' . base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="40" viewBox="0 0 120 40">
          <text x="5" y="28" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="#2563eb" letter-spacing="2px">Mewayz</text>
        </svg>
      ');

      if (empty($logo)) {
          return $default;
      }

      if (!mediaExists('media/site/logo', $logo)) {
        return $default;
      }

      $logo = getStorage('media/site/logo', $logo);
      return $logo;
    }
}

if (!function_exists('login_image')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function login_image(){
      $image = settings('login_logo');
      $default = gs('assets/image/others/login-default-image.jpg');

      if (empty($image) || !mediaExists('media/site/login', $image)) {
          return $default;
      }

      $image = getStorage('media/site/login', $image);

      return $image;
    }
}

if(!function_exists('getRandomHexColor')){
    function getRandomHexColor() {
        // Generate random R, G, and B values
        $r = mt_rand(0, 255);
        $g = mt_rand(0, 255);
        $b = mt_rand(0, 255);
    
        // Convert decimal to hexadecimal
        $hexR = dechex($r);
        $hexG = dechex($g);
        $hexB = dechex($b);
    
        // Ensure that each hexadecimal component is two characters long
        $hexR = str_pad($hexR, 2, "0", STR_PAD_LEFT);
        $hexG = str_pad($hexG, 2, "0", STR_PAD_LEFT);
        $hexB = str_pad($hexB, 2, "0", STR_PAD_LEFT);
    
        // Concatenate the hexadecimal values
        $hexColor = '#' . $hexR . $hexG . $hexB;
    
        return $hexColor;
    }
}

if (!function_exists('logo_icon')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function logo_icon(){
      $logo = settings('logo_icon');
      // Use text-based Mewayz icon logo instead of default image
      $default = 'data:image/svg+xml;base64,' . base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60">
          <circle cx="30" cy="30" r="28" fill="#2563eb"/>
          <text x="30" y="38" font-family="Arial, sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle" letter-spacing="1px">M</text>
        </svg>
      ');

      if (empty($logo) || !mediaExists('media/site/logo', $logo)) {
          return $default;
      }

      $logo = getStorage('media/site/logo', $logo);

      return $logo;
    }
}

if (!function_exists('favicon')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function favicon(){
      $favicon = settings('favicon');
      $default = gs('assets/image/others/default-favicon.png');

      if (empty($favicon)) {
          return $default;
      }

      if (!mediaExists('media/site/favicon', $favicon)) {
        return $default;
      }


      $favicon = getStorage('media/site/favicon', $favicon);

      return $favicon;
    }
}


if (!function_exists('env_update')) {
    function env_update($data = array()){
        if(count($data) > 0){
            $env = file_get_contents(base_path() . '/.env');
            $env = explode("\n", $env);
            foreach((array)$data as $key => $value) {
                if($key == "_token") {
                    continue;
                }
                $notfound = true;
                foreach($env as $env_key => $env_value) {
                    $entry = explode("=", $env_value, 2);
                    if($entry[0] == $key){
                        $env[$env_key] = $key . "=\"" . $value."\"";
                        $notfound = false;
                    } else {
                        $env[$env_key] = $env_value;
                    }
                }
                if($notfound) {
                    $env[$env_key + 1] = "\n".$key . "=\"" . $value."\"";
                }
            }
            $env = implode("\n", $env);
            file_put_contents(base_path('.env'), $env);
            return true;
        } else {
            return false;
        }
    }

}


if (!function_exists('settings')) {
    function settings($key = null){
        if(!config('app.INSTALLED')){
            return false;
        }
       $getsettings = \App\Models\Setting::all()
       ->keyBy('key')
       ->transform(function ($setting) {
             $value = json_decode($setting->value, true);
             $value = (json_last_error() === JSON_ERROR_NONE) ? $value : $setting->value;
             return $value;
        })->toArray();
       app('config')->set('settings', $getsettings);
       $key = !empty($key) ? '.'.$key : null;
       return app('config')->get('settings'.$key);
    }
}


if (!function_exists('settings_put')) {
    function settings_put($key, $data = []){

        $settings = [];
        $settings[$key] = $data;

        $value = $data;
        if (is_array($value)) {
            $settings[$key] = json_encode($value);
            $value = json_encode($value);
        }

        $key_value = ['key' => $key, 'value' => $value];
        if (\App\Models\Setting::where('key', $key)->first()) {
                \App\Models\Setting::where('key', $key)->update(['value' => $value]);
        }else{
            \App\Models\Setting::insert($key_value);
        }
    }
}

if (!function_exists('getOtherResourceFile')) {
    function getOtherResourceFile($file, $dir = 'others', $file_get_contents = false){
        if (file_exists($include = base_path('resources') . "/$dir/$file.php")) {
            if (!$file_get_contents) {
                return require $include;
            }

            return file_get_contents($include);
        }
    }
}

if (!function_exists('slugify')) {
    function slugify($string, $delimiter = '_'){
        $slug = new \Ausi\SlugGenerator\SlugGenerator();
        return $slug->generate($string, ['delimiter' => $delimiter]);
    }
}

if(!empty('__key')){
    function __key($key = null){
        $microtime = microtime();
        $key = md5("$key");
        return 'wire:key="'. $key .'"';
    }
}

if(!empty('_k')){
    function _k($prefix = null, $key = null){
        if(!$key) $key = \Str::uuid();
        $key = md5("$prefix - $key");
        return "wire:key=$key";
    }
}

if(!empty('uukey')){
    function uukey($prefix = null, $key = null){
        if(!$key) $key = \Str::uuid();

        if($key) "$key::" . \Str::uuid();
        
        $key = md5("$prefix - $key");
        return $key;
    }
}

if(!empty('notempty')){
    function notempty(&$var) {
        return ($var==="0"||$var);
    }
}

if (!function_exists('tracking_log')) {
    function tracking_log(){
        $ip = getIp(); //getIp() 102.89.2.139

        $agent = new \Jenssegers\Agent\Agent;
        $iso_code = geoCountry($ip, 'country.iso_code');
        $iso_code = strtolower($iso_code);
        $country = geoCountry($ip, 'country.names.en');
        $city = geoCity($ip, 'city.names.en');

        $tracking = ['ip' => $ip, 'country' => ['iso' => $iso_code, 'name' => $country, 'city' => $city], 'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()]];


        return $tracking;
    }
}

if (!function_exists('_get_chart_data')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function _get_chart_data(Array $main_array){
        $results = [];
        foreach($main_array as $date_label => $data) {
            foreach($data as $label_key => $label_value) {
                if(!isset($results[$label_key])) {
                    $results[$label_key] = [];
                }
                $results[$label_key][] = $label_value;
            }
        }
        foreach($results as $key => $value) {
            $results[$key] = $value; // No need to convert to string
        }
        $results['labels'] = array_keys($main_array);
        return $results;
    }
}

if (!function_exists('get_chart_data')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function get_chart_data(Array $main_array){
            $results = [];
            foreach($main_array as $date_label => $data) {
                foreach($data as $label_key => $label_value) {
                    if(!isset($results[$label_key])) {
                        $results[$label_key] = [];
                    }
                    $results[$label_key][] = $label_value;
                }
            }
            foreach($results as $key => $value) {
                $results[$key] = '["' . implode('", "', $value) . '"]';
            }
            $results['labels'] = '["' . implode('", "', array_keys($main_array)) . '"]';
            return $results;
    }
}


if (!function_exists('geoCountry')) {
    function geoCountry($ip, $key = null){
        $database = storage_path('geoip/GeoLite2-Country.mmdb');
        $reader = new MaxMind\Db\Reader($database);

        $items = $reader->get($ip);

        $reader->close();

        return ao($items, $key);
    }
}
if (!function_exists('geoCity')) {
    function geoCity($ip, $key = null){
        $database = storage_path('geoip/GeoLite2-City.mmdb');
        $reader = new MaxMind\Db\Reader($database);

        $items = $reader->get($ip);

        $reader->close();


        app('config')->set('geo-temp', $items);
        $key = !empty($key) ? '.'.$key : null;
        return app('config')->get('geo-temp'. $key);
    }
}

if(!function_exists('_rest')){

    function _rest(){

        return new \Unirest\Request;
    }
}

if (!function_exists('getIp')) {
    function getIp() {

        // return '102.89.2.139';
        // return '41.210.63.255';

        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {

            if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return trim(reset($ips));
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER['REMOTE_ADDR'];
        } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        return '';
    }
}
if (!function_exists('allLocale')) {
    function allLocale(){
       // Get current translation locale
       $locale = config('app.locale');

       // Get all translations files
       $path = resource_path('lang');
       // Languages array
       $languages = \File::files($path);

       return $languages;
    }
}
if (!function_exists('getContrastColor')) {
    function getContrastColor($hexColor) {

        // hexColor RGB
        $R1 = hexdec(substr($hexColor, 1, 2));
        $G1 = hexdec(substr($hexColor, 3, 2));
        $B1 = hexdec(substr($hexColor, 5, 2));

        // Black RGB
        $blackColor = "#000000";
        $R2BlackColor = hexdec(substr($blackColor, 1, 2));
        $G2BlackColor = hexdec(substr($blackColor, 3, 2));
        $B2BlackColor = hexdec(substr($blackColor, 5, 2));

         // Calc contrast ratio
         $L1 = 0.2126 * pow($R1 / 255, 2.2) +
               0.7152 * pow($G1 / 255, 2.2) +
               0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
              0.7152 * pow($G2BlackColor / 255, 2.2) +
              0.0722 * pow($B2BlackColor / 255, 2.2);

        $contrastRatio = 0;
        if ($L1 > $L2) {
            $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
        }

        // If contrast is more than 5, return black color
        if ($contrastRatio > 5) {
            return '#000000';
        } else { 
            // if not, return white color.
            return '#FFFFFF';
        }
    }
}

if (!function_exists('ao')) {
    function ao($array, $key = NULL){
        app('config')->set('array-temp', $array);
        if(notempty($key)) $key = ".$key";
        return app('config')->get('array-temp'. $key);
    }
}

if (!function_exists('__a')) {
    function __a($array, $key = NULL){
        return ao($array, $key);
    }
}

if (!function_exists('getFonts')) {
    function getFonts(){
        $fonts = config('yena.fonts');
        $processVariant = function($name){

            return __($name) . ' ';
        };
        
        foreach ($fonts as $key => $value) {
            $value = $value;

            $variants = [];

            foreach ($value['variants'] as $k => $v){
                $name = $v;

                // $name = str_replace('500', __('Medium'), $name);
                // $name = str_replace('italic', 'Italic', $name);

                
                $name = str_replace([
                    '100',
                    '200',
                    '300',
                    '400',
                    '500',
                    '600',
                    '700',
                    '800',
                    '900',
                    'regular',
                    'italic',
                ], [
                    $processVariant('Thin'),
                    $processVariant('Extra Light'),
                    $processVariant('Light'),
                    $processVariant('Normal'),
                    $processVariant('Medium'),
                    $processVariant('Semi-Bold'),
                    $processVariant('Bold'),
                    $processVariant('Extra-Bold'),
                    $processVariant('Black'),
                    'Regular',
                    'Italic'
                ],
                $name);

                $variants[$k] = $name;
            }
            
            $value['_variants'] = $variants;
            $fonts[$key] = $value;
        }

        //$fonts = array_slice($fonts, 0, 10);

        return collect($fonts);
    }
}

if (!function_exists('storageFileSize')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function storageFileSize($directory, $file){
      $location = $directory .'/'. $file;
      $filesystem = sandy_filesystem($location);

      if (Storage::disk($filesystem)->exists($location)) {

          return Storage::disk($filesystem)->size($location);
      }

      return 0;
    }
}

if (!function_exists('storageDeleteDir')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function storageDeleteDir($directory){
      $location = $directory;
      $filesystem = sandy_filesystem($location);

      if (Storage::disk($filesystem)->exists($location)) {

          Storage::disk($filesystem)->deleteDirectory($location);

          return true;
      }

      return false;
    }
}

if (!function_exists('storageDelete')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function storageDelete($directory, $file = null){
      $location = $directory .'/'. $file;
      $filesystem = sandy_filesystem($location);

      if (Storage::disk($filesystem)->exists($location)) {

          Storage::disk($filesystem)->delete($location);

          return true;
      }

      return false;
    }
}

if (!function_exists('mediaExists')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function mediaExists($directory, $file = null){
      $location = $directory .'/'. $file;
      $filesystem = sandy_filesystem($location);

      if (Storage::disk($filesystem)->exists($location)) {
          return true;
      }

      return false;
    }
}

if (!function_exists('gs')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function gs($directory, $file = null){
        if ($file == null) {
            $file = basename($directory);
            $directory = dirname($directory);
        }

        return getStorage($directory, $file);
    }
}

if (!function_exists('__t')) {
    function __t($string, array $values = []){
        $string = __($string, $values);
        return $string;
        return clean($string, 'titles');
    }
}

if (!function_exists('settings')) {
    function settings($key = null){
        return;
       $getsettings = \App\Models\Setting::all()
       ->keyBy('key')
       ->transform(function ($setting) {
             $value = json_decode($setting->value, true);
             $value = (json_last_error() === JSON_ERROR_NONE) ? $value : $setting->value;
             return $value;
        })->toArray();
       app('config')->set('settings', $getsettings);
       $key = !empty($key) ? '.'.$key : null;
       return app('config')->get('settings'.$key);
    }
}

if (!function_exists('getStorage')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function getStorage($directory, $file){
        // App Debug
        if (config('app.debug')) {
            $pathinfo = pathinfo($file);
            $date = date('Y-m-d H:i:s');

            $app_debug_css = explode(',', config('app.APP_DEBUG_CSS'));
            if (in_array(ao($pathinfo, 'basename'), $app_debug_css)) {
                $file = "$file?v=$date";
            }
        }
        if (config('app.env') == 'production') {
            $pathinfo = pathinfo($file);
            $version = config('app.app_version');

            $app_debug_css = explode(',', config('app.APP_DEBUG_CSS'));
            if (in_array(ao($pathinfo, 'basename'), $app_debug_css)) {
                $file = "$file?v=$version";
            }
        }
        
      $location = $directory .'/'. $file;
      $filesystem = sandy_filesystem($location);

      $get = \Storage::disk($filesystem)->url($location);

      return $get;
    }
}

if (!function_exists('sandy_filesystem')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function sandy_filesystem($location){
      $filesystem = env('FILESYSTEM');

      if (!config('app.AWS_ASSETS')) {
        $assets = explode('/', $location);
        if (isset($assets[0]) && $assets[0] == 'assets') {
            $filesystem = 'local';
        }
      }


      return $filesystem;
    }
}

if (!function_exists('getStoragePutAs')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function getStoragePutAs($directory, $file, $name){
      $filesystem = sandy_filesystem($directory);

      $put = \Storage::disk($filesystem)->putFileAs($directory, $file, $name);
      \Storage::disk($filesystem)->setVisibility($put, 'public');

      return $name;
    }
}

if (!function_exists('putStorage')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function putStorage($directory, $file){
      $filesystem = sandy_filesystem($directory);


      $put = \Storage::disk($filesystem)->put($directory, $file);

      \Storage::disk($filesystem)->setVisibility($put, 'public');


      return basename($put);
    }
}


if (!function_exists('getYouTubeVideoId')) {
    function getYouTubeVideoId($url) {
            $regExp = "/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/";
            preg_match($regExp, $url, $video);
            return $video[1] ?? '';
    }
}

if (!function_exists('getVimeoId')) {
    function getVimeoId($url) {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1] ?? '';
        }
        return false;
    }
}

if (!function_exists('getVimeoThumb')) {
    function getVimeoThumb($id) {
        try {
            $arr_vimeo = unserialize(file_get_contents("https://vimeo.com/api/v2/video/$id.php"));
            return $arr_vimeo[0]['thumbnail_large'];
        } catch (\Exception $e) {
            
        }


        // return $arr_vimeo[0]['thumbnail_small']; // returns small thumbnail
        // return $arr_vimeo[0]['thumbnail_medium']; // returns medium thumbnail
        // return $arr_vimeo[0]['thumbnail_large']; // returns large thumbnail
    }
}
if (!function_exists('getDailyMotionId')) {
    function getDailyMotionId($url){
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $url, $m)) {
            if (isset($m[6])) {
                return $m[6];
            }
            if (isset($m[4])) {
                return $m[4];
            }
            return $m[2] ?? '';
        }
        return false;
    }
}
if (!function_exists('getDailymotionThumb')) {
    function getDailymotionThumb($id) {
        try {
            $thumbnail_large_url = 'https://api.dailymotion.com/video/'.$id.'?fields=thumbnail_720_url'; //pass thumbnail_360_url, thumbnail_480_url, thumbnail_720_url, etc. for different sizes
            $json_thumbnail = file_get_contents($thumbnail_large_url);
            $arr_dailymotion = json_decode($json_thumbnail, TRUE);
            $thumb = $arr_dailymotion['thumbnail_720_url'];
            return $thumb;
          } catch (\Exception $e) {
                return false;

                // Log error
          }  
    }
}

if (!function_exists('getPlatformThumbnail')) {
    function getPlatformThumbnail($stream, $video){
        switch ($stream) {
            case 'youtube':
                $videoID = getYouTubeVideoId($video);

                return "https://img.youtube.com/vi/$videoID/maxresdefault.jpg";

                // sddefault.jpg - Low quality
                // mqdefault.jpg - Medium quality
                // hqdefault.jpg - High quality
                // maxresdefault.jpg - Max quality
            break;

            case 'vimeo':
                $videoID = getVimeoId($video);

                return getVimeoThumb($videoID);
            break;

            case 'dailymotion':
                $videoID = getDailyMotionId($video);

                return getDailymotionThumb($videoID);
            break;
        }

        return false;
    }
}

if (!function_exists('getEmbedableLink')) {
    function getEmbedableLink($stream = '', $video){
        switch ($stream) {
            case 'youtube':
                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/', $video, $match)) {
                    $embed = $match[1] ?? '';

                    $embed = "https://www.youtube.com/embed/$embed?autoplay=1";
                    
                    return $embed;
                }
            break;
            case 'vimeo':
                if(preg_match('/https:\/\/(player\.)?vimeo\.com(\/video)?\/(\d+)/', $video, $match)) {
                    $embed = $match[3] ?? '';

                    $embed = "https://player.vimeo.com/video/$embed";

                    return $embed;
                }
            break;
            case 'twitch':
                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:twitch\.tv\/)(.+)$/', $video, $match)) {
                    $embed = $match[1] ?? '';

                    $server = $_SERVER['HTTP_HOST'];

                    $embed = "https://player.twitch.tv/?channel=$embed&autoplay=false&parent=$server";

                    return $embed;
                }
            break;
            case 'soundcloud':
                if(preg_match('/(soundcloud\.com)/', $video)) {
                    $embed = $video;

                    $embed = "https://w.soundcloud.com/player/?url=$embed&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true";

                    return $embed;
                }
            break;
            case 'dailymotion':
                $video_id = getDailyMotionId($video);

                $embed = "https://www.dailymotion.com/embed/video/$video_id?autoplay=1";

                return $embed;
            break;
        }

        return $video;
    }
}

if(!function_exists('removeHttpHttpsWww')){
    function removeHttpHttpsWww($url) {
        // Remove "http://", "https://", and "www."
        $clean_url = preg_replace('/^(http:\/\/|https:\/\/)?(www\.)?/', '', $url);
        return $clean_url;
    }
}

if (!function_exists('addHttps')) {
    function addHttps($url, $scheme = 'https'){
        return parse_url($url, PHP_URL_SCHEME) === null ? "$scheme://$url" : $url;
    }
}

if (!function_exists('validate_url')) {
    function validate_url( $url ) {

        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }
}

if (!function_exists('hour2min')) {
    function hour2min($time){
        $time1 = strtotime('12:00am');
        $newtime = strtotime($time);

        if (preg_match('/am$/i', $time)){

            $newtime = trim($time,"AM ");

        } elseif (preg_match('/pm$/i', $time)) {

            $newtime = trim($time,"PM ");

        }

        $time2 = strtotime($newtime);


        $difference = round(abs($time2 - $time1) / 3600,2);

        $hours = $difference;
        $minutes = 0; 
        if (strpos($hours, ':') !== false) 
        { 
            // Split hours and minutes. 
            list($hours, $minutes) = explode(':', $hours); 
        } 


        return $hours * 60 + $minutes;
    }
}