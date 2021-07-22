<?php


return [
    'MAIL' => [
        'REGISTER_MAIL'    => 'アカウント招待',
        'RE_REGISTER_PASSWORD'    => 'パスワード再登録',
    ],
    'LINK' => [
        'REGISTER_LINK'    => config('app.url').'/register/',
        'RE_REGISTER_PASSWORD_LINK'    => config('app.url').'/password-issuance/',
    ],
    'ROUTE_NUM' => 5,
];
