<?php

return [

    /*
     * Determines if the Xray package should be enabled.
     */
    'enabled' => env('XRAY_ENABLED', false),

    /*
     * If you want to exclude certain views from being processed by Xray,
     * you can list them here. Be aware that the check only applies to the
     * root views that you add here. If these views include other views
     * themselves, they need to be excluded manually.
     */
    'excluded' => [
        //
    ],

];
