<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_item', function (Blueprint $table) {
            $table->string('room_id'); // Foreign key for rooms
            $table->string('item_id'); // Foreign key for items (string type to match items table)
            $table->integer('quantity'); // Quantity of items assigned to the room
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');

            // Composite primary key
            $table->primary(['room_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_item');
    }
};