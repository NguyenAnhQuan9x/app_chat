<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('messages',function(Blueprint $table){
            $table->id();
            $table->text('content');
            $table->foreignId('conversation_id')->references('id')->on('conversations');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('parent_id')->references('id')->on('messages');
            $table->boolean('status')->default(true);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('messages');
    }
};
