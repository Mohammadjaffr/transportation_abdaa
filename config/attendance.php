<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Time Locks
    |--------------------------------------------------------------------------
    |
    | Defines the hard cutoff times for when drivers can no longer mark 
    | attendance via the mobile application.
    | Format must be H:i (24-hour time).
    |
    */
    'locks' => [
        'morning' => '16:00',
        'leave'   => '23:00',
    ],
];
