<?php

namespace App\Http\Controllers;

use App\Models\Patient\PatientInvestigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class DataSyncController extends Controller
{
    public function syncLiveDataFromLocal()
    {

        foreach (request()->all() as $data) {
            PatientInvestigation::updateOrCreate(
                ['id' => $data->id],
                $data
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Investigations stored with original IDs.',
        ]);


    }

    public function sendDataToLive()
    {
        $data = PatientInvestigation::skip(0)->take(500)->get()->toArray();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post('https://sehatcard.amch.org.pk/api/v1/syncLiveDataFromLocal', [
            'all_data' => $data, // ✅ wrap in a named key
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'message' => 'Data synced to live successfully',
                'response' => $response->json(),
            ]);
        } else {
           // Log::error('Sync Failed: ' . $response->body());

            return response()->json([
                'status' => false,
                'message' => 'Sync failed',
                'error' => $response->body(),
            ], $response->status());
        }
    }
}
