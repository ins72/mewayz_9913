<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaxRate extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name',
        'country_code',
        'state_code',
        'rate',
        'type',
        'is_active',
        'effective_from',
        'effective_to'
    ];
    
    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }
    
    public function scopeByState($query, $stateCode)
    {
        return $query->where('state_code', $stateCode);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();
        
        return $query->where('effective_from', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', $date);
                    });
    }
    
    public static function getTaxRate(string $countryCode, ?string $stateCode = null, string $type = 'vat'): ?self
    {
        $query = self::active()
            ->effective()
            ->byCountry($countryCode)
            ->byType($type);
        
        if ($stateCode) {
            $query->byState($stateCode);
        }
        
        return $query->first();
    }
    
    public static function calculateTax(float $amount, string $countryCode, ?string $stateCode = null, string $type = 'vat'): array
    {
        $taxRate = self::getTaxRate($countryCode, $stateCode, $type);
        
        if (!$taxRate) {
            return [
                'tax_amount' => 0,
                'tax_rate' => 0,
                'tax_rate_id' => null,
                'subtotal' => $amount,
                'total' => $amount
            ];
        }
        
        $taxAmount = $amount * ($taxRate->rate / 100);
        
        return [
            'tax_amount' => round($taxAmount, 2),
            'tax_rate' => $taxRate->rate,
            'tax_rate_id' => $taxRate->id,
            'subtotal' => $amount,
            'total' => $amount + $taxAmount
        ];
    }
    
    public function calculateTaxForAmount(float $amount): array
    {
        $taxAmount = $amount * ($this->rate / 100);
        
        return [
            'tax_amount' => round($taxAmount, 2),
            'tax_rate' => $this->rate,
            'tax_rate_id' => $this->id,
            'subtotal' => $amount,
            'total' => $amount + $taxAmount
        ];
    }
    
    public function isEffective($date = null): bool
    {
        $date = $date ?? now();
        
        $fromCheck = $this->effective_from <= $date;
        $toCheck = is_null($this->effective_to) || $this->effective_to >= $date;
        
        return $fromCheck && $toCheck;
    }
    
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
    
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    
    public static function getAvailableTypes(): array
    {
        return [
            'vat' => 'Value Added Tax',
            'gst' => 'Goods and Services Tax',
            'sales_tax' => 'Sales Tax',
            'income_tax' => 'Income Tax',
            'corporate_tax' => 'Corporate Tax',
            'withholding_tax' => 'Withholding Tax'
        ];
    }
    
    public static function seedDefaultTaxRates(): void
    {
        $taxRates = [
            // US Sales Tax (varies by state)
            ['name' => 'California Sales Tax', 'country_code' => 'US', 'state_code' => 'CA', 'rate' => 7.25, 'type' => 'sales_tax'],
            ['name' => 'New York Sales Tax', 'country_code' => 'US', 'state_code' => 'NY', 'rate' => 8.0, 'type' => 'sales_tax'],
            ['name' => 'Texas Sales Tax', 'country_code' => 'US', 'state_code' => 'TX', 'rate' => 6.25, 'type' => 'sales_tax'],
            
            // EU VAT
            ['name' => 'Germany VAT', 'country_code' => 'DE', 'rate' => 19.0, 'type' => 'vat'],
            ['name' => 'France VAT', 'country_code' => 'FR', 'rate' => 20.0, 'type' => 'vat'],
            ['name' => 'UK VAT', 'country_code' => 'GB', 'rate' => 20.0, 'type' => 'vat'],
            
            // Other countries
            ['name' => 'Canada GST', 'country_code' => 'CA', 'rate' => 5.0, 'type' => 'gst'],
            ['name' => 'Australia GST', 'country_code' => 'AU', 'rate' => 10.0, 'type' => 'gst'],
            ['name' => 'Japan Consumption Tax', 'country_code' => 'JP', 'rate' => 10.0, 'type' => 'vat'],
            ['name' => 'India GST', 'country_code' => 'IN', 'rate' => 18.0, 'type' => 'gst']
        ];
        
        foreach ($taxRates as $taxRate) {
            $taxRate['effective_from'] = now()->subYear();
            $taxRate['is_active'] = true;
            
            self::updateOrCreate(
                [
                    'country_code' => $taxRate['country_code'],
                    'state_code' => $taxRate['state_code'] ?? null,
                    'type' => $taxRate['type']
                ],
                $taxRate
            );
        }
    }
}