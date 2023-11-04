<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User meta fields
    |--------------------------------------------------------------------------
    |
    | This file is for storing allowed user meta field.
    |
    */
    /* All available role resources */
    'resources' => [
        'space' => [
            "settings" => [
                "read",
                "write",
                "delete",
            ],
            "users" => [
                "view",
                "manage"
            ]
        ],
    ],
    /* Predefined role resources */
    'roles' => [
        'admin' => [
            'space' => [
                "settings" => [
                    "read",
                    "write",
                    "delete",
                ],
                "users" => [
                    "view",
                    "manage"
                ]
            ]
        ],
        'manager' => [
            'space' => [
                "settings" => [
                    "read",
                    "write",
                ],
                "users" => [
                    "view",
                ]
            ]
        ],
    ],
];
