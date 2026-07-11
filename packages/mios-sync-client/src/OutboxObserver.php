<?php

namespace Mtwd\MiosSyncClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Universal outbox observer: every persisted change drops one (resource, source_id,
 * op) row into the local outbox. Payloads are deliberately NOT captured — MIOS pulls
 * the current row state itself, so this stays version-proof across Laravel 9-12 apps.
 *
 * Soft deletes are ordinary upserts (the row still exists, deleted_at tells MIOS to
 * tombstone); only a hard delete emits op=delete.
 */
class OutboxObserver
{
    public function __construct(private readonly string $resource) {}

    public function saved(Model $model): void
    {
        Outbox::record($this->resource, (string) $model->getKey(), 'upsert', $model->getConnectionName());
    }

    public function deleted(Model $model): void
    {
        $soft = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            && method_exists($model, 'isForceDeleting')
            && ! $model->isForceDeleting();

        Outbox::record($this->resource, (string) $model->getKey(), $soft ? 'upsert' : 'delete', $model->getConnectionName());
    }

    public function restored(Model $model): void
    {
        Outbox::record($this->resource, (string) $model->getKey(), 'upsert', $model->getConnectionName());
    }

    public function forceDeleted(Model $model): void
    {
        Outbox::record($this->resource, (string) $model->getKey(), 'delete', $model->getConnectionName());
    }
}
