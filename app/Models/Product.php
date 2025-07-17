<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'name',
        'sku',
        'description',
        'short_description',
        'price',
        'cost_price',
        'currency',
        'type',
        'status',
        'stock_quantity',
        'track_inventory',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'images',
        'variants',
        'seo_data',
        'tags',
        'custom_fields',
    ];

    protected $casts = [
        'id' => 'string',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'track_inventory' => 'boolean',
        'dimensions' => 'array',
        'images' => 'array',
        'variants' => 'array',
        'seo_data' => 'array',
        'tags' => 'array',
        'custom_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Auto-generate SKU if not provided
            if (empty($model->sku)) {
                $model->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->cost_price) {
            return null;
        }
        
        return (($this->price - $this->cost_price) / $this->price) * 100;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->track_inventory && 
               $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->track_inventory && $this->stock_quantity <= 0;
    }

    public function getFeaturedImageAttribute(): ?string
    {
        $images = $this->images ?? [];
        return count($images) > 0 ? $images[0] : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('track_inventory', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Business Logic Methods
    public function adjustStock(int $quantity, string $reason = 'manual'): void
    {
        if (!$this->track_inventory) {
            return;
        }

        $newQuantity = max(0, $this->stock_quantity + $quantity);
        
        $this->update(['stock_quantity' => $newQuantity]);

        // Log stock movement (you could create a separate StockMovement model)
        \Log::info("Stock adjusted for product {$this->sku}: {$quantity} (reason: {$reason})");
    }

    public function reduceStock(int $quantity): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->adjustStock(-$quantity, 'sale');
        return true;
    }

    public function restoreStock(int $quantity): void
    {
        $this->adjustStock($quantity, 'return');
    }

    public function addVariant(array $variant): void
    {
        $variants = $this->variants ?? [];
        $variants[] = array_merge($variant, [
            'id' => (string) Str::uuid(),
            'created_at' => now()->toISOString(),
        ]);
        
        $this->update(['variants' => $variants]);
    }

    public function updateSEO(array $seoData): void
    {
        $currentSEO = $this->seo_data ?? [];
        $this->update(['seo_data' => array_merge($currentSEO, $seoData)]);
    }
}