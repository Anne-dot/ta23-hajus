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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Emotion name
            $table->text('description'); // How this emotion feels
            $table->string('emoji')->nullable(); // Emoji representation
            $table->integer('intensity')->default(5); // 1-10 scale
            $table->string('color')->nullable(); // Associated color
            $table->string('category')->nullable(); // happy, sad, angry, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
