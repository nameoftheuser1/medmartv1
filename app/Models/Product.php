<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'generic_name',
        'category',
        'product_description',
        'price',
        'old_price'
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if ($product->isDirty('price')) {
                $product->old_price = $product->getOriginal('price');
            }
        });
    }

    public function productBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'product_id');
    }

    public function salesDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'product_id');
    }
}
