<?php

return [
    'exchange_rates' => [
        'base_uri' => env('EXCHANGE_RATES_API_BASE_URI', 'https://api.exchangeratesapi.io'),
        'api_key'  => env('EXCHANGE_RATES_API_KEY', ''),
    ],
    'commission'     => [
        'base_currency'     => env('BASE_CURRENCY', 'EUR'),
        'currencies'        => [
            'EUR',
            'USD',
            'JPY',
        ],
        'currency_decimals' => [
            'EUR' => 2,
            'USD' => 2,
            'JPY' => 0,
        ],
        'rules'             => [
            'withdraw' => [
                'private'  => [
                    'commission'             => 0.3,
                    'weekly_free_amount'     => 1000.00,
                    'weekly_free_operations' => 3,
                ],
                'business' => [
                    'commission' => 0.5,
                ],
            ],
            'deposit'  => [
                'commission' => 0.03,
            ],
        ],
    ],
];
