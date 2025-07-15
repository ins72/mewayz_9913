<?php
/**
 * Social Media Management Console Page
 * Professional social media management interface
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'Social Media Management - Mewayz',
    'description' => 'Comprehensive social media management and publishing',
    'keywords' => 'social media, management, publishing, scheduling, analytics'
];

render('livewire.components.console.social.page', $pageData);
?>