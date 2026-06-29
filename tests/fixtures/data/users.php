<?php

return [
    'owner' => [
        'id' => 1,
        'name' => 'Owner',
        'email' => 'owner@test.kz',
        'role_id' => 2,
        'inactive' => 0,
        'created_at' => '2024-01-01 00:00:00',
        'password' => 'not-used-in-tests',
        'auth_key' => 'owner-auth-key',
        'corporate' => 0,
    ],
    'stranger' => [
        'id' => 2,
        'name' => 'Stranger',
        'email' => 'stranger@test.kz',
        'role_id' => 2,
        'inactive' => 0,
        'created_at' => '2024-01-01 00:00:00',
        'password' => 'not-used-in-tests',
        'auth_key' => 'stranger-auth-key',
        'corporate' => 0,
    ],
];
