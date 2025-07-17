<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Yena\Teams;
use App\Yena\YenaMail;
use App\Plans\Traits\HasPlans;
use App\Yena\Currency;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Interfaces\WalletFloat;

class User extends Authenticatable implements MustVerifyEmail, Wallet, WalletFloat
{
    use HasApiTokens, Impersonate, HasFactory, Notifiable, HasPlans, HasWallet, HasWalletFloat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'payment_subscription_ids' => 'array',
        'store' => 'array',
        'wallet_settings' => 'array',
        'booking_workhours' => 'array',
        'booking_gallery' => 'array',
    ];

	protected $appends = [
		// 'avatar_json', // Commented out to avoid missing helper function issues
	];

    protected function avatarJson(): Attribute {
		$data = $this->getAvatar();
        return new Attribute(
            get: fn () => $data,
        );
    }

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->email_verified_at = $model->email_verified_at ? $model->email_verified_at : now();
        });
    }

    public function hasTemplateAccess($id){
        if(YenaTemplateAccess::where('user_id', $this->id)->where('template_id', $id)->first()) {
            return true;
        }

        return false;
    }

    public function hasBioTemplateAccess($id){
        if(YenaBioTemplateAccess::where('user_id', $this->id)->where('template_id', $id)->first()) {
            return true;
        }

        return false;
    }

    public function get_original_user(){
        return Teams::get_original_user();
    }
    
    public function team(){

        $team = Teams::init();
    
        if($team = YenaTeam::where('owner_id', $this->id)->first()) return $team;
        return $team;
    }

    public function projects(){
        return $this->hasMany(ProjectPixel::class, 'user_id');
    }

    public function planJsFeatures(){
        $user = $this;
        $features = [];

        if($subscription = $user->activeSubscription()){
            if($user->activeSubscription()->plan){
                $features = $subscription->features()->get();

                foreach($features as $feature){
                    if(!$feature) continue;
    
    
                    // if($feature == 'limit'){
                        
                    // }

                    if($this->isAdmin()){
                        $feature->limit = 9999;
                        $feature->enable = true;
                    }
                    $features[] = collect($feature);
                }
            }
            // if(!$feature) return false;

            // return $feature->type == 'limit' ? $feature->limit : $feature->enable;
        }

        return collect($features);
    }

	public function getAvatar(){
        $avatar = $this->avatar;
        $default = "https://api.dicebear.com/8.x/initials/svg?seed=$this->name";
        $check = mediaExists('media/avatar', $avatar);
        $path = getStorage('media/avatar', $avatar);

        $avatar = (!empty($avatar) && $check) ? $path : $default;

        if(validate_url($this->avatar)) $avatar = $this->avatar;

        return $avatar;
	}

    public function isAdmin(){
     
        if($this->role) return true;

        return false;
    }

    public function paymentMethod(){
        return config('app.wallet.defaultMethod');
    }

    public function currency(){
        $currency = 'USD';

        $currency = config('app.wallet.currency');
        
        $currency = strtoupper($currency);
        $code = Currency::symbol($currency);


        return [
            'code' => $code,
            'currency' => $currency
        ];
    }

    public function price($price = 0, $delimiter = 1){
        $code = ao($this->currency(), 'code');
        $price = (float) $price;
        $price = number_format($price, $delimiter);
        $light = "{$code}{$price}";

        return $light;
    }

    public function getSites(){

        $sites = [];

        $get = Site::where('user_id', $this->id)->where('is_template', 0)->where('is_admin', 0)->orderBy('updated_at', 'DESC')->get();
        foreach ($get as $key => $value) {
            if(!$value->canAccess()) continue;
            $sites[] = $value;
        }

        return $sites;
    }
    
    public function fullAccess(){
        $access = false;
        if(Teams::permission('ce')) $access = 'full_access';
        if($access == 'full_access') return true;
        return false;
    }

    public function getFolders(){

        $foldersModel = Folder::where('owner_id', $this->id)->get();


        $folders = [];

        foreach ($foldersModel as $item) {
            if(!$item->isMember($this->id)) continue;
            $folders[] = $item;
        }
        

        return $folders;
    }

    public function _delete(){
        //

        
        $user = $this;

        $exclude = [];

        
        $pages = Site::where('user_id', $user->id)->get();
        foreach ($pages as $key => $value) {
            $value->deleteCompletely();
        }

        $models = new \DirectoryIterator(base_path('app/Models'));
        // Get ALl Registered Upload Paths

        foreach ($models as $info){
            $class = str_replace('.php', '', $info->getFilename());
            $path = $info->getPathname();
            if ($info->isDot()) continue;
            if(in_array($class, $exclude)) continue;
            if(is_dir($path)) continue;

            $init = app()->make("\App\Models\\$class");

            $column = 'user';

            if(Schema::hasColumn($init->getTable(), 'user_id')){
                $column = 'user_id';
            }

            if(Schema::hasColumn($init->getTable(), 'owner_id')){
                $column = 'owner_id';
            }

            if(Schema::hasColumn($init->getTable(), 'user') || Schema::hasColumn($init->getTable(), 'user_id') || Schema::hasColumn($init->getTable(), 'owner_id')){
                $init->where($column, $user->id)->delete();
            }
        }
        
        $user->delete();
        
        return true;
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $mail = new YenaMail;
        $mail->send([
           'to' => $this->email,
           'subject' => __('Reset Password'),
        ], 'account.reset', [
           'token' => $token,
           'user' => $this
        ]);
    }
    
    /**
     * Get the workspaces for the user
     */
    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }
    
    /**
     * Get the organizations for the user (used as workspaces)
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
