<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',

    'middleware' => ['web'],

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'lowercase_usernames' => true,

    'home' => '/dashboard',

    'prefix' => '',

    'domain' => null,

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        // Features::emailVerification(), // enable once mail is configured
        // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
    ],

];
