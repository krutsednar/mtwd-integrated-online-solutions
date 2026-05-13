<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\OnlineHrisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOnlineAttendance implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 300;

    public function handle(): void
    {
        /** @var OnlineHrisService $service */
        $service = app(OnlineHrisService::class);

        $records = $service->fetchUnsyncedAttendances();

        if ($records->isEmpty()) {
            Log::info('Online HRIS sync: No new attendance records to sync.');
            return;
        }

        $synced = 0;

        DB::transaction(function () use ($records, $service, &$synced) {
            foreach ($records as $record) {
                try {
                    $remoteId = (int) ($record['id'] ?? 0);

                    Attendance::upsert(
                        [
                            [
                                'employee_number' => $record['employee_number'],
                                'attendance_date' => $record['attendance_date'],
                                'remote_id'       => $remoteId ?: null,
                                'morning_in'      => $record['morning_in'] ?? null,
                                'morning_out'     => $record['morning_out'] ?? null,
                                'afternoon_in'    => $record['afternoon_in'] ?? null,
                                'afternoon_out'   => $record['afternoon_out'] ?? null,
                                'ot_in'           => $record['ot_in'] ?? null,
                                'ot_out'          => $record['ot_out'] ?? null,
                                'is_synced'       => 1,
                                'synced_at'       => now(),
                            ],
                        ],
                        uniqueBy: ['employee_number', 'attendance_date'],
                        update: [
                            'remote_id',
                            'morning_in',
                            'morning_out',
                            'afternoon_in',
                            'afternoon_out',
                            'ot_in',
                            'ot_out',
                            'is_synced',
                            'synced_at',
                        ]
                    );

                    if ($remoteId > 0) {
                        $service->markSynced($remoteId);
                    }

                    $synced++;
                } catch (\Throwable $e) {
                    Log::error('Online HRIS sync: failed to process record', [
                        'employee_number' => $record['employee_number'] ?? 'unknown',
                        'attendance_date' => $record['attendance_date'] ?? 'unknown',
                        'error'           => $e->getMessage(),
                    ]);
                }
            }
        });

        Log::info("Online HRIS sync complete: {$synced} records saved and marked synced.");
    }
}
