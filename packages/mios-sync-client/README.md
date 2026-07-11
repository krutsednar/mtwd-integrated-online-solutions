# mtwd/mios-sync-client

Transactional **sync outbox** for the MTWD legacy apps. It records *which rows
changed* — `(resource, source_id, op)` — into a local `mios_sync_outbox` table.
That is all. MIOS pulls the outbox over an SSH tunnel, reads the current row state
straight from the database, and applies every mapping rule on its own side
(see `docs/SYNC_INGEST.md` in the MIOS repo).

Design consequences:

- **No HTTP, no tokens, no secrets** stored in the legacy apps.
- **No payload serialization** — works identically on Laravel 9, 10, 11, 12
  (PHP ≥ 8.0.2), regardless of the app's Filament/Livewire versions.
- **Never blocks production writes** — the manual `Outbox` helper swallows and
  reports failures; observers only fire after the row is already persisted.

## Install (per legacy app)

1. Add the package (VCS repo or path) and require it:

   ```json
   "repositories": [{"type": "vcs", "url": "git@github.com:MTWD-Repo/mios-sync-client.git"}],
   "require": {"mtwd/mios-sync-client": "*"}
   ```

2. Run `php artisan migrate` (creates `mios_sync_outbox` in the app's own DB).

3. Publish + fill the config with THIS app's models:

   ```
   php artisan vendor:publish --tag=mios-sync-config
   ```

   ```php
   // config/mios-sync.php — e.g. for MEP
   'observe' => [
       \App\Models\SmsReport::class => 'sms_report',
   ],
   ```

   Resource slugs must match the MIOS registry (`config/sync_ingest.php`).

4. Deploy. Every Eloquent save / soft-delete / restore / force-delete now leaves an
   outbox row; MIOS drains them within a minute of the pull being enabled.

## Per-app scope (locked 2026-07-11)

| App | Models → resources |
|---|---|
| MEP | SmsReport → `sms_report` (plus its kitdb-bound Statement/Account models → `statement` / `account`, since MEP's Excel import writes MCP's DB) |
| MCP | User → `customer`, Account → `account`, Statement → `statement`, Payment → `payment`, UserPayment → `user_payment` |
| MCIS | UpdateForm → `update_form` |
| MOCA | Career → `career`, Applicant → `applicant`, InternalApplicant → `internal_applicant`, MyApplicationForm → `my_application_form` |
| PFIS | WaterSystem, ProductionFacility, Booster, Reservoir, ProductionWell, BoosterDatum, ReservoirDatum, WellDatum → matching slugs |
| legacy MIOS (MOJO) | OnlineJobOrder → `mojo_order`, JoAccomplishment → `jo_accomplishment`, JoDispatch → `jo_dispatch` |

## Writes observers cannot see — use `Outbox::record()`

- **MCP account↔user pivot** (`account_user` has no model events). In the
  link/unlink controller actions:

  ```php
  use Mtwd\MiosSyncClient\Outbox;

  Outbox::record('account_customer', $user->id.':'.$account->id);            // attach
  Outbox::record('account_customer', $user->id.':'.$account->id, 'delete');  // detach
  ```

- **MEP `SmsBlast`** updates statuses via `DB::table('sms_reports')->update(...)`
  — either convert those two updates to Eloquent saves (preferred) or add
  `Outbox::record('sms_report', (string) $account->id)` beside them.

- `StatementsImport` needs nothing: it persists row-by-row through Eloquent.

## Outbox hygiene

The table is append-only and MIOS keeps its own cursor. Once the pull is live,
prune periodically (e.g. weekly): `DELETE FROM mios_sync_outbox WHERE created_at < NOW() - INTERVAL 30 DAY;`
