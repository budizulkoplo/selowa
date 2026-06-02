<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (! Schema::hasColumn('transactions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('delivery_run_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (Schema::hasColumn('transactions', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
