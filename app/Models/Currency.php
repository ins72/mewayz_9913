<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Currency extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_active',
        'is_default'
    ];
    
    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
        
        static::updating(function ($model) {
            // Ensure only one default currency
            if ($model->is_default && $model->isDirty('is_default')) {
                self::where('id', '!=', $model->id)->update(['is_default' => false]);
            }
        });
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->first() ?? self::where('code', 'USD')->first();
    }
    
    public static function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }
        
        $from = self::where('code', $fromCurrency)->first();
        $to = self::where('code', $toCurrency)->first();
        
        if (!$from || !$to) {
            return 1.0;
        }
        
        // Convert to USD first, then to target currency
        return $to->exchange_rate / $from->exchange_rate;
    }
    
    public function convertTo(float $amount, string $targetCurrency): float
    {
        $rate = self::getExchangeRate($this->code, $targetCurrency);
        return $amount * $rate;
    }
    
    public function convertFrom(float $amount, string $sourceCurrency): float
    {
        $rate = self::getExchangeRate($sourceCurrency, $this->code);
        return $amount * $rate;
    }
    
    public function formatAmount(float $amount): string
    {
        return $this->symbol . number_format($amount, 2);
    }
    
    public static function getAvailableCurrencies(): array
    {
        return self::active()->orderBy('code')->get()->map(function ($currency) {
            return [
                'code' => $currency->code,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'exchange_rate' => $currency->exchange_rate
            ];
        })->toArray();
    }
    
    public function updateExchangeRate(float $rate): void
    {
        $this->update(['exchange_rate' => $rate]);
    }
    
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
    
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    
    public function setAsDefault(): void
    {
        // Remove default from all other currencies
        self::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }
    
    public static function seedDefaultCurrencies(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0, 'is_default' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.85],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.73],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 110.0],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.25],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.35],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr', 'exchange_rate' => 0.92],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 6.45],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 74.0],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'exchange_rate' => 5.20]
        ];
        
        foreach ($currencies as $currency) {
            self::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}