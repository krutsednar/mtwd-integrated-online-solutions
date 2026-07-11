<?php

namespace Mtwd\MiosSyncClient;

use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Manual outbox writer — for the write paths Eloquent observers cannot see:
 * pivot attach/detach, DB::table()->update(), bulk upserts.
 *
 *     Outbox::record('account_customer', $userId.':'.$accountId);           // link
 *     Outbox::record('account_customer', $userId.':'.$accountId, 'delete'); // unlink
 *     Outbox::record('sms_report', (string) $id);                           // after DB::table update
 *
 * NEVER throws: losing one outbox row is recoverable (reconciliation), breaking a
 * live production write is not. Failures are reported and swallowed.
 */
class Outbox
{
    public static function record(string $resource, string $sourceId, string $op = 'upsert', ?string $connection = null): void
    {
        try {
            DB::connection($connection)
                ->table((string) config('mios-sync.table', 'mios_sync_outbox'))
                ->insert([
                    'resource' => $resource,
                    'source_id' => $sourceId,
                    'op' => $op,
                    'created_at' => now(),
                ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
