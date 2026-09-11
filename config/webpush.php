<?php

return [
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@smattendance.com'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],
    'settings' => [
        'default_ttl' => 86400, // 24 hours
        'urgency' => 'normal',  // very-low, low, normal, high
    ],
];
