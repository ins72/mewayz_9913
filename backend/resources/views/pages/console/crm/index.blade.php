<?php
/**
 * CRM Management Console Page
 * Professional Customer Relationship Management interface
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'CRM Management - Mewayz',
    'description' => 'Comprehensive customer relationship management and lead tracking',
    'keywords' => 'crm, customers, leads, sales, management'
];

render('livewire.components.console.crm.page', $pageData);
?>