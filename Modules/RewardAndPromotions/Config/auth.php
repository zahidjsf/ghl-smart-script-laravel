<?php

return [

    'guards' => [
        'location' => [
            'driver' => 'session',
            'provider' => 'location',
        ],
    ],

    'providers' => [
        'location' => [
            'driver' => 'eloquent',
            'model' => Modules\RewardAndPromotions\Entities\Location::class, // Adjust path as needed
        ],
    ],
];
