<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'name', 'roomtype_id', 'status',
    ];

    protected $primaryKey = 'room_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static $rules = [
        'name' => 'required|string|max:255|unique:rooms,name',
        'roomtype_id' => 'required|exists:room_types,roomtype_id',
        'status' => 'in:empty,occupied',
    ];

    protected static function booted()
    {
        static::creating(function ($room) {
            $lastRoom = Room::orderBy('room_id', 'desc')->first();
            $lastId = $lastRoom ? (int) substr($lastRoom->room_id, 1) : 0;
            $room->room_id = 'R' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            $room->status = 'empty'; // Default status is "empty"
        });
    }

    public function type()
    {
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'roomtype_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'room_item', 'room_id', 'item_id') // Corrected foreign keys
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}