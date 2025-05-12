<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $table = 'stock_out'; 

    protected $fillable = ['item_id', 'quantity']; 

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}