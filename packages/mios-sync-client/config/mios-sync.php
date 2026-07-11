<?php

/*
|--------------------------------------------------------------------------
| MIOS sync outbox (legacy-app side)
|--------------------------------------------------------------------------
|
| Each entry under `observe` registers the universal outbox observer on a
| model: every save / soft-delete / restore / force-delete drops one row into
| the local mios_sync_outbox table — resource slug + primary key + op. That
| is ALL this package does. MIOS pulls the outbox over SSH, reads the current
| row state itself, and owns every mapping rule; nothing is pushed from here.
|
| Resource slugs MUST match config/sync_ingest.php in the MIOS repo
| (docs/SYNC_INGEST.md is the contract).
|
| Writes that bypass Eloquent model events (DB::table()->update(), bulk
| upserts, pivot attach/detach) are invisible to observers — call
| Mtwd\MiosSyncClient\Outbox::record(...) next to those writes instead.
|
*/

return [

    'enabled' => env('MIOS_SYNC_OUTBOX', true),

    'table' => 'mios_sync_outbox',

    // Per-app model => resource-slug map. Examples (uncomment per app):
    'observe' => [
        // MEP:  \App\Models\SmsReport::class => 'sms_report',
        // MCP:  \App\Models\User::class => 'customer',
        //       \App\Models\Account::class => 'account',
        //       \App\Models\Statement::class => 'statement',
        //       \App\Models\Payment::class => 'payment',
        //       \App\Models\UserPayment::class => 'user_payment',
        // MCIS: \App\Models\UpdateForm::class => 'update_form',
        // MOCA: \App\Models\Career::class => 'career',
        //       \App\Models\Applicant::class => 'applicant',
        //       \App\Models\InternalApplicant::class => 'internal_applicant',
        //       \App\Models\MyApplicationForm::class => 'my_application_form',
        // PFIS: \App\Models\WaterSystem::class => 'water_system',
        //       \App\Models\ProductionFacility::class => 'production_facility',
        //       \App\Models\Booster::class => 'booster',
        //       \App\Models\Reservoir::class => 'reservoir',
        //       \App\Models\ProductionWell::class => 'production_well',
        //       \App\Models\BoosterDatum::class => 'booster_data',
        //       \App\Models\ReservoirDatum::class => 'reservoir_data',
        //       \App\Models\WellDatum::class => 'well_data',
        // MOJO (legacy MIOS app):
        //       \App\Models\OnlineJobOrder::class => 'mojo_order',
        //       \App\Models\JoAccomplishment::class => 'jo_accomplishment',
        //       \App\Models\JoDispatch::class => 'jo_dispatch',
    ],
];
