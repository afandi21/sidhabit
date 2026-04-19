<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webauthn_credentials', function (Blueprint $table) {
            if (!Schema::hasColumn('webauthn_credentials', 'authenticatable_type')) {
                $table->string('authenticatable_type')->default('App\\Models\\User')->after('id');
            }
            if (!Schema::hasColumn('webauthn_credentials', 'authenticatable_id')) {
                // Rename user_id to authenticatable_id is risky due to constraints, so we add a new column
                // Or if user_id exists, we can use it, but laragear expects authenticatable_id
                $table->unsignedBigInteger('authenticatable_id')->nullable()->after('authenticatable_type');
            }
        });

        // Copy data from user_id to authenticatable_id if user_id exists
        if (Schema::hasColumn('webauthn_credentials', 'user_id')) {
            \DB::statement('UPDATE webauthn_credentials SET authenticatable_id = user_id WHERE user_id IS NOT NULL');
            
            // Drop foreign key and column
            Schema::table('webauthn_credentials', function (Blueprint $table) {
                // Ignore error if foreign key doesn't exist
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {}
                
                $table->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        // ...
    }
};
