<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Tables exchanged between local and live databases (desktop + syncDataLive).
     */
    protected function syncTableNames(): array
    {
        $tables = [
            'in_patient_admissions',
            'patient_investigations',
            'patients',
            'products',
            'product_kits',
            'main_category',
            'sub_category',
            'item_generic_name',
            'item_make',
            'consultants',
            'patient_investigation_result',
            'patient_investigations_payments',
            'appointments',
            'sale',
            'sale_details',
            'sale_payments',
            'temp_sale',
            'temp_sale_details',
            'grn',
            'grn_details',
            'pharmacy_transfer',
            'pharmacy_transfer_details',
            'pharmacy_return_items',
            'investigation_category',
            'investigation_sub_category',
            'investigation_sub_category_parameters',
            'consultant_procedures',
            'consultant_procedure_pricing',
            'consultant_speciality',
            'consultant_type',
        ];

        if (date('His') >= 210101 && date('His') <= 235959) {
            $tables[] = 'finance_heads';
            $tables[] = 'finance_transactions';
            $tables[] = 'finance_vouchers';
        }

        return $tables;
    }

    protected function isAllowedSyncTable(string $table): bool
    {
        return in_array($table, $this->syncTableNames(), true);
    }

    public function syncTables()
    {
        return response()->json([
            'status' => 'success',
            'tables' => $this->syncTableNames(),
        ]);
    }

    /**
     * Pull rows from live DB → local desktop (GET chunks by id cursor).
     */
    public function pullData(Request $request)
    {
        $table = $request->input('table');
        $afterId = (int) $request->input('after_id', 0);
        $limit = min(max((int) $request->input('limit', 30), 1), 100);

        if (!$table || !$this->isAllowedSyncTable($table)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or disallowed table name',
            ], 400);
        }

        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Table {$table} not found in database",
            ], 400);
        }

        if (!DB::getSchemaBuilder()->hasColumn($table, 'id')) {
            return response()->json([
                'status'  => 'error',
                'message' => "Table {$table} has no id column for sync pull",
            ], 400);
        }

        $rows = DB::table($table)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $maxId = $rows->max('id') ?? $afterId;

        return response()->json([
            'status'   => 'success',
            'table'    => $table,
            'after_id' => $afterId,
            'max_id'   => $maxId,
            'count'    => $rows->count(),
            'data'     => $rows,
        ]);
    }

    public function syncData(Request $request)
    {
        $table = $request->input('table');
        $records = $request->input('data'); // array of 20 records

        if (!$table || !$records || !is_array($records)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid request: table or data missing'
            ], 400);
        }

        if (!$this->isAllowedSyncTable($table)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or disallowed table name',
            ], 400);
        }

        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Table {$table} not found in database"
            ], 400);
        }

        $syncedIds = [];

        foreach ($records as $key => $data) {

            if (!isset($data['id'])) {
                continue; // skip invalid
            }

            DB::table($table)->updateOrInsert(
                ['id' => $data['id']],
                $data
            );

            $syncedIds[] = $data['id'];
        }

        return response()->json([
            'status'     => 'success',
            'table'      => $table,
            'synced_ids' => $syncedIds,
            'count'      => count($syncedIds),
        ]);
    }

    public function load_sync_view()
    {
        return view("reports.sync_view");
    }


public function syncLoclDataWithLive()
{
    $logPath = storage_path('logs');
    // Clear logs (optional, usually not recommended in production)
    foreach (glob($logPath . '/*.log') as $file) {
        file_put_contents($file, '');
    }

    $tables = $this->syncTableNames();

    $summary = [
        'success' => [],
        'errors'  => [],
    ];

    foreach ($tables as $table) {
        try {
            \DB::table($table)
                ->where('is_sync', 0)
                ->orderBy('id')
                ->chunk(30, function ($records) use ($table, &$summary) {

                    if ($records->isEmpty()) {
                        return;
                    }

                    $apiUrl = "https://hospital.awamisawari.com/api/sync";

                    $payload = [
                        'table' => $table,
                        'data'  => $records->map(fn($r) => (array) $r)->toArray(),
                    ];

                    $response = \Http::withOptions([
                        'verify' => false, // ?? important for your localhost SSL issue
                    ])->withHeaders([
                        'Accept' => 'application/json',
                    ])->post($apiUrl, $payload);

                    if ($response->successful()) {
                        $syncedIds = $response->json('synced_ids') ?? [];

                        if (!empty($syncedIds)) {
                            \DB::table($table)
                                ->whereIn('id', $syncedIds)
                                ->update(['is_sync' => 1]);
                        }

                        $summary['success'][] = [
                            'table' => $table,
                            'count' => count($syncedIds),
                        ];
                    } else {
                        $summary['errors'][] = [
                            'table'    => $table,
                            'response' => $response->body(),
                        ];
                    }
                });
        } catch (\Exception $e) {
            $summary['errors'][] = [
                'table' => $table,
                'error' => $e->getMessage(),
            ];
        }
    }

    return response()->json([
        'status'  => empty($summary['errors']),
        'message' => empty($summary['errors'])
            ? 'All tables synced successfully'
            : 'Some tables failed to sync',
        'details' => $summary,
    ]);
}
    public function syncLoclDataWithLiveBackup()
    {
        /*$logPath = storage_path('logs');
        // Delete all log files inside storage/logs
        foreach (glob($logPath.'/*.log') as $file) {
            file_put_contents($file, ''); // Empty file
        }*/
        $tables = [
            "in_patient_admissions",
            "patient_investigations",
            "patients",
            "consultants",
            "patient_investigation_result",
            "appointments",
        ];

        if(date("His") >= 200101){

            $tables =  $tables['finance_heads'];
            $tables =  $tables['finance_transactions'];
            $tables =  $tables['finance_vouchers'];
        }


        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)
                ->where('is_sync', 0) // only unsynced
                ->orderBy('id')
                ->chunk(20, function ($records) use ($table) {
                    $apiUrl = env('LIVE_URL').'api/sync';

                    $payload = [
                        'table' => $table,
                        'data'  => $records->map(fn($r) => (array) $r)->toArray(),
                    ];

                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Accept' => 'application/json',
                    ])->post($apiUrl, $payload);

                    if ($response->successful()) {
                        // response se synced ids nikalo
                        $syncedIds = $response->json('synced_ids') ?? [];

                        if (!empty($syncedIds)) {
                            \Illuminate\Support\Facades\DB::table($table)->whereIn('id', $syncedIds)->update(['is_sync' => 1]);
                        }
                        echo ("{$table}: synced " . count($syncedIds) . " records.<br>");
                    } else {

                        echo ("{$table}: failed syncing chunk <br>");
                        echo ("Response: " . $response->body());

                    }
                });
        }
        return ["status"=>true,"message"=>"There is no data to sync. all records are synced"];

    }
}