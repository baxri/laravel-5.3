<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Train differenceTime
    |--------------------------------------------------------------------------
    |
    | Hide trains wich leaves after less than 130 min.
    | Value is in minutes
    |
    */

    'differencetime' => 130,

    /*
    |--------------------------------------------------------------------------
    | Offset Userd in get free trains method
    |--------------------------------------------------------------------------
    |
    | Add 4 hour to datetime wich comes from railway server
    |
    */

    'offset' => 0,

    /*
    |--------------------------------------------------------------------------
    | Timeout parameter for waiting responses (Guzzle library)
    |--------------------------------------------------------------------------
    |
    */

    'guzzle_timeout' => 15.0,

    /*
    |--------------------------------------------------------------------------
    | Field to sort vagons array ( sort by amount)
    |--------------------------------------------------------------------------
    |
    */

    'sort_vagons_field' => 'amount',

    /*
    |--------------------------------------------------------------------------
    | Ordering for sorting vagons array ( sort by amount asc )
    |--------------------------------------------------------------------------
    |
    */

    'sort_vagons_order' => SORT_ASC,

    /*
    |--------------------------------------------------------------------------
    | Ticket lifetime in minutes (10 min)
    |--------------------------------------------------------------------------
    |
    */

    'ticket_action_lifetime' => 15,

    /*
   |--------------------------------------------------------------------------
   | Mail send from hello@app.com
   |--------------------------------------------------------------------------
   |
   */

    'email_from' => 'noreply@matarebeli.ge',

    /*
    |--------------------------------------------------------------------------
    | Checkout Return Urls Automatic adds /{transaction_id}
    |--------------------------------------------------------------------------
    |
    */

    'checkout_success' => 'http://matarebeli.ge/ticket-check?mid=',
    'checkout_cancel' => 'http://www.matarebeli.ge/?cancel=1&order=',

    /*
    |--------------------------------------------------------------------------
    | Comissions for Checkout Page
    |--------------------------------------------------------------------------
    |
    */

    'commission_type' => 'fixed', /* values - none, fixed, percentage */
    'commission' => 0.50, /* Amount (GEL) or percent  */
    'minimal_commission' => 0.25,/* Amount (GEL) */

    /*
    |--------------------------------------------------------------------------
    | Timeout After Second Mark ( 7 min )
    |--------------------------------------------------------------------------
    |
    */
    'second_mark_timeout' => 7, /* Set Minutes */


    /*
    |--------------------------------------------------------------------------
    | Dashboard Ip Activity Count
    |--------------------------------------------------------------------------
    |
    */
    'last_ip_activity_count' => 10,

    /*
    |--------------------------------------------------------------------------
    | Max passengers count for transaction
    |--------------------------------------------------------------------------
    |
    */
    'max_passengers_per_transaction' => 8,

    /*
    |--------------------------------------------------------------------------
    | Ticket Pdf Location
    |--------------------------------------------------------------------------
    |
    */

    'pdf_location' => base_path().'/storage/pdf/',

    /*
    |--------------------------------------------------------------------------
    | Ticket Pdf Location
    |--------------------------------------------------------------------------
    |
    */

    'task_transaction_clear' => base_path().'/storage/cronjobs/',

];
