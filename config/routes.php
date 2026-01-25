<?php

return [
    '/' => 'site/index',
    '/show/<id:\d+>' => 'site/show',

    'items' => 'item/index',
    'categories' => 'category/index',
    'users' => 'user/index',

    'admin' => 'admin/index',
    'about' => 'site/about',
    'contact' => 'site/contact',

    'item/<action:\w+>/<id:\d+>' => 'item/<action>',
    'item/<action:\w+>' => 'item/<action>',
    'category/<action:\w+>/<id:\d+>' => 'category/<action>',
    'category/<action:\w+>' => 'category/<action>',
    'user/<action:\w+>/<id:\d+>' => 'user/<action>',
    'user/<action:\w+>' => 'user/<action>', 

];
