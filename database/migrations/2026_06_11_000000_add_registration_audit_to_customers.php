<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'registered_at')) {
                $table->timestamp('registered_at')->nullable()->after('id');
            }

            if (! Schema::hasColumn('customers', 'registered_by')) {
                $table->foreignId('registered_by')->nullable()->after('registered_at')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('customers', 'registered_at')) {
            DB::table('customers')
                ->whereNull('registered_at')
                ->update(['registered_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (Schema::hasColumn('customers', 'registered_by')) {
                $table->dropConstrainedForeignId('registered_by');
            }

            if (Schema::hasColumn('customers', 'registered_at')) {
                $table->dropColumn('registered_at');
            }
        });
    }
};
