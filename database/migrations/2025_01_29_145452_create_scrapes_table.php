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
        Schema::create('scrapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('scrape_type_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('url', 2048);
            $table->text('prompt');
            $table->string('strategy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapes');
    }
};
