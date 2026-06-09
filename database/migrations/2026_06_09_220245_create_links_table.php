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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 128)->unique();
            $table->text('destination_url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_listed')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_listed', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
