<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnedItem extends Model
{
    protected $fillable = [
        'item_id', 
        'quantity', 
        'reason',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}