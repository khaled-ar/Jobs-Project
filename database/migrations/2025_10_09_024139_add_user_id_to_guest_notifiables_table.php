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
        Schema::table('guest_notifiables', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_notifiables', function (Blueprint $table) {
            $table->dropForeign('guest_notifiables_users_user_id');
            $table->dropColumn('user_id');
        });
    }
};
