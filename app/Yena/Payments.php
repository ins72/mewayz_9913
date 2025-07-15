<?php

namespace App\Yena;
use App\Models\PaymentsSpv;
use App\Models\PaymentsSpvHistory;

class Payments{
    private $dir;
    function __construct(){
        $this->dir = base_path('app/Payments');

    }

    public function dir(){
        return $this->dir;
    }

    public static function is_paid($sxref){
        if (!$payment = PaymentsSpv::where('sxref', $sxref)->first()) {
            return false;
        }

        if ($payment->status) {
            return $payment;
        }
    }

    public function create($sxref, $data = [], $keys = [], $meta = []){

        $price = (float) ao($data, 'price');
        $method = ao($data, 'method');
        $redirect = \Route::has("sandy-payments-$method-create") ? route("sandy-payments-$method-create", ['sxref' => $sxref]) : false;


        $spv = new PaymentsSpv;
        $spv->email = ao($data, 'email');
        $spv->price = ao($data, 'price');
        $spv->currency = ao($data, 'currency');
        $spv->method = ao($data, 'method');
        $spv->callback = ao($data, 'callback');
        $spv->sxref = $sxref;
        $spv->keys = $keys;
        $spv->meta = $meta;
        $spv->save();



        if ($price == 0) {
            $redirect = url_query(ao($data, 'callback'), ['sxref' => $sxref]);
            $spv = PaymentsSpv::find($spv->id);
            $spv->is_paid = 1;
            $spv->status = 1;
            $spv->save();
        }

        if ($redirect) {
            return redirect($redirect);
        }

        return back()->with('error', __('Payment route doesnt exists'));
    }

    public static function has($plugin){
        $self = new self();
        $pluginDir = "$self->dir/$plugin";
        if (is_dir($pluginDir)) {
            return true;
        }

        return false;
    }

    public static function getdir($plugin, $dir = ''){
        $self = new self();
        $plugindir = "$self->dir/$plugin";
        if (is_dir($plugindir)) {
            return "$plugindir/$dir";
        }

        return false;
    }
    
    public static function config($plugin, $key = null){
        $self = new self();
        if (!$self->has($plugin)) {
            return false;
        }

        if (file_exists($file = $self->getdir($plugin, 'config.php'))) {
            $config = file_get_contents($file);
            $config = json_decode($config, true);

            if (!is_array($config)) {
                $config = [];
            }

            return ao($config, $key);
        }

        return false;
    }

    public function getInstalledMethods(){
        $self = new self();
        $plugins = [];
        $dir = $this->dir;

        foreach(scandir($path = $dir) as $dir){
            if (file_exists($filepath = "{$path}/{$dir}/config.php")) {
                $plugins[$dir] = $self->config($dir);
            }
        }

        return $plugins;
    }

    public static function success($spvid, $reference, $data){
        if (!$spv = PaymentsSpv::where('id', $spvid)->first()) {
            return false;
        }

        $spv = PaymentsSpv::find($spv->id);
        $spv->status = 1;
        $spv->is_paid = 1;

        $spv->keys = null;
        $spv->method_ref = $reference;

        $spv->save();


        //

        if ($history = PaymentsSpvHistory::where('spv_id', $spv->id)->first()) {
            $history = PaymentsSpvHistory::find($history->id);
            $history->status = 1;
            $history->method = $spv->method;
            $history->method_ref = $reference;
            $history->method_data = $data;
            $history->save();
        }else{
            $new = new PaymentsSpvHistory;
            $new->spv_id = $spv->id;
            $new->status = 1;
            $new->method = $spv->method;
            $new->method_ref = $reference;
            $new->method_data = $data;

            $new->save();
        }

        return true;
    }

    public static function failed($spvid, $reference, $data){
        if (!$spv = PaymentsSpv::where('id', $spvid)->first()) {
            return false;
        }

        $spv = PaymentsSpv::find($spv->id);
        $spv->status = 0;
        $spv->is_paid = 0;

        //$spv->keys = null;
        $spv->method_ref = $reference;

        $spv->save();


        //

        if ($history = PaymentsSpvHistory::where('spv_id', $spv->id)->first()) {
            $history = PaymentsSpvHistory::find($history->id);
            $history->status = 0;
            $history->save();
        }else{
            $new = new PaymentsSpvHistory;
            $new->spv_id = $spv->id;
            $new->status = 0;
            $new->method = $spv->method;
            $new->method_ref = $reference;
            $new->method_data = $data;

            $new->save();
        }

        return true;

    }
}