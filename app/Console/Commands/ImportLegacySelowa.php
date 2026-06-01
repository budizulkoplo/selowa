<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\District;
use App\Models\Gallon;
use App\Models\Transaction;
use App\Models\Village;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacySelowa extends Command
{
    protected $signature = 'selowa:import-legacy {database=selowa_legacy}';

    protected $description = 'Import selected Selowa legacy tables into the new Laravel schema.';

    public function handle(): int
    {
        $database = (string) $this->argument('database');

        config(['database.connections.legacy' => array_merge(config('database.connections.mysql'), [
            'database' => $database,
        ])]);

        DB::purge('legacy');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['transactions', 'gallons', 'customers', 'villages', 'districts', 'cities', 'companies'] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->copyCompanies();
        $this->copySimple('cities', City::class);
        $this->copySimple('districts', District::class, ['city_id']);
        $this->copySimple('villages', Village::class, ['district_id']);
        $this->copyCustomers();
        $this->copyGallons();
        $this->copyTransactions();

        $this->info('Legacy import selesai.');

        return self::SUCCESS;
    }

    private function legacy(string $table)
    {
        return DB::connection('legacy')->table($table);
    }

    private function copyCompanies(): void
    {
        foreach ($this->legacy('company')->get() as $row) {
            Company::unguarded(fn () => Company::create([
                'id' => $row->id,
                'name' => $row->name,
                'phone' => $row->phone,
                'email' => $row->email,
                'address' => $row->address,
                'embed_map' => $row->embed_map,
                'image' => $row->image,
                'created_at' => $row->updated_at ?: now(),
                'updated_at' => $row->updated_at ?: now(),
            ]));
        }
    }

    private function copySimple(string $table, string $model, array $extra = []): void
    {
        foreach ($this->legacy($table)->get() as $row) {
            $data = [
                'id' => $row->id,
                'name' => $row->name,
                'is_active' => (int) $row->active === 1,
                'created_at' => $row->created_at ?: now(),
                'updated_at' => $row->updated_at ?: now(),
            ];

        foreach ($extra as $column) {
                if ($column === 'city_id' && ! City::whereKey($row->{$column})->exists()) {
                    continue 2;
                }

                if ($column === 'district_id' && ! District::whereKey($row->{$column})->exists()) {
                    continue 2;
                }

                $data[$column] = $row->{$column};
            }

            $model::unguarded(fn () => $model::create($data));
        }
    }

    private function copyCustomers(): void
    {
        foreach ($this->legacy('customer')->get() as $row) {
            Customer::unguarded(fn () => Customer::create([
                'id' => $row->id,
                'name' => $row->customer_name,
                'phone' => $row->phone,
                'village_id' => Village::whereKey($row->village_id)->exists() ? $row->village_id : null,
                'rw' => $row->rw,
                'rt' => $row->rt,
                'timetable' => $row->timetable,
                'customer_price' => (int) $row->customer_price,
                'is_active' => (int) $row->active === 1,
                'created_at' => $row->created_at ?: now(),
                'updated_at' => $row->updated_at ?: now(),
            ]));
        }
    }

    private function copyGallons(): void
    {
        foreach ($this->legacy('gallon')->get() as $row) {
            Gallon::unguarded(fn () => Gallon::create([
                'id' => $row->id,
                'name' => $row->name,
                'stock' => (int) $row->stock,
                'is_active' => (int) $row->active === 1,
                'created_at' => $row->created_at ?: now(),
                'updated_at' => $row->updated_at ?: now(),
            ]));
        }
    }

    private function copyTransactions(): void
    {
        foreach ($this->legacy('hd_transaction')->get() as $row) {
            $code = $row->transaction_code;

            if (Transaction::where('transaction_code', $code)->exists()) {
                $code .= '-'.$row->id;
            }

            Transaction::unguarded(fn () => Transaction::create([
                'id' => $row->id,
                'transaction_code' => $code,
                'customer_id' => Customer::whereKey($row->customer_id)->exists() ? $row->customer_id : null,
                'customer_name_snapshot' => Customer::whereKey($row->customer_id)->value('name'),
                'qty' => (int) $row->qty,
                'price' => (int) $row->price,
                'status' => (int) $row->status,
                'created_at' => $row->created_at ?: now(),
                'updated_at' => $row->updated_at ?: now(),
            ]));
        }
    }
}
