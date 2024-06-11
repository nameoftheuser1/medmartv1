<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'supplier_id',
        'batch_number',
        'expiration_date',
        'supplier_price',
        'received_date',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'received_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'batch_id');
    }
}
