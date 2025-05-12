<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('stock_out', function (Blueprint $table) {
            $table->string('item_id'); 
            $table->integer('quantity'); 
            $table->timestamps(); 

            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out');
    }
};