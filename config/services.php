<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'prisma' => [
        'merchant_id' => env('PRISMALINK_MERCHANT_ID'),
        'key_id'      => env('PRISMALINK_KEY_ID'),
        'secret_key'  => env('PRISMALINK_SECRET_KEY'),
        'api_url'     => env('PRISMA_API_URL', 'https://api-staging.plink.co.id/gateway/v2'),
        'backend_callback'  => env('PLINK_BACKEND_CALLBACK'),
        'frontend_callback' => env('PLINK_FRONTEND_CALLBACK'),
    ],

];
