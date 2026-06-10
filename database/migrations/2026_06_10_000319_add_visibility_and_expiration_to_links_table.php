<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('visibility', 16)->default('featured')->after('is_listed');
            $table->timestamp('expires_at')->nullable()->after('last_clicked_at');

            $table->index(
                ['visibility', 'is_active', 'expires_at', 'sort_order'],
                'links_visibility_active_expiry_sort_index',
            );
        });

        DB::table('links')
            ->where('is_listed', false)
            ->update(['visibility' => 'hidden']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropIndex('links_visibility_active_expiry_sort_index');
            $table->dropColumn(['visibility', 'expires_at']);
        });
    }
};
