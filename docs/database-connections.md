# External Database Connections

This application connects to three external databases in addition to its own primary database. None of these are managed by Laravel migrations — schema changes must be coordinated with the responsible team for each system.

---

## Primary Database

| Key | Value |
|-----|-------|
| Config key | `DB_CONNECTION` (default) |
| Purpose | All application-owned data: users, job orders, divisions, barangays, cities, roles, activity logs |
| Migrations | Yes — fully managed under `database/migrations/` |
| Models | `User`, `OnlineJobOrder`, `JobOrderCode`, `Division`, `Barangay`, `City`, `JoDispatch`, `JoAccomplishment` |
| Access | Read/Write |

---

## kitdb

| Key | Value |
|-----|-------|
| Config key | `DB_KITDB_*` in `.env` / `config/database.php` connection `kitdb` |
| Purpose | Billing system — account master list, statements, payments, city/barangay reference data |
| Migrations | **None** — schema is owned by the billing system team |
| Models | `Account`, `Statement`, `Payment`, `UserPayment` |
| Raw queries | `DB::connection('kitdb')->table('accounts')` — account lookup in MOJO form; `DB::connection('kitdb')->table('cities')` — town dropdown; `DB::connection('kitdb')->table('barangays')` — map widget |
| Access | Read/Write (`Statement::updateOrCreate` during import; `Account` read-only) |
| FK note | `SET FOREIGN_KEY_CHECKS=0` is required during `StatementsImport` because Statement records reference FK columns that may not exist in the import dataset. This is handled at import-level via `BeforeImport`/`AfterImport` events — **not per-row**. |
| Responsible team | Billing / IT-Systems |

---

## mepdb

| Key | Value |
|-----|-------|
| Config key | `DB_MEPDB_*` in `.env` / `config/database.php` connection `mepdb` |
| Purpose | SMS reporting — stores outbound SMS queue and delivery status |
| Migrations | **None** — schema is owned by the MEP system |
| Models | `SmsReport` |
| Access | Write (records created during `StatementsImport` for customers with a valid mobile number) |
| Responsible team | MEP / Communications team |

---

## mcisdb

| Key | Value |
|-----|-------|
| Config key | `DB_MCISDB_*` in `.env` / `config/database.php` connection `mcisdb` |
| Purpose | MCIS system — update forms / service requests from customer-facing portal |
| Migrations | **None** — schema is owned by the MCIS system |
| Models | `UpdateForm` |
| Access | Read |
| Responsible team | MCIS / Customer Service team |

---

## Health & Availability

These connections are **not health-checked** at application boot. If any external database is unavailable:

- `kitdb` down → MOJO account-number lookup returns `null` (caught by `try/catch` in `afterStateUpdated`); `StatementsImport` will fail at the `BeforeImport` event.
- `mepdb` down → `SmsReport::create()` throws during import; covered by `SkipsOnFailure` in `StatementsImport`.
- `mcisdb` down → Any page loading `UpdateForm` records will throw a connection exception.

Consider adding a database health check route or a Laravel Pulse / Telescope monitor for these connections.
