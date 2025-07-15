<?php

namespace App\Yena;
use App\Models\YenaEmbedStore;
use Embed\Embed;

class YenaEmbed{
    public $url;
    public $objects = [];

    function __construct($url){
        $this->url = $url;
    }

    
    public function check_oembed(){
        $providers = config('yena.oembed-providers.oembed_providers');

        foreach ($providers as $endpoint => $provider) {
            if (isset($provider)) {
                if ($this->findProviderMatch($this->url, $provider)) {
                    return true;
                }
            } else {
                if ($this->findProviderMatch($this->url, $endpoint)) {
                    return true;
                }
            }
        }
        
        foreach (config('yena.oembed-providers.regex_providers') as $provider) {
            if ($this->findProviderMatch($this->url, $provider['urls'])) {
                return true;
            }
        }
    }

    public function regex_oembed(){
        $providers = config('yena.oembed-providers.regex_providers');

        foreach ($providers as $provider) {
            if ($this->findProviderMatch($this->url, $provider['urls'])) {
                return $this->regex_extractor($provider, $this->url);
            }
        }

        return false;
    }
    

    public function fetch(){

        try {
            $this->get_dom();
        }catch (\Exception){
            // return;
        }

        return $this->objects;
    }


    private function get_dom(){
        if ($store = $this->fetch_stored()) {
            return $this->objects = $store->data;
        }

        $embed = new Embed();
        $embed->setSettings([
            'twitch:parent' => url('/'), //Required to embed twitch videos as iframe
        ]);
        $info = $embed->get($this->url);

        // Website
        $this->objects['w'] = parse($info->providerUrl, 'host');
        $this->objects['fw'] = (string) $info->url;

        // Title
        if ($info->title) {
            $this->objects['t'] = $info->title;
        }


        // Description
        if ($info->description) {
            $this->objects['d'] = $info->description;
        }

        // Seo Image
        if ($info->image) {
            $this->objects['s'] = (string) $info->image;
        }

        // Favicon
        if ($info->favicon) {
            $this->objects['f'] = (string) $info->favicon;
        }

        if ($info->code) {
            $this->objects['h'] = $info->code->html;
        }else{
            if($regex_embed = $this->regex_oembed()){
                $this->objects['h'] = $regex_embed;
            }
        }

        //$this->store();
    }

    private function fetch_stored(){
        if (!$store = YenaEmbedStore::where('link', $this->url)->first()) {
            return false;
        }

        if ($store->created_at->diffInHours() > 24) {
            $store->delete();


            return false;
        }

        return $store;
    }

    private function store(){
        if ($store = YenaEmbedStore::where('link', $this->url)->first()) {
            return false;
        }

        $new = new YenaEmbedStore;
        $new->link = $this->url;
        $new->data = $this->objects;
        $new->save();
    }
    
    protected function findProviderMatch($url, $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (!!preg_match($pattern, $url)) {
                return true;
            }
        }
        return false;
    }



    // Regex EXTRACTOR
    public function regex_extractor($provider, $url, $parameters = [])
    {
        $data = $provider['data'];

        $matches = null;

        foreach ($provider['urls'] as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                break;
            }
        }

        if (!$matches) {
            return null;
        }

        $protocol = $this->regex_get_protocol($provider, $url);
        $data = $this->regex_hydrate_provider($data, $matches, $protocol);

        $html = $data['html'];
        $type = array_key_first($html);
        return (new HtmlBuilder($type, $html[$type]))->html();
    }

    protected function regex_get_protocol($provider, $url): string
    {
        if ($provider['ssl']) {
            return 'https';
        } elseif (strpos($url, 'https://') === 0) {
            return 'https';
        } else {
            return 'http';
        }
    }


    protected function regex_hydrate_provider(array &$data, array $matches, string $protocol): array
    {
        $matchCount = count($matches);

        // Check if we have an iframe creation array.
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = $this->regex_hydrate_provider($val, $matches, $protocol);
            } else {
                $data[$key] = str_replace('{protocol}', $protocol, $data[$key]);
                for ($i = 1; $i < $matchCount; $i++) {
                    $data[$key] = str_replace('{' . $i . '}', $matches[$i], $data[$key]);
                }
            }
        }

        return $data;
    }
}
