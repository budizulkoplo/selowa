<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (! Schema::hasColumn('transactions', 'customer_name_snapshot')) {
                $table->string('customer_name_snapshot', 100)->nullable()->after('customer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (Schema::hasColumn('transactions', 'customer_name_snapshot')) {
                $table->dropColumn('customer_name_snapshot');
            }
        });
    }
};
