<?php

/*
|--------------------------------------------------------------------------
| MIOS sync outbox — legacy MIOS / MOJO scope (locked 2026-07-11)
|--------------------------------------------------------------------------
|
| Job orders + accomplishments + dispatches only — users/roles/audit logs are
| deliberately NOT synced (MCT owns employee identity). MIOS pulls the outbox
| over SSH. Slugs must match config/sync_ingest.php in the new MIOS repo.
|
*/

return [

    'enabled' => env('MIOS_SYNC_OUTBOX', true),

    'table' => 'mios_sync_outbox',

    'observe' => [
        \App\Models\OnlineJobOrder::class => 'mojo_order',
        \App\Models\JoAccomplishment::class => 'jo_accomplishment',
        \App\Models\JoDispatch::class => 'jo_dispatch',
    ],
];
