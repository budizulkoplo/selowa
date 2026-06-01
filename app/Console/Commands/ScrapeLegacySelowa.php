<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Customer;
use App\Models\District;
use App\Models\Transaction;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ScrapeLegacySelowa extends Command
{
    protected $signature = 'selowa:scrape-legacy
        {--base=https://selowa.aytech.id : Base URL aplikasi lama}
        {--email= : Email login aplikasi lama}
        {--password= : Password login aplikasi lama}
        {--fresh : Kosongkan customer, alamat, dan transaksi sebelum import}
        {--skip-download : Pakai file HTML yang sudah ada di storage/app/legacy-scrape}';

    protected $description = 'Scrape customer and transaction data from the legacy Selowa web app.';

    private string $workDir;

    private array $monthMap = [
        'januari' => 1,
        'februari' => 2,
        'maret' => 3,
        'april' => 4,
        'mei' => 5,
        'juni' => 6,
        'juli' => 7,
        'agustus' => 8,
        'september' => 9,
        'oktober' => 10,
        'november' => 11,
        'desember' => 12,
    ];

    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $this->workDir = storage_path('app/legacy-scrape');

        if (! is_dir($this->workDir)) {
            mkdir($this->workDir, 0775, true);
        }

        if ($this->option('fresh')) {
            $this->freshBusinessData();
        }

        if (! $this->option('skip-download')) {
            $email = (string) ($this->option('email') ?: env('LEGACY_SELOWA_EMAIL'));
            $password = (string) ($this->option('password') ?: env('LEGACY_SELOWA_PASSWORD'));

            if ($email === '') {
                $email = (string) $this->ask('Email legacy');
            }

            if ($password === '') {
                $password = (string) $this->secret('Password legacy');
            }

            $this->downloadLegacyPages((string) $this->option('base'), $email, $password);
        }

        $customerResult = $this->importCustomers($this->workDir.DIRECTORY_SEPARATOR.'customer.html');
        $transactionResult = $this->importTransactions($this->workDir.DIRECTORY_SEPARATOR.'transaction.html');

        $this->newLine();
        $this->info('Scrape legacy selesai.');
        $this->line('Customer dibuat: '.$customerResult['created']);
        $this->line('Customer diperbarui: '.$customerResult['updated']);
        $this->line('Transaksi dibuat: '.$transactionResult['created']);
        $this->line('Transaksi diperbarui: '.$transactionResult['updated']);
        $this->line('Transaksi tanpa customer unik: '.$transactionResult['unmatched']);

        return self::SUCCESS;
    }

    private function freshBusinessData(): void
    {
        $this->warn('Mengosongkan transaksi, pelanggan, dan alamat lama di database baru...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['transactions', 'customers', 'villages', 'districts', 'cities'] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function downloadLegacyPages(string $baseUrl, string $email, string $password): void
    {
        $baseUrl = rtrim($baseUrl, '/');
        $cookieFile = $this->workDir.DIRECTORY_SEPARATOR.'cookies.txt';

        $this->info('Login ke aplikasi lama...');
        $loginHtml = $this->request($baseUrl.'/login', $cookieFile);

        if (! preg_match('/name="csrf_test_name"\s+value="([^"]+)"/i', $loginHtml, $match)) {
            throw new RuntimeException('CSRF token aplikasi lama tidak ditemukan.');
        }

        $this->request($baseUrl.'/login', $cookieFile, [
            'csrf_test_name' => $match[1],
            'login' => $email,
            'password' => $password,
        ]);

        $this->info('Download halaman customer...');
        $this->requestToFile($baseUrl.'/customer', $cookieFile, $this->workDir.DIRECTORY_SEPARATOR.'customer.html');

        $this->info('Download halaman transaksi. Ini bisa agak lama karena data lama besar...');
        $this->requestToFile($baseUrl.'/transaction', $cookieFile, $this->workDir.DIRECTORY_SEPARATOR.'transaction.html');
    }

    private function request(string $url, string $cookieFile, ?array $post = null): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_USERAGENT => 'SelowaMigration/1.0',
        ]);

        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($body === false || $status >= 400) {
            throw new RuntimeException('Request gagal: '.$url.' status '.$status.' '.$error);
        }

        return (string) $body;
    }

    private function requestToFile(string $url, string $cookieFile, string $target): void
    {
        $handle = fopen($target, 'wb');

        if (! $handle) {
            throw new RuntimeException('Tidak bisa membuat file '.$target);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FILE => $handle,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_USERAGENT => 'SelowaMigration/1.0',
        ]);

        $ok = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        fclose($handle);

        if ($ok === false || $status >= 400) {
            throw new RuntimeException('Download gagal: '.$url.' status '.$status.' '.$error);
        }
    }

    private function importCustomers(string $file): array
    {
        $this->info('Import customer...');

        $result = ['created' => 0, 'updated' => 0];
        $html = $this->readFile($file);
        $bar = $this->output->createProgressBar(substr_count($html, '<tr>'));
        $offset = 0;

        while (preg_match('/<tr\b[^>]*>(.*?)<\/tr>/is', $html, $rowMatch, PREG_OFFSET_CAPTURE, $offset)) {
            $rowHtml = $rowMatch[1][0];
            $offset = $rowMatch[0][1] + strlen($rowMatch[0][0]);
            $bar->advance();

            if (! preg_match('/customer\/edit\/(\d+)/i', $rowHtml, $idMatch)) {
                continue;
            }

            $cells = $this->tableCells($rowHtml);

            if (count($cells) < 12) {
                continue;
            }

            $customerId = (int) $idMatch[1];
            $villageId = $this->resolveVillage($cells[5], $cells[6], $cells[7]);
            [$rw, $rt] = $this->splitRwRt($cells[8]);

            $exists = Customer::whereKey($customerId)->exists();

            Customer::unguarded(fn () => Customer::updateOrCreate(['id' => $customerId], [
                'name' => $cells[1],
                'timetable' => $cells[2],
                'customer_price' => $this->moneyToInt($cells[3]),
                'phone' => $cells[4] ?: null,
                'village_id' => $villageId,
                'rw' => $rw,
                'rt' => $rt,
                'is_active' => true,
                'is_member' => false,
                'discount_type' => 'none',
                'discount_value' => 0,
                'created_at' => $this->parseIndonesianDate($cells[9]) ?: now(),
                'updated_at' => $this->parseIndonesianDate($cells[10]) ?: now(),
            ]));

            $exists ? $result['updated']++ : $result['created']++;
        }

        $bar->finish();
        $this->newLine();

        return $result;
    }

    private function importTransactions(string $file): array
    {
        $this->info('Import transaksi...');

        $result = ['created' => 0, 'updated' => 0, 'unmatched' => 0];
        $customerMap = $this->customerNameMap();
        $customerPriceMap = $this->customerNamePriceMap();
        $html = $this->readFile($file);
        $bar = $this->output->createProgressBar(substr_count($html, '<tr>'));
        $offset = 0;

        while (preg_match('/<tr\b[^>]*>(.*?)<\/tr>/is', $html, $rowMatch, PREG_OFFSET_CAPTURE, $offset)) {
            $rowHtml = $rowMatch[1][0];
            $offset = $rowMatch[0][1] + strlen($rowMatch[0][0]);
            $bar->advance();
            $cells = $this->tableCells($rowHtml);

            if (count($cells) < 8 || ! str_starts_with(strtoupper($cells[2]), 'TRX')) {
                continue;
            }

            $code = $cells[2];
            $customerKey = $this->nameKey($cells[1]);
            $price = $this->moneyToInt($cells[4]);
            $customerId = $customerMap[$customerKey] ?? $customerPriceMap[$customerKey.'|'.$price] ?? null;
            $existing = Transaction::where('transaction_code', $code)->first();

            if (! $customerId && $existing?->customer_id) {
                $customerId = $existing->customer_id;
            }

            if (! $customerId) {
                $result['unmatched']++;
            }

            Transaction::unguarded(fn () => Transaction::updateOrCreate(['transaction_code' => $code], [
                'customer_id' => $customerId,
                'customer_name_snapshot' => $cells[1] ?: null,
                'delivery_run_id' => $existing?->delivery_run_id,
                'qty' => $this->qtyToInt($cells[3]),
                'price' => $price,
                'status' => 1,
                'created_at' => $this->parseIndonesianDate($cells[6]) ?: $existing?->created_at ?: now(),
                'updated_at' => $this->parseIndonesianDate($cells[6]) ?: $existing?->updated_at ?: now(),
            ]));

            $existing ? $result['updated']++ : $result['created']++;
        }

        $bar->finish();
        $this->newLine();

        return $result;
    }

    private function readFile(string $file): string
    {
        if (! is_file($file)) {
            throw new RuntimeException('File scrape tidak ditemukan: '.$file);
        }

        return (string) file_get_contents($file);
    }

    private function tableCells(string $rowHtml): array
    {
        preg_match_all('/<td\b[^>]*>(.*?)<\/td>/is', $rowHtml, $matches);

        return array_map(fn (string $cell) => $this->cleanText($cell), $matches[1]);
    }

    private function cleanText(string $value): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', $value) ?: '';

        return trim($value);
    }

    private function resolveVillage(string $cityName, string $districtName, string $villageName): ?int
    {
        $cityName = $this->titleName($cityName);
        $districtName = $this->titleName($districtName);
        $villageName = $this->titleName($villageName);

        if ($cityName === '' || $districtName === '' || $villageName === '') {
            return null;
        }

        $city = $this->firstOrCreateByName(City::query(), $cityName);
        $district = District::query()
            ->where('city_id', $city->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($districtName)])
            ->first();

        if (! $district) {
            $district = District::create(['city_id' => $city->id, 'name' => $districtName, 'is_active' => true]);
        }

        $village = Village::query()
            ->where('district_id', $district->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($villageName)])
            ->first();

        if (! $village) {
            $village = Village::create(['district_id' => $district->id, 'name' => $villageName, 'is_active' => true]);
        }

        return $village->id;
    }

    private function firstOrCreateByName($query, string $name)
    {
        $model = $query->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($model) {
            return $model;
        }

        return $query->getModel()::create(['name' => $name, 'is_active' => true]);
    }

    private function splitRwRt(string $value): array
    {
        $parts = array_map('trim', explode('/', $value));

        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    private function moneyToInt(string $value): int
    {
        return (int) preg_replace('/\D+/', '', $value);
    }

    private function qtyToInt(string $value): int
    {
        return max(1, (int) preg_replace('/\D+/', '', $value));
    }

    private function parseIndonesianDate(string $value): ?Carbon
    {
        if (! preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\s+\|\s+(\d{1,2}):(\d{2})/u', mb_strtolower($value), $match)) {
            return null;
        }

        $month = $this->monthMap[$match[2]] ?? null;

        if (! $month) {
            return null;
        }

        return Carbon::create((int) $match[3], $month, (int) $match[1], (int) $match[4], (int) $match[5], 0, 'Asia/Jakarta');
    }

    private function customerNameMap(): array
    {
        $grouped = Customer::query()
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->groupBy(fn (Customer $customer) => $this->nameKey($customer->name));

        return $grouped
            ->filter(fn ($rows) => $rows->count() === 1)
            ->map(fn ($rows) => $rows->first()->id)
            ->all();
    }

    private function customerNamePriceMap(): array
    {
        $grouped = Customer::query()
            ->where('is_active', true)
            ->get(['id', 'name', 'customer_price'])
            ->groupBy(fn (Customer $customer) => $this->nameKey($customer->name).'|'.(int) $customer->customer_price);

        return $grouped
            ->filter(fn ($rows) => $rows->count() === 1)
            ->map(fn ($rows) => $rows->first()->id)
            ->all();
    }

    private function nameKey(string $name): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($name)) ?: '');
    }

    private function titleName(string $name): string
    {
        $name = $this->cleanText($name);

        return mb_convert_case(mb_strtolower($name), MB_CASE_TITLE, 'UTF-8');
    }
}
