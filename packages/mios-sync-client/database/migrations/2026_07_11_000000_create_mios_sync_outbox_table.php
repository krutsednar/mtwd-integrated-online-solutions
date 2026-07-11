<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The app-local sync outbox MIOS pulls over SSH. Append-only; MIOS keeps its own
 * cursor, so no sync state lives here beyond the events themselves. Prune old rows
 * with a scheduled `... where id <= <cursor>` delete once the pull is live, or by age.
 */
return new class extends Migration
{
    public function up(): void
    {
        $table = (string) config('mios-sync.table', 'mios_sync_outbox');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $t) {
            $t->id();
            $t->string('resource', 64)->index();
            $t->string('source_id', 191);
            $t->string('op', 8)->default('upsert');
            $t->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists((string) config('mios-sync.table', 'mios_sync_outbox'));
    }
};
