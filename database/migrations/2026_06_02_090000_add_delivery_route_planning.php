<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_vehicles', function (Blueprint $table): void {
            if (! Schema::hasColumn('delivery_vehicles', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::create('delivery_routes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_vehicle_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->string('name', 100);
            $table->string('area', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['delivery_vehicle_id', 'day_of_week', 'is_active']);
        });

        Schema::table('delivery_runs', function (Blueprint $table): void {
            if (! Schema::hasColumn('delivery_runs', 'delivery_route_id')) {
                $table->foreignId('delivery_route_id')->nullable()->after('delivery_vehicle_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_runs', function (Blueprint $table): void {
            if (Schema::hasColumn('delivery_runs', 'delivery_route_id')) {
                $table->dropConstrainedForeignId('delivery_route_id');
            }
        });

        Schema::dropIfExists('delivery_routes');

        Schema::table('delivery_vehicles', function (Blueprint $table): void {
            if (Schema::hasColumn('delivery_vehicles', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
