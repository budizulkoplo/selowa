<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('is_active');
            }
        });

        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'is_member')) {
                $table->boolean('is_member')->default(false)->after('customer_price');
            }

            if (! Schema::hasColumn('customers', 'discount_type')) {
                $table->string('discount_type', 20)->default('none')->after('is_member');
            }

            if (! Schema::hasColumn('customers', 'discount_value')) {
                $table->unsignedInteger('discount_value')->default(0)->after('discount_type');
            }
        });

        Schema::create('delivery_vehicles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('plate_number', 30)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('run_date');
            $table->string('driver_name', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table): void {
            if (! Schema::hasColumn('transactions', 'delivery_run_id')) {
                $table->foreignId('delivery_run_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (Schema::hasColumn('transactions', 'delivery_run_id')) {
                $table->dropConstrainedForeignId('delivery_run_id');
            }
        });

        Schema::dropIfExists('delivery_runs');
        Schema::dropIfExists('delivery_vehicles');

        Schema::table('customers', function (Blueprint $table): void {
            foreach (['discount_value', 'discount_type', 'is_member'] as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }
};
