<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncToLive extends Command
{
    protected $signature = 'sync:tolive';
    protected $description = 'Send local unsynced data to live DB';

    public function handle()
    {
        $tables = [
            "in_patient_admissions",
            "patient_investigations",
            "patients",
            "patient_investigation_result",
            "appointments",
            /*"sale",
            "sale_details",*/
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->where('is_sync', 0) // only unsynced
                ->orderBy('id')
                ->chunk(20, function ($records) use ($table) {
                    $apiUrl = 'http://sehatcard.amch.org.pk/api/sync';

                    $payload = [
                        'table' => $table,
                        'data'  => $records->map(fn($r) => (array) $r)->toArray(),
                    ];

                    $response = Http::withHeaders([
                        'Accept' => 'application/json',
                    ])->post($apiUrl, $payload);

                    if ($response->successful()) {
                        // response se synced ids nikalo
                        $syncedIds = $response->json('synced_ids') ?? [];

                        if (!empty($syncedIds)) {
                            DB::table($table)->whereIn('id', $syncedIds)->update(['is_sync' => 1]);
                        }

                        $this->info("{$table}: synced " . count($syncedIds) . " records.");
                        sleep(2);
                    } else {
                        $this->error("{$table}: failed syncing chunk");
                        $this->error("Response: " . $response->body());
                        sleep(2);
                    }
                });
        }
    }
}
