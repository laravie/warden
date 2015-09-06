<?php

return [

    /*
    |----------------------------------------------------------------------
    | E-mail Configuration
    |----------------------------------------------------------------------
    |
    | Set E-mail template to be used for sending notification.
    |
    */

   'email' => 'laravie/warden::email.notification',

    /*
    |----------------------------------------------------------------------
    | Authentication Model
    |----------------------------------------------------------------------
    |
    | We need to know which eloquent model act as User model for the
    | application. You can set this to null and it will fallback to
    | "auth.model" configuration.
    |
    */

    'model' => null,

    /*
    |----------------------------------------------------------------------
    | Watchlist Attributes
    |----------------------------------------------------------------------
    |
    | Define users attribute that should be put as watchlist.
    |
    */

    'watchlist' => ['email'],

    /*
    |----------------------------------------------------------------------
    | Expiry Day Length
    |----------------------------------------------------------------------
    |
    | Define expiry (in days) before disabling user from reverting changes.
    |
    */

    'expire' => 30,
];
