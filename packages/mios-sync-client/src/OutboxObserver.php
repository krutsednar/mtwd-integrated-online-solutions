<?php

namespace Mtwd\MiosSyncClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Universal outbox observer: every persisted change drops one (resource, source_id,
 * op) row into the local outbox. Payloads are deliberately NOT captured — MIOS pulls
 * the current row state itself, so this stays version-proof across Laravel 9-12 apps.
 *
 * MUST stay constructor-less: Eloquent registers observers by CLASS NAME and
 * re-resolves them from the container when events fire, silently discarding any
 * instance state — a constructor parameter here 500s every observed save. The
 * resource slug is therefore looked up from config per event; a model missing from
 * the map is a silent no-op, never an error.
 *
 * Soft deletes are ordinary upserts (the row still exists, deleted_at tells MIOS to
 * tombstone); only a hard delete emits op=delete.
 */
class OutboxObserver
{
    public function saved(Model $model): void
    {
        $this->write($model, 'upsert');
    }

    public function deleted(Model $model): void
    {
        $soft = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            && method_exists($model, 'isForceDeleting')
            && ! $model->isForceDeleting();

        $this->write($model, $soft ? 'upsert' : 'delete');
    }

    public function restored(Model $model): void
    {
        $this->write($model, 'upsert');
    }

    public function forceDeleted(Model $model): void
    {
        $this->write($model, 'delete');
    }

    private function write(Model $model, string $op): void
    {
        $resource = $this->resourceFor($model);

        if ($resource === null) {
            return;
        }

        Outbox::record($resource, (string) $model->getKey(), $op, $model->getConnectionName());
    }

    private function resourceFor(Model $model): ?string
    {
        $entry = ((array) config('mios-sync.observe'))[get_class($model)] ?? null;

        if (is_array($entry)) {
            return $entry['resource'] ?? null;
        }

        return is_string($entry) && $entry !== '' ? $entry : null;
    }
}
