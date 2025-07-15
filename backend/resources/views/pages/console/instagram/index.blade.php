<?php
/**
 * Instagram Management Console Page
 * Professional interface for Instagram marketing automation
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'Instagram Management - Mewayz',
    'description' => 'Comprehensive Instagram marketing automation and analytics platform',
    'keywords' => 'instagram, marketing, automation, analytics, social media'
];

render('livewire.components.console.instagram.page', $pageData);
?>