<?php
/**
 * Analytics Dashboard Console Page
 * Professional analytics and reporting interface
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'Analytics Dashboard - Mewayz',
    'description' => 'Comprehensive analytics and performance tracking',
    'keywords' => 'analytics, dashboard, reports, metrics, performance'
];

render('livewire.components.console.analytics.page', $pageData);
?>