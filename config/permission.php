<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Roles
    |--------------------------------------------------------------------------
    |
    | Roles are stored directly on the MongoDB users collection.
    | No SQL permission tables are required.
    |
    */

    'roles' => [
        'super_admin',
        'state_admin',
        'district_officer',
        'inspection_officer',
        'allottee',
    ],

];