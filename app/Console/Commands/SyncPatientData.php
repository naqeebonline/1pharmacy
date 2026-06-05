<?php

namespace App\Console\Commands;

use App\Models\Patient\Patient;
use Illuminate\Console\Command;
use App\Models\YourModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPatientData extends Command
{
    protected $signature = 'sync:patient-data';
    protected $description = 'Continuously sync data every minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        while (true) {
            try {
                $apiUrl = 'http://sehatcard.amch.org.pk/api/v1/updatePatient';
                Patient::chunk(20, function ($patients) use ($apiUrl) {
                    $response = Http::withHeaders([
                        'Accept' => 'application/json',
                    ])->post($apiUrl, [
                        'items' => $patients->toJson(),
                    ]);

                    // Handle the response as needed
                    if ($response->successful()) {
                        echo $response->body();
                    } else {
                        echo "Error Occured";
                    }
                });
            } catch (\Exception $e) {
                Log::error('Data sync failed: ' . $e->getMessage());
            }


        }
    }
}
