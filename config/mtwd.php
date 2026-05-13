<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supervisor Division IDs
    |--------------------------------------------------------------------------
    | Users whose division_id is in this list can see all job orders across
    | all divisions (supervisor/admin-level access in the MOJO panel).
    */
    'supervisor_division_ids' => [2022, 2023, 7, 2, 3, 4, 5],

    /*
    |--------------------------------------------------------------------------
    | Account Lookup Cache TTL (minutes)
    |--------------------------------------------------------------------------
    | How long to cache cross-database account lookups from kitdb.
    */
    'account_lookup_cache_ttl' => 10,

    /*
    |--------------------------------------------------------------------------
    | Executive Widget Division Codes
    |--------------------------------------------------------------------------
    | Division codes shown in the JobOrderPerDivision and related widgets on
    | the Executive dashboard. Change here to add or remove divisions.
    */
    'widget_division_codes' => ['16', '2016', '2017', '2018', '2021', '2024', '2020'],

    /*
    |--------------------------------------------------------------------------
    | Excluded Category Codes
    |--------------------------------------------------------------------------
    | Category codes excluded from the JoPerCategory report footer totals
    | and table rows. Add codes here to suppress internal/admin categories.
    */
    'excluded_category_codes' => ['2023', '2022'],
];
