<?php

// status_id: 1 — открыт, 2 — закрыт, 3 — приостановлен
return [
    // владелец = user 1, не опубликован, открыт
    'draft' => [
        'id' => 1,
        'name' => 'Draft item',
        'description' => 'desc',
        'created_at' => '2024-01-01 00:00:00',
        'status_id' => 1,
        'category_id' => null,
        'created_user_id' => 1,
        'sum_need' => 1000.00,
        'sum_current' => 0.00,
        'currency' => 'KZT',
        'published' => 0,
    ],
    // владелец = user 1, опубликован, сбор идёт
    'published' => [
        'id' => 2,
        'name' => 'Published item',
        'description' => 'desc',
        'created_at' => '2024-01-01 00:00:00',
        'status_id' => 1,
        'category_id' => null,
        'created_user_id' => 1,
        'sum_need' => 1000.00,
        'sum_current' => 0.00,
        'currency' => 'KZT',
        'published' => 1,
    ],
    // владелец = user 2 (чужой), не опубликован
    'foreign' => [
        'id' => 3,
        'name' => 'Foreign item',
        'description' => 'desc',
        'created_at' => '2024-01-01 00:00:00',
        'status_id' => 1,
        'category_id' => null,
        'created_user_id' => 2,
        'sum_need' => 1000.00,
        'sum_current' => 0.00,
        'currency' => 'KZT',
        'published' => 0,
    ],
];
