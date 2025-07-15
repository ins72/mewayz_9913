<?php

return [

    /*
     * The model which handles the plans tables.
     */

    'models' => [

        'plan' => \App\Models\Plan::class,
        'subscription' => \App\Models\PlansSubscription::class,
        'feature' => \App\Models\PlansFeature::class,
        'usage' => \App\Models\PlansUsage::class,

        //'stripeCustomer' => \Iprop\Plans\Models\StripeCustomerModel::class,

    ],

];