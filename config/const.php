<?php


return [
    'MAIL' => [
        'REGISTER_MAIL'    => 'アカウント招待',
        'REGISTER_COMPANY_ADMIN_MAIL'    => 'アカウント作成依頼',
        'RE_REGISTER_PASSWORD'    => 'パスワード再登録',
    ],
    'LINK' => [
        'REGISTER_LINK'    => config('app.url').'/register/',
        'REGISTER_COMPANY_ADMIN_LINK'    => config('app.url').'/register-admin/',
        'RE_REGISTER_PASSWORD_LINK'    => config('app.url').'/password-issuance/',
    ],
    'ROUTE_NUM' => 5,
];
