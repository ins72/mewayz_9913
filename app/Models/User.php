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

//     protected function avatarJson(): Attribute {
// 		$data = $this->getAvatar();
//         return new Attribute(
//             get: fn () => $data,
//         );
//     }

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

    // Gamification relationships and methods
    public function gamificationLevel()
    {
        return $this->hasOne(\App\Models\Gamification\UserLevel::class);
    }

    public function gamificationAchievements()
    {
        return $this->hasMany(\App\Models\Gamification\UserAchievement::class);
    }

    public function gamificationXpEvents()
    {
        return $this->hasMany(\App\Models\Gamification\XpEvent::class);
    }

    public function gamificationStreaks()
    {
        return $this->hasMany(\App\Models\Gamification\Streak::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(\App\Models\Gamification\Achievement::class, 'gamification_user_achievements', 'user_id', 'achievement_id')
                    ->withPivot(['progress', 'target', 'completed', 'completed_at', 'completion_count', 'progress_data'])
                    ->withTimestamps();
    }

    public function addXp($amount, $eventType = 'manual', $eventData = [])
    {
        $userLevel = $this->gamificationLevel();
        
        if (!$userLevel) {
            $userLevel = \App\Models\Gamification\UserLevel::create([
                'user_id' => $this->id,
                'level' => 1,
                'total_xp' => 0,
                'current_level_xp' => 0,
                'next_level_xp' => 100,
                'xp_to_next_level' => 100,
                'level_name' => 'Newcomer',
                'level_tier' => 'Bronze',
                'level_benefits' => []
            ]);
        }

        return $userLevel->addXp($amount, $eventType, $eventData);
    }

    public function hasCompletedAchievement(\App\Models\Gamification\Achievement $achievement)
    {
        return $this->gamificationAchievements()
                    ->where('achievement_id', $achievement->id)
                    ->where('completed', true)
                    ->exists();
    }

    public function getAchievementCompletionCount(\App\Models\Gamification\Achievement $achievement)
    {
        $userAchievement = $this->gamificationAchievements()
                                ->where('achievement_id', $achievement->id)
                                ->first();
        
        return $userAchievement ? $userAchievement->completion_count : 0;
    }

    public function updateStreak($streakType, $activityDate = null)
    {
        $streak = $this->gamificationStreaks()->where('streak_type', $streakType)->first();
        
        if (!$streak) {
            $streak = \App\Models\Gamification\Streak::create([
                'user_id' => $this->id,
                'streak_type' => $streakType,
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_completions' => 0,
                'is_active' => true,
                'streak_multiplier' => 1,
                'milestones' => []
            ]);
        }

        return $streak->updateStreak($activityDate);
    }

    public function getGamificationStats()
    {
        $userLevel = $this->gamificationLevel();
        
        if (!$userLevel) {
            return [
                'level' => 1,
                'total_xp' => 0,
                'achievements_completed' => 0,
                'active_streaks' => 0,
                'longest_streak' => 0
            ];
        }

        $achievementsCompleted = $this->gamificationAchievements()->where('completed', true)->count();
        $activeStreaks = $this->gamificationStreaks()->where('is_active', true)->count();
        $longestStreak = $this->gamificationStreaks()->max('longest_streak') ?? 0;

        return [
            'level' => $userLevel->level,
            'level_name' => $userLevel->level_name,
            'level_tier' => $userLevel->level_tier,
            'total_xp' => $userLevel->total_xp,
            'achievements_completed' => $achievementsCompleted,
            'active_streaks' => $activeStreaks,
            'longest_streak' => $longestStreak,
            'progress_percentage' => $userLevel->getProgressPercentage()
        ];
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
        $avatar = $this->avatar ?? null;
        $default = "https://api.dicebear.com/8.x/initials/svg?seed=" . urlencode($this->name);
        
        // Simple fallback for missing helper functions
        if (!empty($avatar) && filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }
        
        return $default;
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
