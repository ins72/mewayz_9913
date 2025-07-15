<?php
/**
 * Email Marketing Console Page
 * Professional email campaign management interface
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'Email Marketing - Mewayz',
    'description' => 'Advanced email marketing campaigns and automation',
    'keywords' => 'email, marketing, campaigns, automation, newsletters'
];

render('livewire.components.console.email.page', $pageData);
?>