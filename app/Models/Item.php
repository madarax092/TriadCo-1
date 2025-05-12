<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'name', 'category_id', 'in_stock', 'price',
    ];

    protected $primaryKey = 'item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static $rules = [
        'name' => 'required|string|max:255|unique:items,name',
        'category_id' => 'required|exists:item_categories,itemctgry_id',
        'price' => 'required|numeric|min:0',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $lastItem = Item::orderBy('item_id', 'desc')->first();
            $lastId = $lastItem ? (int) substr($lastItem->item_id, 2) : 0;
            $item->item_id = 'IT' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            $item->in_stock = 0; // Default to 0 when creating a new item
        });
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id', 'itemctgry_id');
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class, 'item_id', 'item_id');
    }
    
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_item', 'item_id', 'room_id') // Corrected foreign keys
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}