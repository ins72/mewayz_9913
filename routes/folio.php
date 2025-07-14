<?php

use Laravel\Folio\Folio;
use Illuminate\Support\Facades\Route;

Folio::path(resource_path('views/pages/courses'))->uri('/courses')->middleware([
    '*' => [
        'web'
    ],
]);
Folio::path(resource_path('views/pages/products'))->uri('/products')->middleware([
    '*' => [
        'web'
    ],
]);
Folio::path(resource_path('views/pages/booking'))->uri('/booking')->middleware([
    '*' => [
        'web'
    ],
]);
Folio::path(resource_path('views/pages/s'))->uri('/s')->middleware([
    '*' => [
        'web'
    ],
]);
Folio::path(resource_path('views/pages/invoice'))->uri('/invoice')->middleware([
    '*' => [
        'web'
    ],
]);
